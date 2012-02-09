<?php
require(sysConfig::getDirFsCatalog() . 'includes/classes/FileParser/csv.php');

if ($_POST['report_type'] == 'day'){
	$dateFromArr = date_parse($_POST['date_from']);
	$dateToArr = date_parse($_POST['date_to']);

	$dateFrom = date('Y-m-d H:i:s', mktime(0, 0, 0, $dateFromArr['month'], $dateFromArr['day'], $dateFromArr['year']));
	$dateTo = date('Y-m-d H:i:s', mktime(23, 59, 59, $dateToArr['month'], $dateToArr['day'], $dateToArr['year']));
}
elseif ($_POST['report_type'] == 'month'){
	$dateFrom = date('Y-m-d H:i:s', mktime(0, 0, 0, $_POST['month_from'], 1, $_POST['year_from']));
	$dateTo = date('Y-m-d H:i:s', mktime(23, 59, 59, $_POST['month_to']+1, 0, $_POST['year_to']));
}
elseif ($_POST['report_type'] == 'year'){
	$dateFrom = date('Y-m-d H:i:s', mktime(0, 0, 0, 1, 1, $_POST['year_from']));
	$dateTo = date('Y-m-d H:i:s', mktime(23, 59, 59, 13, 0, $_POST['year_to']));
}

$Qsales = Doctrine_Query::create()
	->from('Orders o')
	->leftJoin('o.OrdersTotal ot')
	->leftJoin('o.OrdersProducts op')
	->where('o.date_purchased >= ?', $dateFrom)
	->andWhere('o.date_purchased <= ?', $dateTo);

if (isset($_POST['payment_modules'])){
	$Qsales
		->leftJoin('o.OrdersPaymentsHistory ph')
		->andWhereIn('ph.payment_module', $_POST['payment_modules']);
}

if (isset($_POST['report_stores'])){
	$Qsales
		->leftJoin('o.OrdersToStores o2s')
		->andWhereIn('o2s.stores_id', $_POST['report_stores']);
}

if (in_array('late_fee', $_POST['income_columns'])){
	$Qsales->leftJoin('op.LateFees lf');
}

if (
	in_array('store_to_store_income', $_POST['income_columns']) ||
	in_array('store_to_store_expense', $_POST['expense_columns'])
){
	$Qsales->leftJoin('op.StoreToStorePayments s2sp');
}

$Result = $Qsales->execute();
if ($Result){
	$ReportTable = htmlBase::newElement('table')
		->setCellPadding(2)
		->setCellSpacing(0)
		->css('width', '100%');

	$reportData = array();

	$showSales = in_array('sales', $_POST['income_columns']);
	$showLateFees = in_array('late_fees', $_POST['income_columns']);
	$showSalesTax = in_array('sales_tax', $_POST['income_columns']);
	$showStoreToStoreIncome = in_array('store_to_store_income', $_POST['income_columns']);

	$showCoupons = in_array('coupons', $_POST['expense_columns']);
	$showCredits = in_array('credits', $_POST['expense_columns']);
	$showStoreToStoreExpense = in_array('store_to_store_expense', $_POST['expense_columns']);

	$showPaymentModules = (isset($_POST['payment_modules']));

	foreach($Result as $oInfo){
		$pDate = date_parse($oInfo['date_purchased']);
		$pYear = $pDate['year'];
		$pMonth = $pDate['month'];
		$pDay = $pDate['day'];

		if (isset($_POST['report_stores'])){
			$oStore = $oInfo['OrdersToStores']['stores_id'];
			if (in_array($oStore, $_POST['report_stores'])){
				if (!isset($reportData[$oStore])){
					$reportData[$oStore] = array();
				}
				if (!isset($reportData[$oStore][$pYear][$pMonth][$pDay])){
					$reportData[$oStore][$pYear][$pMonth][$pDay] = array();
				}
				$DataBase =& $reportData[$oStore][$pYear][$pMonth][$pDay];
			}else{
				continue;
			}
		}else{
			if (!isset($reportData[$pYear][$pMonth][$pDay])){
				$reportData[$pYear][$pMonth][$pDay] = array();
			}
			$DataBase =& $reportData[$pYear][$pMonth][$pDay];
		}

		if ($showStoreToStoreIncome === true || $showStoreToStoreExpense === true){
			foreach($oInfo['OrdersProducts'] as $opInfo){
				if (!empty($opInfo['StoreToStorePayments'])){
					$PaymentInfo = $opInfo['StoreToStorePayments'];

					if ($showStoreToStoreIncome === true && $PaymentInfo['to_store_id'] == $oStore){
						if (!isset($DataBase['income']['store_to_store'])){
							$DataBase['income']['store_to_store'] = 0;
						}
						$DataBase['income']['store_to_store'] += $PaymentInfo['payment_amount'];
					}

					if ($showStoreToStoreExpense === true && $PaymentInfo['from_store_id'] == $oStore){
						if (!isset($DataBase['expense']['store_to_store'])){
							$DataBase['expense']['store_to_store'] = 0;
						}
						$DataBase['expense']['store_to_store'] += $PaymentInfo['payment_amount'];
					}
				}
			}
		}

		if ($showPaymentModules === true){
			foreach($oInfo['OrdersPaymentsHistory'] as $pInfo){
				if (!isset($DataBase['income']['payments'])){
					$DataBase['income']['payments'] = array();
					$DataBase['expense']['payments'] = array();
				}

				if ($pInfo['payment_amount'] < 0){
					$DataBase['expense']['payments'][$pInfo['payment_method']] += abs($pInfo['payment_amount']);
				}else{
					$DataBase['income']['payments'][$pInfo['payment_method']] += $pInfo['payment_amount'];
				}
			}
		}

		foreach($oInfo['OrdersTotal'] as $otInfo){
			$moduleType = $otInfo['module_type'];
			$otValue = $otInfo['value'];

			if ($moduleType == 'total' && $showSales === true){
				if (!isset($DataBase['income']['sales'])){
					$DataBase['income']['sales'] = 0;
				}
				$DataBase['income']['sales'] += $otValue;
			}
			elseif ($moduleType == 'late_fee' && $showLateFees === true){
				if (!isset($DataBase['income']['late_fees'])){
					$DataBase['income']['late_fees'] = 0;
				}
				$DataBase['income']['late_fees'] += $otValue;
			}
			elseif ($moduleType == 'tax' && $showSalesTax === true){
				if (!isset($DataBase['income']['sales_tax'])){
					$DataBase['income']['sales_tax'] = 0;
				}
				$DataBase['income']['sales_tax'] += $otValue;
			}
			elseif ($moduleType == 'credit' && $showCredits === true){
				if (!isset($DataBase['expense']['credits'])){
					$DataBase['expense']['credits'] = 0;
				}
				$DataBase['expense']['credits'] += $otValue;
			}
			elseif ($moduleType == 'coupon' && $showCoupons === true){
				if (!isset($DataBase['expense']['coupons'])){
					$DataBase['expense']['coupons'] = 0;
				}
				$DataBase['expense']['coupons'] += $otValue;
			}
		}
	}

	if (isset($_GET['csv'])){
		$FileObj = new FileParserCsv(sysConfig::getDirFsCatalog() . 'admin/csv_export/salesReport.csv', 'w+');
		$csvRow = array();
		$csvRow[] = 'date';
		if ($showSales === true){
			$csvRow[] = 'sales';
		}
		if ($showLateFees === true){
			$csvRow[] = 'late_fees';
		}
		if ($showSalesTax === true){
			$csvRow[] = 'sales_tax';
		}
		if ($showCredits === true){
			$csvRow[] = 'credits';
		}
		if ($showCoupons === true){
			$csvRow[] = 'coupons';
		}
		if ($showStoreToStoreIncome === true){
			$csvRow[] = 'owed_from_main';
		}
		if ($showStoreToStoreExpense === true){
			$csvRow[] = 'owed_to_main';
		}
		$FileObj->addRow($csvRow);
	}else{
		$headerCols = array();
		$headerCols[] = array('addCls' => 'ui-widget-header', 'text' => sysLanguage::get('TABLE_HEADING_DATE'));
		if ($showSales === true){
			$headerCols[] = array('align' => 'right', 'addCls' => 'ui-widget-header', 'text' => sysLanguage::get('TABLE_HEADING_SALES'));
		}
		if ($showLateFees === true){
			$headerCols[] = array('align' => 'right', 'addCls' => 'ui-widget-header', 'text' => sysLanguage::get('TABLE_HEADING_LATE_FEES'));
		}
		if ($showSalesTax === true){
			$headerCols[] = array('align' => 'right', 'addCls' => 'ui-widget-header', 'text' => sysLanguage::get('TABLE_HEADING_SALES_TAX'));
		}
		if ($showCredits === true){
			$headerCols[] = array('align' => 'right', 'addCls' => 'ui-widget-header', 'text' => sysLanguage::get('TABLE_HEADING_CREDITS'));
		}
		if ($showCoupons === true){
			$headerCols[] = array('align' => 'right', 'addCls' => 'ui-widget-header', 'text' => sysLanguage::get('TABLE_HEADING_COUPONS'));
		}
		if ($showStoreToStoreIncome === true){
			$headerCols[] = array('align' => 'right', 'addCls' => 'ui-widget-header', 'text' => sysLanguage::get('TABLE_HEADING_STORE_TO_STORE_INCOME'));
		}
		if ($showStoreToStoreExpense === true){
			$headerCols[] = array('align' => 'right', 'addCls' => 'ui-widget-header', 'text' => sysLanguage::get('TABLE_HEADING_STORE_TO_STORE_EXPENSE'));
		}

		$ReportTable->addHeaderRow(array(
				'columns' => $headerCols
			));
	}

	if ($_POST['report_type'] == 'day'){
		function parseReportInfo($rInfo, &$ReportTable){
			global $currencies, $FileObj, $headerCols, $showSales, $showLateFees, $showSalesTax, $showCredits, $showCoupons, $showStoreToStoreIncome, $showStoreToStoreExpense, $showPaymentModules;
			foreach($rInfo as $year => $yInfo){
				foreach($yInfo as $month => $mInfo){
					if (!isset($_GET['csv'])){
						$ReportTable->addBodyRow(array(
								'columns' => array(
									array(
										'addCls' => 'ui-widget-content ui-state-hover',
										'colspan' => sizeof($headerCols),
										'text' => '<b>' . date('F', mktime(0,0,0,$month,1,$year)) . ' ' . $year . '</b>'
									)
								)
							));
					}

					foreach($mInfo as $day => $rInfo){
						if (isset($_GET['csv'])){
							$csvRow = array();
							$csvRow[] = date('m/d/Y', mktime(0,0,0,$month,$day,$year));
							if ($showSales === true){
								$csvRow[] = $currencies->format($rInfo['income']['sales']);
							}
							if ($showLateFees === true){
								$csvRow[] = $currencies->format($rInfo['income']['late_fees']);
							}
							if ($showSalesTax === true){
								$csvRow[] = $currencies->format($rInfo['income']['sales_tax']);
							}
							if ($showCredits === true){
								$csvRow[] = $currencies->format($rInfo['expense']['credits']);
							}
							if ($showCoupons === true){
								$csvRow[] = $currencies->format($rInfo['expense']['coupons']);
							}
							if ($showStoreToStoreIncome === true){
								$csvRow[] = $currencies->format($rInfo['income']['store_to_store']);
							}
							if ($showStoreToStoreExpense === true){
								$csvRow[] = $currencies->format($rInfo['expense']['store_to_store']);
							}

							$FileObj->addRow($csvRow);
						}else{
							$rowCols = array();
							$rowCols[] = array('text' => 'Day: ' . $day);
							if ($showSales === true){
								$rowCols[] = array('align' => 'right', 'text' => $currencies->format($rInfo['income']['sales']));
							}
							if ($showLateFees === true){
								$rowCols[] = array('align' => 'right', 'text' => $currencies->format($rInfo['income']['late_fees']));
							}
							if ($showSalesTax === true){
								$rowCols[] = array('align' => 'right', 'text' => $currencies->format($rInfo['income']['sales_tax']));
							}
							if ($showCredits === true){
								$rowCols[] = array('align' => 'right', 'text' => $currencies->format($rInfo['expense']['credits']));
							}
							if ($showCoupons === true){
								$rowCols[] = array('align' => 'right', 'text' => $currencies->format($rInfo['expense']['coupons']));
							}
							if ($showStoreToStoreIncome === true){
								$rowCols[] = array('align' => 'right', 'text' => $currencies->format($rInfo['income']['store_to_store']));
							}
							if ($showStoreToStoreExpense === true){
								$rowCols[] = array('align' => 'right', 'text' => $currencies->format($rInfo['expense']['store_to_store']));
							}

							$ReportTable->addBodyRow(array(
									'columns' => $rowCols
								));

							if ($showPaymentModules === true){
								foreach($rInfo['income']['payments'] as $Method => $total){
									$rowCols = array();
									$rowCols[] = array('text' => 'Payment Method: ' . $Method . ' - Income');
									if ($showSales === true){
										$rowCols[] = array('align' => 'right', 'text' => $currencies->format($total));
									}

									$ReportTable->addBodyRow(array(
											'columns' => $rowCols
										));
								}

								foreach($rInfo['expense']['payments'] as $Method => $total){
									$rowCols = array();
									$rowCols[] = array('text' => 'Payment Method: ' . $Method . ' - Expense');
									if ($showSales === true){
										$rowCols[] = array('align' => 'right', 'text' => $currencies->format($total));
									}

									$ReportTable->addBodyRow(array(
											'columns' => $rowCols
										));
								}
							}
						}
					}
				}
			}
		}
	}
	elseif ($_POST['report_type'] == 'month'){
		function parseReportInfo($rInfo, &$ReportTable){
			global $currencies, $FileObj, $headerCols, $showSales, $showLateFees, $showSalesTax, $showCredits, $showCoupons, $showStoreToStoreIncome, $showStoreToStoreExpense, $showPaymentModules;
			foreach($rInfo as $year => $yInfo){
				foreach($yInfo as $month => $mInfo){
					if ($showSales === true){
						$totalSales = 0;
					}
					if ($showLateFees === true){
						$totalLateFees = 0;
					}
					if ($showSalesTax === true){
						$totalTax = 0;
					}
					if ($showStoreToStoreIncome === true){
						$totalStoreToStoreIncome = 0;
					}
					if ($showCredits === true){
						$totalCredits = 0;
					}
					if ($showCoupons === true){
						$totalCoupons = 0;
					}
					if ($showStoreToStoreExpense === true){
						$totalStoreToStoreExpense = 0;
					}
					foreach($mInfo as $day => $rInfo){
						if ($showSales === true){
							$totalSales += $rInfo['income']['sales'];
						}
						if ($showRentals === true){
							$totalRentals += $rInfo['income']['rentals'];
						}
						if ($showLateFees === true){
							$totalLateFees += $rInfo['income']['late_fees'];
						}
						if ($showSalesTax === true){
							$totalTax += $rInfo['income']['sales_tax'];
						}
						if ($showStoreToStoreIncome === true){
							$totalStoreToStoreIncome += $rInfo['income']['store_to_store'];
						}
						if ($showCredits === true){
							$totalCredits += $rInfo['expense']['credits'];
						}
						if ($showCoupons === true){
							$totalCoupons += $rInfo['expense']['coupons'];
						}
						if ($showStoreToStoreExpense === true){
							$totalStoreToStoreExpense += $rInfo['expense']['store_to_store'];
						}
					}
					if (isset($_GET['csv'])){
						$csvRow = array();
						$csvRow[] = date('F', mktime(0,0,0,$month,1,$year)) . ' ' . $year;
						if ($showSales === true){
							$csvRow[] = $currencies->format($totalSales);
						}
						if ($showLateFees === true){
							$csvRow[] = $currencies->format($totalLateFees);
						}
						if ($showSalesTax === true){
							$csvRow[] = $currencies->format($totalTax);
						}
						if ($showCredits === true){
							$csvRow[] = $currencies->format($totalCredits);
						}
						if ($showCoupons === true){
							$csvRow[] = $currencies->format($totalCoupons);
						}
						if ($showStoreToStoreIncome === true){
							$csvRow[] = $currencies->format($totalStoreToStoreIncome);
						}
						if ($showStoreToStoreExpense === true){
							$csvRow[] = $currencies->format($totalStoreToStoreExpense);
						}

						$FileObj->addRow($csvRow);
					}else{
						$rowCols = array();
						$rowCols[] = array('text' => '<b>' . date('F', mktime(0,0,0,$month,1,$year)) . ' ' . $year . '</b>');
						if ($showSales === true){
							$rowCols[] = array('align' => 'right', 'text' => $currencies->format($totalSales));
						}
						if ($showLateFees === true){
							$rowCols[] = array('align' => 'right', 'text' => $currencies->format($totalLateFees));
						}
						if ($showSalesTax === true){
							$rowCols[] = array('align' => 'right', 'text' => $currencies->format($totalTax));
						}
						if ($showCredits === true){
							$rowCols[] = array('align' => 'right', 'text' => $currencies->format($totalCredits));
						}
						if ($showCoupons === true){
							$rowCols[] = array('align' => 'right', 'text' => $currencies->format($totalCoupons));
						}
						if ($showStoreToStoreIncome === true){
							$rowCols[] = array('align' => 'right', 'text' => $currencies->format($totalStoreToStoreIncome));
						}
						if ($showStoreToStoreExpense === true){
							$rowCols[] = array('align' => 'right', 'text' => $currencies->format($totalStoreToStoreExpense));
						}

						$ReportTable->addBodyRow(array(
								'columns' => $rowCols
							));

						if ($showPaymentModules === true){
							$PaymentsIncomeTotal = array();
							$PaymentsExpenseTotal = array();
							foreach($mInfo as $day => $rInfo){
								foreach($rInfo['income']['payments'] as $Method => $total){
									$PaymentsIncomeTotal[$Method] += $total;
								}

								foreach($rInfo['expense']['payments'] as $Method => $total){
									$PaymentsExpenseTotal[$Method] += $total;
								}
							}

							foreach($PaymentsIncomeTotal as $Method => $total){
								$rowCols = array();
								$rowCols[] = array('text' => 'Payment Method: ' . $Method . ' - Income');
								if ($showSales === true){
									$rowCols[] = array('align' => 'right', 'text' => $currencies->format($total));
								}

								$ReportTable->addBodyRow(array(
										'columns' => $rowCols
									));
							}

							foreach($PaymentsExpenseTotal as $Method => $total){
								$rowCols = array();
								$rowCols[] = array('text' => 'Payment Method: ' . $Method . ' - Expense');
								if ($showSales === true){
									$rowCols[] = array('align' => 'right', 'text' => $currencies->format($total));
								}

								$ReportTable->addBodyRow(array(
										'columns' => $rowCols
									));
							}
						}
					}
				}
			}
		}
	}
	elseif ($_POST['report_type'] == 'year'){
		function parseReportInfo($rInfo, &$ReportTable){
			global $currencies, $FileObj, $headerCols, $showSales, $showLateFees, $showSalesTax, $showCredits, $showCoupons, $showStoreToStoreIncome, $showStoreToStoreExpense, $showPaymentModules;
			foreach($rInfo as $year => $yInfo){
				if ($showSales === true){
					$totalSales = 0;
				}
				if ($showLateFees === true){
					$totalLateFees = 0;
				}
				if ($showSalesTax === true){
					$totalTax = 0;
				}
				if ($showStoreToStoreIncome === true){
					$totalStoreToStoreIncome = 0;
				}
				if ($showCredits === true){
					$totalCredits = 0;
				}
				if ($showCoupons === true){
					$totalCoupons = 0;
				}
				if ($showStoreToStoreExpense === true){
					$totalStoreToStoreExpense = 0;
				}
				foreach($yInfo as $month => $mInfo){
					foreach($mInfo as $day => $rInfo){
						if ($showSales === true){
							$totalSales += $rInfo['income']['sales'];
						}
						if ($showLateFees === true){
							$totalLateFees += $rInfo['income']['late_fees'];
						}
						if ($showSalesTax === true){
							$totalTax += $rInfo['income']['sales_tax'];
						}
						if ($showStoreToStoreIncome === true){
							$totalStoreToStoreIncome += $rInfo['income']['store_to_store'];
						}
						if ($showCredits === true){
							$totalCredits += $rInfo['expense']['credits'];
						}
						if ($showCoupons === true){
							$totalCoupons += $rInfo['expense']['coupons'];
						}
						if ($showStoreToStoreExpense === true){
							$totalStoreToStoreExpense += $rInfo['expense']['store_to_store'];
						}
					}
				}
				if (isset($_GET['csv'])){
					$csvRow = array();
					$csvRow[] = $year;
					if ($showSales === true){
						$csvRow[] = $currencies->format($totalSales);
					}
					if ($showLateFees === true){
						$csvRow[] = $currencies->format($totalLateFees);
					}
					if ($showSalesTax === true){
						$csvRow[] = $currencies->format($totalTax);
					}
					if ($showCredits === true){
						$csvRow[] = $currencies->format($totalCredits);
					}
					if ($showCoupons === true){
						$csvRow[] = $currencies->format($totalCoupons);
					}
					if ($showStoreToStoreIncome === true){
						$csvRow[] = $currencies->format($totalStoreToStoreIncome);
					}
					if ($showStoreToStoreExpense === true){
						$csvRow[] = $currencies->format($totalStoreToStoreExpense);
					}

					$FileObj->addRow($csvRow);
				}else{
					$rowCols = array();
					$rowCols[] = array('text' => '<b>' . $year . '</b>');
					if ($showSales === true){
						$rowCols[] = array('align' => 'right', 'text' => $currencies->format($totalSales));
					}
					if ($showLateFees === true){
						$rowCols[] = array('align' => 'right', 'text' => $currencies->format($totalLateFees));
					}
					if ($showSalesTax === true){
						$rowCols[] = array('align' => 'right', 'text' => $currencies->format($totalTax));
					}
					if ($showCredits === true){
						$rowCols[] = array('align' => 'right', 'text' => $currencies->format($totalCredits));
					}
					if ($showCoupons === true){
						$rowCols[] = array('align' => 'right', 'text' => $currencies->format($totalCoupons));
					}
					if ($showStoreToStoreIncome === true){
						$rowCols[] = array('align' => 'right', 'text' => $currencies->format($totalStoreToStoreIncome));
					}
					if ($showStoreToStoreExpense === true){
						$rowCols[] = array('align' => 'right', 'text' => $currencies->format($totalStoreToStoreExpense));
					}

					$ReportTable->addBodyRow(array(
							'columns' => $rowCols
						));

					if ($showPaymentModules === true){
						$PaymentsIncomeTotal = array();
						$PaymentsExpenseTotal = array();
						foreach($yInfo as $month => $mInfo){
							foreach($mInfo as $day => $rInfo){
								foreach($rInfo['income']['payments'] as $Method => $total){
									$PaymentsIncomeTotal[$Method] += $total;
								}

								foreach($rInfo['expense']['payments'] as $Method => $total){
									$PaymentsExpenseTotal[$Method] += $total;
								}
							}
						}

						foreach($PaymentsIncomeTotal as $Method => $total){
							$rowCols = array();
							$rowCols[] = array('text' => 'Payment Method: ' . $Method . ' - Income');
							if ($showSales === true){
								$rowCols[] = array('align' => 'right', 'text' => $currencies->format($total));
							}

							$ReportTable->addBodyRow(array(
									'columns' => $rowCols
								));
						}

						foreach($PaymentsExpenseTotal as $Method => $total){
							$rowCols = array();
							$rowCols[] = array('text' => 'Payment Method: ' . $Method . ' - Expense');
							if ($showSales === true){
								$rowCols[] = array('align' => 'right', 'text' => $currencies->format($total));
							}

							$ReportTable->addBodyRow(array(
									'columns' => $rowCols
								));
						}
					}
				}
			}
		}
	}

	if (isset($_POST['report_stores'])){
		$MultiStore = $appExtension->getExtension('multiStore');
		foreach($reportData as $storeId => $sInfo){
			$storeInfo = $MultiStore->getStoresArray($storeId);
			$ReportTable->addBodyRow(array(
					'columns' => array(
						array(
							'addCls' => 'ui-widget-header',
							'colspan' => sizeof($headerCols),
							'text' => '<b>' . $storeInfo['stores_name'] . '</b>'
						)
					)
				));

			parseReportInfo($sInfo, $ReportTable);
		}
	}else{
		parseReportInfo($reportData, $ReportTable);
	}
}

if (isset($_GET['csv'])){
	EventManager::attachActionResponse(array(
			'success' => true,
			'redirectTo' => itw_app_link('action=downloadReport&report=salesReport', 'statistics', 'salesReport')
		), 'json');
}else{
	EventManager::attachActionResponse(array(
			'success' => true,
			'html' => $ReportTable->draw()
		), 'json');
}
