<?php

if (isset($_POST['selectedOrder']) && is_array($_POST['selectedOrder'])){
    $dataExport = new dataExport();

	$fields = array();

	if (isset($_POST['v_orders_id'])){
		$fields[] = 'v_orders_id';
	}
	if (isset($_POST['v_orders_customers_name'])){
		$fields[] = 'v_orders_customers_name';
	}
	if (isset($_POST['v_orders_customers_company'])){
		$fields[] = 'v_orders_customers_company';
	}
	if (isset($_POST['v_orders_customers_email_address'])){
		$fields[] = 'v_orders_customers_email_address';
	}
	if (isset($_POST['v_orders_customers_telephone'])){
		$fields[] = 'v_orders_customers_telephone';
	}
	if (isset($_POST['v_orders_billing_name'])){
		$fields[] = 'v_orders_billing_name';
	}
	if (isset($_POST['v_orders_billing_address'])){
		$fields[] = 'v_orders_billing_address';
	}
	if (isset($_POST['v_orders_billing_city'])){
		$fields[] = 'v_orders_billing_city';
	}
	if (isset($_POST['v_orders_billing_state'])){
		$fields[] = 'v_orders_billing_state';
	}
	if (isset($_POST['v_orders_billing_country'])){
		$fields[] = 'v_orders_billing_country';
	}
	if (isset($_POST['v_orders_billing_postcode'])){
		$fields[] = 'v_orders_billing_postcode';
	}
	if (isset($_POST['v_orders_shipping_name'])){
		$fields[] = 'v_orders_shipping_name';
	}
	if (isset($_POST['v_orders_shipping_address'])){
		$fields[] = 'v_orders_shipping_address';
	}
	if (isset($_POST['v_orders_shipping_city'])){
		$fields[] = 'v_orders_shipping_city';
	}
	if (isset($_POST['v_orders_shipping_state'])){
		$fields[] = 'v_orders_shipping_state';
	}
	if (isset($_POST['v_orders_shipping_country'])){
		$fields[] = 'v_orders_shipping_country';
	}
	if (isset($_POST['v_orders_shipping_postcode'])){
		$fields[] = 'v_orders_shipping_postcode';
	}
	if (isset($_POST['v_orders_subtotal'])){
		$fields[] = 'v_orders_subtotal';
	}
	if (isset($_POST['v_orders_total'])){
		$fields[] = 'v_orders_total';
	}
	if (isset($_POST['v_orders_tax'])){
		$fields[] = 'v_orders_tax';
	}
	if (isset($_POST['v_orders_payment_method'])){
		$fields[] = 'v_orders_payment_method';
	}
	if (isset($_POST['v_orders_status'])){
		$fields[] = 'v_orders_status';
	}
	if (isset($_POST['v_orders_shipping'])){
		$fields[] = 'v_orders_shipping';
	}
	if (isset($_POST['v_orders_date_purchased'])){
		$fields[] = 'v_orders_date_purchased';
	}
	if (sizeof($fields) > 0) {
		$dataExport->setHeaders($fields);
	}
		EventManager::notify('OrdersExportQueryFileLayoutHeader', &$dataExport);

		$QHeaders = Doctrine_Query::create()
					->select('count(*) as vmax')
					->from('OrdersProducts op')
					->groupBy('op.orders_id')
					->whereIn('op.orders_id', $_POST['selectedOrder'])
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		if (count($QHeaders)){
			$maxVal = -1;
			foreach($QHeaders as $hInfo){
				if($hInfo['vmax'] > $maxVal){
					$maxVal = $hInfo['vmax'];
				}
			}
		}

		if (count($QHeaders)){
			for($i=1;$i<=$maxVal;$i++){
				$fieldsp = array();
				if (isset($_POST['v_orders_products_name'])){
					$fieldsp[] = 'v_orders_products_name_'.$i;
				}
				if (isset($_POST['v_orders_products_model'])){
					$fieldsp[] = 'v_orders_products_model_'.$i;
				}
				if (isset($_POST['v_orders_products_price'])){
					$fieldsp[] = 'v_orders_products_price_'.$i;
				}
				if (isset($_POST['v_orders_products_tax'])){
					$fieldsp[] = 'v_orders_products_tax_'.$i;
				}
				if (isset($_POST['v_orders_products_finalprice'])){
					$fieldsp[] = 'v_orders_products_finalprice_'.$i;
				}
				if (isset($_POST['v_orders_products_qty'])){
					$fieldsp[] = 'v_orders_products_qty_'.$i;
				}
				if (isset($_POST['v_orders_products_barcode'])){
					$fieldsp[] = 'v_orders_products_barcode_'.$i;
				}
				if (isset($_POST['v_orders_products_purchasetype'])){
					$fieldsp[] = 'v_orders_products_purchasetype_'.$i;
				}
				if (sizeof($fieldsp) > 0){
					$dataExport->setHeaders($fieldsp);
					unset($fieldsp);
				}
				EventManager::notify('OrdersProductsExportQueryFileLayoutHeader', &$dataExport, $i);
			}
		}

		$QfileLayout = Doctrine_Query::create()
		->from('Orders o')
		->leftJoin('o.OrdersProducts op')
		->leftJoin('o.OrdersTotal ot')
		->leftJoin('o.OrdersAddresses a')
		->leftJoin('o.OrdersStatus s')
		->leftJoin('s.OrdersStatusDescription sd')
		->where('sd.language_id = ?', Session::get('languages_id'))
		->orderBy('o.date_purchased desc')
		->whereIn('o.orders_id', $_POST['selectedOrder']);

		EventManager::notify('OrdersExportQueryBeforeExecute', &$QfileLayout);

		$Result = $QfileLayout->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		$dataRows = array();
		foreach($Result as $oInfo){
			if (isset($_POST['v_orders_id'])){
				$pInfo['v_orders_id'] = $oInfo['orders_id'];
			}
			$Qcustomer = Doctrine_Query::create()
						->from('Customers')
						->where('customers_id = ?', $oInfo['customers_id'])
						->fetchOne();
			if (isset($_POST['v_orders_customers_name'])){
				$pInfo['v_orders_customers_name'] = $Qcustomer->customers_firstname . ' ' . $Qcustomer->customers_lastname;
			}
			if (isset($_POST['v_orders_customers_email_address'])){
				$pInfo['v_orders_customers_email_address'] = $Qcustomer->customers_email_address;
			}
			if (isset($_POST['v_orders_customers_telephone'])){
				$pInfo['v_orders_customers_telephone'] = $Qcustomer->customers_telephone;
			}

			foreach($oInfo['OrdersAddresses'] as $oaInfo){
				if($oaInfo['address_type'] == 'billing'){
					if (isset($_POST['v_orders_billing_name'])){
						$pInfo['v_orders_billing_name'] = $oaInfo['entry_name'];
					}
					if (isset($_POST['v_orders_billing_company'])){
						$pInfo['v_orders_billing_company'] = $oaInfo['entry_company'];
					}
					if (isset($_POST['v_orders_billing_address'])){
						$pInfo['v_orders_billing_address'] = $oaInfo['entry_street_address'];
					}
					if (isset($_POST['v_orders_billing_city'])){
						$pInfo['v_orders_billing_city'] = $oaInfo['entry_city'];
					}
					if (isset($_POST['v_orders_billing_state'])){
						$pInfo['v_orders_billing_state'] = $oaInfo['entry_state'];
					}
					if (isset($_POST['v_orders_billing_country'])){
						$pInfo['v_orders_billing_country'] = $oaInfo['entry_country'];
					}
					if (isset($_POST['v_orders_billing_postcode'])){
						$pInfo['v_orders_billing_postcode'] = $oaInfo['entry_postcode'];
					}
				}else if($oaInfo['address_type'] == 'delivery'){
					if (isset($_POST['v_orders_shipping_name'])){
						$pInfo['v_orders_shipping_name'] = $oaInfo['entry_name'];
					}
					if (isset($_POST['v_orders_shipping_company'])){
						$pInfo['v_orders_shipping_company'] = $oaInfo['entry_company'];
					}
					if (isset($_POST['v_orders_shipping_address'])){
						$pInfo['v_orders_shipping_address'] = $oaInfo['entry_street_address'];
					}
					if (isset($_POST['v_orders_shipping_city'])){
						$pInfo['v_orders_shipping_city'] = $oaInfo['entry_city'];
					}
					if (isset($_POST['v_orders_shipping_state'])){
						$pInfo['v_orders_shipping_state'] = $oaInfo['entry_state'];
					}
					if (isset($_POST['v_orders_shipping_country'])){
						$pInfo['v_orders_shipping_country'] = $oaInfo['entry_country'];
					}
					if (isset($_POST['v_orders_shipping_postcode'])){
						$pInfo['v_orders_shipping_postcode'] = $oaInfo['entry_postcode'];
					}
				}
			}
			if (isset($_POST['v_orders_date_purchased'])){
				$pInfo['v_orders_date_purchased'] = $oInfo['date_purchased'];
			}
			foreach($oInfo['OrdersTotal'] as $otInfo){
				if($otInfo['module_type'] == 'ot_subtotal' || $otInfo['module_type'] == 'subtotal'){
					if (isset($_POST['v_orders_subtotal'])){
						$pInfo['v_orders_subtotal'] = $otInfo['value'];
					}
				}else if($otInfo['module_type'] == 'ot_total' || $otInfo['module_type'] == 'total'){
					if (isset($_POST['v_orders_total'])){
						$pInfo['v_orders_total'] = $otInfo['value'];
					}
				}else if($otInfo['module_type'] == 'ot_tax' || $otInfo['module_type'] == 'tax'){
					if (isset($_POST['v_orders_tax'])){
						$pInfo['v_orders_tax'] = $otInfo['value'];
					}
				}else if($otInfo['module_type'] == 'ot_shipping' || $otInfo['module_type'] == 'shipping'){
					if (isset($_POST['v_orders_shipping'])){
						$pInfo['v_orders_shipping'] = $otInfo['value'];
					}
				}
			}
			if (isset($_POST['v_orders_status'])){
				$pInfo['v_orders_status'] = $oInfo['OrdersStatus']['OrdersStatusDescription'][0]['orders_status_name'];
			}
			if (isset($_POST['v_orders_payment_method'])){
				$pInfo['v_orders_payment_method'] = $oInfo['payment_module'];
			}

			$i = 1;
			foreach($oInfo['OrdersProducts'] as $opInfo){
				if (isset($_POST['v_orders_products_name'])){
					$pInfo['v_orders_products_name_'. $i] = $opInfo['products_name'];
				}
				if (isset($_POST['v_orders_products_model'])){
					$pInfo['v_orders_products_model_'. $i] = $opInfo['products_model'];
				}
				if (isset($_POST['v_orders_products_price'])){
					$pInfo['v_orders_products_price_'. $i] = $opInfo['products_price'];
				}
				if (isset($_POST['v_orders_products_tax'])){
					$pInfo['v_orders_products_tax_'. $i] = $opInfo['products_tax'];
				}
				if (isset($_POST['v_orders_products_finalprice'])){
					$pInfo['v_orders_products_finalprice_'. $i] = $opInfo['final_price'];
				}
				if (isset($_POST['v_orders_products_qty'])){
					$pInfo['v_orders_products_qty_'. $i] = $opInfo['products_quantity'];
				}
				if (isset($_POST['v_orders_products_purchasetype'])){
					$pInfo['v_orders_products_purchasetype_'. $i] = $opInfo['purchase_type'];
				}
				if (isset($_POST['v_orders_products_barcode'])){
					if (isset($opInfo['barcode_id']) && !empty($opInfo['barcode_id'])){
						$barcodeTable = Doctrine_Core::getTable('ProductsInventoryBarcodes')->findOneByBarcodeId($opInfo['barcode_id']);
						$pInfo['v_orders_products_barcode_'. $i] = $barcodeTable->barcode;
					}else{
						$pInfo['v_orders_products_barcode_'. $i] = '';
					}
				}
				EventManager::notify('OrderProductsExportBeforeFileLineCommit', &$pInfo, &$opInfo, $i);
				$i++;
			}

			EventManager::notify('OrderExportBeforeFileLineCommit', &$pInfo, &$oInfo);

			$dataRows[] = $pInfo;
		}

		$dataExport->setExportData($dataRows);
		$dataExport->output(true);

}
?>