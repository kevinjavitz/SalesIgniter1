<?php
	$extAttributes = $appExtension->getExtension('attributes');
	
	$tableHtml = $extAttributes->pagePlugin->getInventoryTable(array(
		'productId'    => $_POST['products_id'],
		'purchaseType' => $_POST['purchaseType'],
		'trackMethod'  => $_POST['trackMethod'],
		'options'      => $_POST['attribute_inventory_option']
	));
	
	EventManager::attachActionResponse($tableHtml, 'html');
?>