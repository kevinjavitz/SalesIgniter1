<?php
$OrderedProduct = $Editor->ProductManager->get($_GET['id']);

if ($Editor->hasErrors() === false){
	$response = array(
		'success' => true,
		'hasError' => false,
		'price' => (float)$OrderedProduct->getFinalPrice(false, false),
		'name' => $OrderedProduct->getNameEdit(),
		'barcodes' => $OrderedProduct->getBarcodeEdit()
	);
}
else {
	$response = array(
		'success' => true,
		'hasError' => true,
		'errorMessage' => $Editor->getErrors()
	);
}
EventManager::attachActionResponse($response, 'json');
