<?php
	$OrderedProduct = $Editor->ProductManager->get($_GET['id']);

	$OrderedProduct->setPurchaseType($_GET['purchase_type']);

	EventManager::attachActionResponse(array(
		'success' => true,
		'price' => (float) $OrderedProduct->getFinalPrice(false, false),
		'name' => $OrderedProduct->getNameEdit($Editor->ProductManager->getExcludedPurchaseTypes($OrderedProduct))
	), 'json');
?>