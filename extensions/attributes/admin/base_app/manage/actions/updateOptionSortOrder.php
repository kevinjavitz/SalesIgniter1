<?php
	$Table = Doctrine_Core::getTable('ProductsOptionsToProductsOptionsGroups');
	$Table->setAttribute(Doctrine_Core::ATTR_COLL_KEY, 'products_options_groups_id');
	$groupId = $_GET['group_id'];
	$AllOptions = $Table->findByProductsOptionsGroupsId($groupId);
	$currentValues = $AllOptions->toArray();

	$newOrders = explode(',',$_GET['option']);
	for($i=0; $i<sizeof($newOrders); $i++){
		$currentValues[$newOrders[$i]]['sort_order'] = $i;
	}
	
	$AllOptions->synchronizeWithArray($currentValues);
	$AllOptions->save();

	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>