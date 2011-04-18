<?php
	$TaxRates = Doctrine_Core::getTable('TaxRates');
	if (isset($_GET['rID'])){
		$TaxRate = $TaxRates->find((int)$_GET['rID']);
	}else{
		$TaxRate = $TaxRates->create();
	}
	
	$TaxRate->tax_zone_id = $_POST['tax_zone_id'];
	$TaxRate->tax_class_id = $_POST['tax_class_id'];
	$TaxRate->tax_rate = $_POST['tax_rate'];
	$TaxRate->tax_description = $_POST['tax_description'];
	$TaxRate->tax_priority = $_POST['tax_priority'];
	
	$TaxRate->save();
	
	EventManager::attachActionResponse(array(
		'success' => true,
		'rID'     => $TaxRate->tax_rates_id
	), 'json');
?>