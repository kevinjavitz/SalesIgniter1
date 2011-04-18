<div id="rentalMembership"><?php
	$Qsum = dataAccess::setQuery('select sum(membership_months) as months_sum, sum(membership_days) as days_sum from {members}');
	$Qsum->setTable('{members}', TABLE_MEMBER);
	$Qsum->runQuery();
  
	$months = $Qsum->getVal('months_sum');
	$days = $Qsum->getVal('days_sum');
  
	$sep = tep_draw_separator('pixel_trans.gif', '100%', '10');
  
	$productTable = htmlBase::newElement('table')->css('width', '100%')->setCellPadding(3)->setCellSpacing(0);
	
	$tableColumns = array();
	$tableColumns[] = array('addCls' => 'main', 'text' => '&nbsp;');
	$tableColumns[] = array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('AMC_PACKAGE_NAME') . '</b>', 'align' => 'left');
	if ($months > 0){
		$tableColumns[] = array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('AMC_MEMBERSHIP_MONTHS') . '</b>', 'align' => 'center');
	}
	if ($days > 0){
		$tableColumns[] = array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('AMC_MEMBERSHIP_DAYS') . '</b>', 'align' => 'center');
	}
	$tableColumns[] = array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('AMC_N_OF_TITLES') . '</b>', 'align' => 'center');
	if (RENTAL_SHOW_TAX_COLUMN == 'true'){
		$tableColumns[] = array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('AMC_TAX') . '</b>', 'align' => 'center');
	}
	$tableColumns[] = array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('AMC_PRICE') . '</b>', 'align' => 'center');
	if (RENTAL_SHOW_TOTAL_PRICE_COLUMN == 'true'){
		$tableColumns[] = array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('AMC_PRICE_WITH_TAX') . '</b>', 'align' => 'center');
	}
	if (RENTAL_SHOW_TRIAL_COLUMN == 'true'){
		$tableColumns[] = array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('AMC_FREE_TRIAL_DAYS') . '</b>', 'align' => 'center');
	}
	
	$productTable->addBodyRow(array(
		'columns' => $tableColumns
	));

	$Qcheck = dataAccess::setQuery('select plan_id from {membership} where default_plan = "1"')
	->setTable('{membership}', TABLE_MEMBER)
	->runQuery();
	$hasDefault = false;
	if ($Qcheck->numberOfRows() > 0){
		$hasDefault = true;
		$default = $Qcheck->getVal('plan_id');
	}
	$Qplan = dataAccess::setQuery('select tm.*, tt.tax_rate as tax from {membership} tm left join {tax_rates} tt on tt.tax_rates_id = tm.rent_tax_class_id order by tm.sort_order asc')
	->setTable('{membership}', TABLE_MEMBER)
	->setTable('{tax_rates}', TABLE_TAX_RATES);
	$i=1;
	while($Qplan->next() !== false) {
		if (($hasDefault === false && $i == 1) || ($hasDefault === true && $Qplan->getVal('plan_id') == $default)) {
			$chk = true;
		} else {
			$chk = false;
		}
		
		$tableColumns = array();
		$tableColumns[] = array('addCls' => 'main', 'text' => tep_draw_radio_field('plan_id', $Qplan->getVal('plan_id'), $chk, 'class="rentalPlans"'), 'align' => 'center');
		$tableColumns[] = array('addCls' => 'main', 'text' => $Qplan->getVal('package_name'));
		if ($months > 0){
			$tableColumns[] = array('addCls' => 'main', 'text' => $Qplan->getVal('membership_months'), 'align' => 'center');
		}
		if ($days > 0){
			$tableColumns[] = array('addCls' => 'main', 'text' => $Qplan->getVal('membership_days'), 'align' => 'center');
		}
		$tableColumns[] = array('addCls' => 'main', 'text' => $Qplan->getVal('no_of_titles'), 'align' => 'center');
		if (RENTAL_SHOW_TAX_COLUMN == 'true'){
			$tableColumns[] = array('addCls' => 'main', 'text' => tep_display_tax_value($Qplan->getVal('tax'), 0) . '%', 'align' => 'center');
		}
		$tableColumns[] = array('addCls' => 'main', 'text' => $currencies->format($Qplan->getVal('price'), true, $order->info['currency'], $order->info['currency_value']), 'align' => 'center');
		if (RENTAL_SHOW_TOTAL_PRICE_COLUMN == 'true'){
			$tableColumns[] = array('addCls' => 'main', 'text' => $currencies->format(tep_add_tax($Qplan->getVal('price'), $Qplan->getVal('tax')), true, $order->info['currency'], $order->info['currency_value']), 'align' => 'center');
		}
		if (RENTAL_SHOW_TRIAL_COLUMN == 'true'){
			$tableColumns[] = array('addCls' => 'main', 'text' => $Qplan->getVal('free_trial'), 'align' => 'center');
		}
	
		$productTable->addBodyRow(array(
			'columns' => $tableColumns
		));
		$i++;
	}
	echo $productTable->draw();
?></div>