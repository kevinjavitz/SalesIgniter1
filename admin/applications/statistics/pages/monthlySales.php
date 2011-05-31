<?php
	$print = false;
	if (isset($_GET['print']) && $_GET['print'] == 'yes'){
		$print = true;
	}

	$invert = false;
	if (isset($_GET['invert']) && $_GET['invert'] == 'yes'){
		$invert = true;
	}

	function mirror_out($field){
		global $csv_accum;
		$fieldOrig = $field;

		$field = strip_tags($field);
		$field = str_replace(",","",$field);
		if ($csv_accum==''){
			$csv_accum=$field;
		}else{
			if (strrpos($csv_accum,chr(10)) == (strlen($csv_accum)-1)){
				$csv_accum .= $field;
			}else{
				$csv_accum .= "," . $field;
			}
		}
		return $fieldOrig;
	}

	// detect whether this is monthly detail request
	$sel_month = 0;
	if (isset($_GET['month']) && isset($_GET['year'])){
		$sel_month = $_GET['month'];
		$sel_year = $_GET['year'];
	}

	// get list of orders_status names for dropdown selection
	$orders_statuses = array();
	$orders_status_array = array();
	$QordersStatus = Doctrine_Query::create()
	->select('orders_status_id, sd.orders_status_name')
	->from('OrdersStatus s')
	->leftJoin('s.OrdersStatusDescription sd')
	->where('sd.language_id = ?', Session::get('languages_id'))
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	foreach($QordersStatus as $osInfo){
		$orders_statuses[] = array(
			'id'   => $osInfo['orders_status_id'],
			'text' => $osInfo['OrdersStatusDescription'][0]['orders_status_name']
		);
		$orders_status_array[$osInfo['orders_status_id']] = $osInfo['OrdersStatusDescription'][0]['orders_status_name'];
	}

	// name of status selection
	$orders_status_text = sysLanguage::get('TEXT_ALL_ORDERS');
	if (isset($_GET['status'])){
		$orders_status_text = $orders_status_array[$_GET['status']];
	}

	// determine if loworder fee is enabled in configuration, include/omit the column
	$QlowOrder = sysConfig::get('MODULE_ORDER_TOTAL_LOWORDERFEE_LOW_ORDER_FEE');
	$loworder = false;
	if ($QlowOrder != '') {
		if ($QlowOrder == 'true') $loworder = true;
	}

	//
	// if there are extended class values in orders_table
	// create extra column so totals are comprehensively correct
	$classValueArr = array(
		'ot_subtotal',
		'subtotal',
		'total',
		'tax',
		'shipping',
		'ot_tax',
		'ot_shipping',
		'ot_loworderfee',
		'ot_total'
	);
	$QclassCheck = Doctrine_Query::create()
	->select('value')
	->from('OrdersTotal')
	->whereNotIn('module_type', $classValueArr)
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	$extra_class = false;
	if ($QclassCheck){
		$extra_class = true;
	}
	// start accumulator for the report content mirrored in CSV
	$csv_accum = '';
?>
<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE_MONTHLY_SALES');
?></div>
<div style="text-align:right;"><?php
	if ($sel_month <> 0){
		$buttonLinkParams = array();
		if (isset($_GET['status'])){
			$buttonLinkParams[] = 'status=' . $status;
		}

		if (isset($_GET['invert'])){
			$buttonLinkParams[] = '&invert=yes';
		}
		$backButtonLink = itw_app_link(implode('&', $buttonLinkParams), 'statistics', 'monthlySales');

		echo htmlBase::newElement('button')
		->usePreset('back')
		->setHref($backButtonLink)
		->draw();
	}

	echo htmlBase::newElement('button')
	->usePreset('print')
	->attr('target', 'print')
	->setHref(itw_app_link(tep_get_all_get_params(array('action', 'app', 'appPage')) . 'print=yes', 'statistics', 'monthlySales'))
	->disable()
	->draw();

	echo htmlBase::newElement('button')
	->usePreset('back')
	->setText(sysLanguage::get('TEXT_BUTTON_REPORT_INVERT'))
	->setHref(itw_app_link(tep_get_all_get_params(array('action', 'invert', 'app', 'appPage')) . (!$invert ? 'invert=yes': ''), 'statistics', 'monthlySales'))
	->draw();

	echo htmlBase::newElement('button')
	->usePreset('help')
	->setHref(itw_app_link('action=helpMonthlySales', 'statistics', 'monthlySales'))
	->attr('onclick', 'window.open(this.href, \'help\', config=\'height=400,width=600,scrollbars=1,resizable=1\');return false;')
	->draw();
?></div>
<div style="text-align:right;"><form name="status_select" action="<?php echo itw_app_link();?>" method="GET"><?php
	$statusDrop = htmlBase::newElement('selectbox')
	->setName('status')
	->attr('onchange', 'this.form.submit();');
	foreach($orders_status_array as $k => $v){
		$statusDrop->addOption($k, $v);
	}

	echo sysLanguage::get('HEADING_TITLE_STATUS') . ': ' . $statusDrop->draw();
	if ($sel_month<>0){
		echo htmlBase::newElement('input')->setType('hidden')->setName('month')->setValue($sel_month)->draw();
		echo htmlBase::newElement('input')->setType('hidden')->setName('year')->setValue($sel_year)->draw();
	}

	if ($invert){
		echo htmlBase::newElement('input')->setType('hidden')->setName('invert')->setValue('yes')->draw();
	}
?></form></div>
<?php
	$reportTable = htmlBase::newElement('table')
	->setCellPadding(2)
	->setCellSpacing(0)
	->css(array(
		'width' => '100%'
	))
	->addClass('ui-widget ui-widget-content');

	$col2Text = ($sel_month == 0 ? sysLanguage::get('TABLE_HEADING_YEAR') : sysLanguage::get('TABLE_HEADING_DAY'));
	$headerCols = array(
		array('align' => 'left', 'valign' => 'bottom', 'width' => '45', 'text' => mirror_out(sysLanguage::get('TABLE_HEADING_MONTH'))),
		array('align' => 'left', 'valign' => 'bottom', 'width' => '35', 'text' => mirror_out($col2Text)),
		array('align' => 'right', 'valign' => 'bottom', 'width' => '70', 'text' => mirror_out(sysLanguage::get('TABLE_HEADING_INCOME'))),
		array('align' => 'right', 'valign' => 'bottom', 'width' => '70', 'text' => mirror_out(sysLanguage::get('TABLE_HEADING_SALES'))),
		array('align' => 'right', 'valign' => 'bottom', 'width' => '70', 'text' => mirror_out(sysLanguage::get('TABLE_HEADING_RENTAL_MEMBERSHIPS'))),
		array('align' => 'right', 'valign' => 'bottom', 'width' => '70', 'text' => mirror_out(sysLanguage::get('TABLE_HEADING_NONTAXED'))),
		array('align' => 'right', 'valign' => 'bottom', 'width' => '70', 'text' => mirror_out(sysLanguage::get('TABLE_HEADING_TAXED'))),
		array('align' => 'right', 'valign' => 'bottom', 'width' => '70', 'text' => mirror_out(sysLanguage::get('TABLE_HEADING_TAX_COLL'))),
		array('align' => 'right', 'valign' => 'bottom', 'width' => '70', 'text' => mirror_out(sysLanguage::get('TABLE_HEADING_SHIPHNDL'))),
		array('align' => 'right', 'valign' => 'bottom', 'width' => '70', 'text' => mirror_out(sysLanguage::get('TABLE_HEADING_SHIP_TAX')))
	);

	if ($loworder){
		$headerCols[] = array('align' => 'right', 'valign' => 'bottom', 'width' => '70', 'text' => mirror_out(sysLanguage::get('TABLE_HEADING_LOWORDER')));
	}

	if ($extra_class){
		$headerCols[] = array('align' => 'right', 'valign' => 'bottom', 'width' => '70', 'text' => mirror_out(sysLanguage::get('TABLE_HEADING_OTHER')));
	}

	$reportTable->addHeaderRow(array(
		'addCls' => 'ui-widget-header',
		'columns' => $headerCols
	));

	// clear footer totals
	$footer_gross = 0;
	$footer_sales = 0;
	$footer_rentals = 0;
	$footer_sales_nontaxed = 0;
	$footer_sales_taxed = 0;
	$footer_tax_coll = 0;
	$footer_shiphndl = 0;
	$footer_shipping_tax = 0;
	$footer_loworder = 0;
	$footer_other = 0;

	// new line for CSV
	$csv_accum .= "\n";

	// order totals, the driving force
	$status = '';

	$Qsales = Doctrine_Query::create()
	->select('
		o.orders_id,
		SUM(ot.value) as gross_sales,
		MONTHNAME(o.date_purchased) as row_month,
		YEAR(o.date_purchased) as row_year,
		MONTH(o.date_purchased) as i_month,
		DAYOFMONTH(o.date_purchased) as row_day,
	')
	->from('Orders o')
	->leftJoin('o.OrdersTotal ot')
	->andWhereIn('ot.module_type', array('total', 'ot_total'))
	->groupBy('YEAR(o.date_purchased) , MONTH(o.date_purchased)' . ($sel_month > 0 ? ' , DAYOFMONTH(o.date_purchased)' : ''))
	->orderBy('o.date_purchased ' . ($invert ? 'asc' : 'desc'));

	if (isset($_GET['status']) && !empty($_GET['status'])){
		$Qsales->andWhere('o.orders_status = ?', $status);
	}

	if ($sel_month > 0){
		$Qsales->andWhere('MONTH(o.date_purchased) = ?', $sel_month);
	}

	$Result = $Qsales->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if ($Result){
		$rows = 0;
		$lastRowNum = sizeof($Result);
		foreach($Result as $sInfo){
			$rows++;
			if ($rows > 1 && $sInfo['row_year'] <> $last_row_year){
				$col1Text = '';
				if ($sInfo['row_year'] == date('Y')){
					$col1Text = sysLanguage::get('TABLE_FOOTER_YTD');
				}elseif ($sel_month==0){
					$col1Text = sysLanguage::get('TABLE_FOOTER_YEAR');
				}else{
					$col1Text = strtoupper(substr($sInfo['row_month'],0,3));
				}

				$cssLeft = array(
					'text-align' => 'left',
					'font-weight' => 'bold'
				);
				$cssRight = array(
					'text-align' => 'right',
					'font-weight' => 'bold'
				);

				$bodyRowCols = array(
					array('css' => $cssLeft, 'text' => mirror_out($col1Text)),
					array('css' => $cssLeft, 'text' => mirror_out($last_row_year)),
					array('css' => $cssRight, 'format' => 'currency', 'text' => mirror_out($footer_gross)),
					array('css' => $cssRight, 'format' => 'currency', 'text' => mirror_out($footer_sales)),
					array('css' => $cssRight, 'format' => 'currency', 'text' => mirror_out($footer_rentals)),
					array('css' => $cssRight, 'format' => 'currency', 'text' => mirror_out($footer_sales_nontaxed)),
					array('css' => $cssRight, 'format' => 'currency', 'text' => mirror_out($footer_sales_taxed)),
					array('css' => $cssRight, 'format' => 'currency', 'text' => mirror_out($footer_tax_coll)),
					array('css' => $cssRight, 'format' => 'currency', 'text' => mirror_out($footer_shiphndl)),
					array('css' => $cssRight, 'format' => 'currency', 'text' => mirror_out(($footer_shipping_tax <= 0) ? 0 : $footer_shipping_tax))
				);

				if ($loworder){
					$bodyRowCols[] = array('css' => $cssRight, 'format' => 'currency', 'text' => mirror_out($footer_loworder));
				}

				if ($extra_class){
					$bodyRowCols[] = array('css' => $cssRight, 'format' => 'currency', 'text' => mirror_out($footer_other));
				}

				$reportTable->addBodyRow(array(
					'addCls' => 'ui-state-hover',
					'columns' => $bodyRowCols
				));

				// clear footer totals
				$footer_gross = 0;
				$footer_sales = 0;
				$footer_rentals = 0;
				$footer_sales_nontaxed = 0;
				$footer_sales_taxed = 0;
				$footer_tax_coll = 0;
				$footer_shiphndl = 0;
				$footer_shipping_tax = 0;
				$footer_loworder = 0;
				$footer_other = 0;

				// new line for CSV
				$csv_accum .= "\n";
			}

			$Queries = array();

			$Queries['salesRental'] = Doctrine_Query::create()
			->select('SUM(op.final_price * op.products_quantity) as total')
			->from('Orders o')
			->leftJoin('o.OrdersProducts op')
			->andWhere('op.purchase_type = ?', 'membership');

			$Queries['netNoTax'] = Doctrine_Query::create()
			->select('SUM(op.final_price * op.products_quantity) as total')
			->from('Orders o')
			->leftJoin('o.OrdersProducts op')
			->where('op.products_tax = ?', '0');

			$Queries['netTax'] = Doctrine_Query::create()
			->select('SUM(op.final_price * op.products_quantity) as total')
			->from('Orders o')
			->leftJoin('o.OrdersProducts op')
			->where('op.products_tax > ?', '0');

			$Queries['grossSales'] = Doctrine_Query::create()
			->select('SUM(op.final_price * op.products_quantity * (1 + (op.products_tax / 100.0))) as total')
			->from('Orders o')
			->leftJoin('o.OrdersProducts op')
			->where('op.products_tax > ?', '0');

			$Queries['salesTax'] = Doctrine_Query::create()
			->select('SUM((op.final_price * op.products_quantity * (1 + (op.products_tax / 100.0))) - (op.final_price * op.products_quantity)) as total')
			->from('Orders o')
			->leftJoin('o.OrdersProducts op')
			->where('op.products_tax > ?', '0');

			$Queries['taxCollected'] = Doctrine_Query::create()
			->select('SUM(ot.value) as total')
			->from('Orders o')
			->leftJoin('o.OrdersTotal ot')
			->whereIn('ot.module_type', array('ot_tax','tax'));

			$Queries['shippingCollected'] = Doctrine_Query::create()
			->select('SUM(ot.value) as total')
			->from('Orders o')
			->leftJoin('o.OrdersTotal ot')
			->whereIn('ot.module_type', array('ot_shipping','shipping'));

			if ($loworder) {
				$Queries['lowOrderFees'] = Doctrine_Query::create()
				->select('SUM(ot.value) as total')
				->from('Orders o')
				->leftJoin('o.OrdersTotal ot')
				->where('ot.module_type = ?', 'ot_loworderfee');
			}

			if ($extra_class){
				$Queries['otherOrderFees'] = Doctrine_Query::create()
				->select('SUM(ot.value) as total')
				->from('Orders o')
				->leftJoin('o.OrdersTotal ot')
				->whereNotIn('ot.module_type', $classValueArr);
			}

			foreach($Queries as $finalVarName => $queryObj){
				$queryObj->andWhere('MONTH(o.date_purchased) = ?', $sInfo['i_month'])
				->andWhere('YEAR(o.date_purchased) = ?', $sInfo['row_year']);

				if ($status <> ''){
					$queryObj->andWhere('o.orders_status = ?', $status);
				}

				if ($sel_month <> 0){
					$queryObj->andWhere('DAYOFMONTH(o.date_purchased) = ?', $sInfo['row_day']);
				}

				$$finalVarName = $queryObj->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			}

			$rentals_sales_this_row = $salesRental[0]['total'];
			$zero_rated_net_sales_this_row = $netNoTax[0]['total'];
			$net_sales_this_row = $netTax[0]['total'];
			$gross_sales_this_row = $grossSales[0]['total'];
			$sales_tax_this_row = $salesTax[0]['total'];
			$tax_this_row = $taxCollected[0]['total'];
			$shiphndl_this_row = $shippingCollected[0]['total'];
			if ($loworder){
				$loworder_this_row = $QlowOrderFees[0]['total'];
			}
			if ($extra_class){
				$other_this_row = $otherOrderFees[0]['total'];
			}

			// Correct any rounding errors
			$rentals_sales_this_row = (floor(($rentals_sales_this_row * 100) + 0.5)) / 100;
			$net_sales_this_row = (floor(($net_sales_this_row * 100) + 0.5)) / 100;
			$sales_tax_this_row = (floor(($sales_tax_this_row * 100) + 0.5)) / 100;
			$zero_rated_net_sales_this_row = (floor(($zero_rated_net_sales_this_row * 100) + 0.5)) / 100;
			$tax_this_row = (floor(($tax_this_row * 100) + 0.5)) / 100;

			// accumulate row results in footer
			$footer_gross += $sInfo['gross_sales']; // Gross Income
			$footer_sales += $net_sales_this_row + $zero_rated_net_sales_this_row - $rentals_sales_this_row; // Product Sales
			$footer_rentals += $rentals_sales_this_row; // Product Rental
			$footer_sales_nontaxed += $zero_rated_net_sales_this_row; // Nontaxed Sales
			$footer_sales_taxed += $net_sales_this_row; // Taxed Sales
			$footer_tax_coll += $sales_tax_this_row; // Taxes Collected
			$footer_shiphndl += $shiphndl_this_row; // Shipping & handling
			$footer_shipping_tax += ($tax_this_row - $sales_tax_this_row); // Shipping Tax
			if ($loworder) $footer_loworder += $loworder_this_row;
			if ($extra_class) $footer_other += $other_this_row;

			$col1Text = mirror_out(substr($sInfo['row_month'],0,3));

			if ($sel_month == 0){
				$col1Text = htmlBase::newElement('a')
				->setHref(itw_app_link(tep_get_all_get_params(array('action', 'month', 'year')) . 'month=' . $sInfo['i_month'] . '&year=' . $sInfo['row_year']))
				->html($col1Text)
				->draw();
			}

			if ($sel_month==0){
				$col2Text = mirror_out($sInfo['row_year']);
			}else{
				$col2Text = mirror_out($sInfo['row_day']);
			}
			$last_row_year = $sInfo['row_year']; // save this row's year to check for annual footer

			$sh_tax = $tax_this_row - $sales_tax_this_row;
			$col9Text =  ($sh_tax <= 0) ? 0 : $sh_tax;

			$bodyRowCols = array(
				array('align' => 'left', 'text' => $col1Text),
				array('align' => 'left', 'text' => $col2Text),
				array('align' => 'right', 'format' => 'currency', 'text' => mirror_out($sInfo['gross_sales'])),
				array('align' => 'right', 'format' => 'currency', 'text' => mirror_out($net_sales_this_row + $zero_rated_net_sales_this_row - $rentals_sales_this_row)),
				array('align' => 'right', 'format' => 'currency', 'text' => mirror_out($rentals_sales_this_row)),
				array('align' => 'right', 'format' => 'currency', 'text' => mirror_out($zero_rated_net_sales_this_row)),
				array('align' => 'right', 'format' => 'currency', 'text' => mirror_out($net_sales_this_row)),
				array('align' => 'right', 'format' => 'currency', 'text' => mirror_out($sales_tax_this_row)),
				array('align' => 'right', 'format' => 'currency', 'text' => mirror_out($shiphndl_this_row)),
				array('align' => 'right', 'format' => 'currency', 'text' => mirror_out($col9Text))
			);

			if ($loworder){
				$bodyRowCols[] = array('align' => 'right', 'format' => 'currency', 'text' => mirror_out($loworder_this_row));
			}

			if ($extra_class){
				$bodyRowCols[] = array('align' => 'right', 'format' => 'currency', 'text' => mirror_out($other_this_row));
			}

			$reportTable->addBodyRow(array(
				'columns' => $bodyRowCols
			));

			$csv_accum .= "\n";

			if ($rows == $lastRowNum){
				if ($sel_month <> 0){
					$col1Text = strtoupper(substr($sInfo['row_month'],0,3));
				}elseif ($sInfo['row_year'] == date('Y')){
					$col1Text = sysLanguage::get('TABLE_FOOTER_YTD');
				}else{
					$col1Text = sysLanguage::get('TABLE_FOOTER_YEAR');
				}

				$cssLeft = array(
					'text-align' => 'left',
					'font-weight' => 'bold'
				);
				$cssRight = array(
					'text-align' => 'right',
					'font-weight' => 'bold'
				);

				$bodyRowCols = array(
					array('css' => $cssLeft, 'text' => mirror_out($col1Text)),
					array('css' => $cssLeft, 'text' => mirror_out($sInfo['row_year'])),
					array('css' => $cssRight, 'format' => 'currency', 'text' => mirror_out($footer_gross)),
					array('css' => $cssRight, 'format' => 'currency', 'text' => mirror_out($footer_sales)),
					array('css' => $cssRight, 'format' => 'currency', 'text' => mirror_out($footer_rentals)),
					array('css' => $cssRight, 'format' => 'currency', 'text' => mirror_out($footer_sales_nontaxed)),
					array('css' => $cssRight, 'format' => 'currency', 'text' => mirror_out($footer_sales_taxed)),
					array('css' => $cssRight, 'format' => 'currency', 'text' => mirror_out($footer_tax_coll)),
					array('css' => $cssRight, 'format' => 'currency', 'text' => mirror_out($footer_shiphndl)),
					array('css' => $cssRight, 'format' => 'currency', 'text' => mirror_out(($footer_shipping_tax <= 0 ? 0 : $footer_shipping_tax)))
				);

				if ($loworder){
					$bodyRowCols[] = array('css' => $cssRight, 'format' => 'currency', 'text' => mirror_out($footer_loworder));
				}

				if ($extra_class){
					$bodyRowCols[] = array('css' => $cssRight, 'format' => 'currency', 'text' => mirror_out($footer_other));
				}

				$reportTable->addBodyRow(array(
					'addCls' => 'ui-state-hover',
					'columns' => $bodyRowCols
				));

				$footer_gross = 0;
				$footer_sales = 0;
				$footer_rentals = 0;
				$footer_sales_nontaxed = 0;
				$footer_sales_taxed = 0;
				$footer_tax_coll = 0;
				$footer_shiphndl = 0;
				$footer_shipping_tax = 0;
				$footer_loworder = 0;
				$footer_other = 0;

				// new line for CSV
				$csv_accum .= "\n";
			}
		}

		$form = htmlBase::newElement('form')
		->attr('name', 'cvs_stuff')
		->attr('method', 'POST')
		->attr('action', itw_app_link('action=saveMonthlySalesCsv', 'statistics', 'monthlySales'));

		$saveAsValue = 'sales_report_';
		if ($sel_month <> 0){
			$saveAsValue .= $sel_year;
		}

		if ($sel_month < 10){
			$saveAsValue .= '0' . $sel_month;
		}else{
			$saveAsValue .= $sel_month;
		}
		$saveAsValue .= '_';

		if (strpos($orders_status_text, ' ')){
			$saveAsValue .= substr($orders_status_text, 0, strpos($orders_status_text,' '));
		}else{
			$saveAsValue .= $orders_status_text;
		}
		$saveAsValue .= '_' . date('YmdHi');

		$form->append(htmlBase::newElement('input')->setType('hidden')->setName('csv')->setValue($csv_accum));
		$form->append(htmlBase::newElement('input')->setType('hidden')->setName('saveas')->setValue($saveAsValue));
		$form->append(htmlBase::newElement('button')->setType('submit')->usePreset('save')->setText(sysLanguage::get('TEXT_BUTTON_REPORT_SAVE')));
	}else{
		$reportTable->addBodyRow(array(
			'columns' => array(
				array('colspan' => sizeof($headerCols), 'text' => sysLanguage::get('TEXT_NOTHING_FOUND'))
			)
		));
	}
?>
 <div style="width:100%;float:left;">
  <div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
   <div style="width:99%;margin:5px;">
   <?php echo $reportTable->draw();?>
   </div>
  </div>
  <?php
  if (isset($form)){
  	echo '<div style="text-align:right;margin:.5em;">' . $form->draw() . '</div>';
  }
  ?>
 </div>
