<?php
	$Table = Doctrine_Core::getTable('ProductsOptionsValuesToProductsOptions');
	$Table->setAttribute(Doctrine_Core::ATTR_COLL_KEY, 'products_options_values_id');
	$optionId = $_GET['option_id'];
	
	$AllOptions = $Table->findByProductsOptionsId($optionId);
	$currentValues = $AllOptions->toArray();

	$newOrders = explode(',',$_GET['value']);
	for($i=0; $i<sizeof($newOrders); $i++){
		$currentValues[$newOrders[$i]]['sort_order'] = $i;
	}
	
	$AllOptions->synchronizeWithArray($currentValues);
	$AllOptions->save();

	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>