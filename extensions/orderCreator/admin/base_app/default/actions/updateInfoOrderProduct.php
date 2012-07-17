<?php
$OrderedProduct = $Editor->ProductManager->get($_GET['id']);
if(is_object($OrderedProduct) && $_GET['purchase_type'] != 'reservation'){

if ($Editor->hasErrors() === false){
	$response = array(
		'success' => true,
		'hasError' => false,
		'price' => (float)$OrderedProduct->getFinalPrice(false, false),
		'name' => $OrderedProduct->getNameEdit(),
		'barcodes' => $OrderedProduct->getBarcodeEdit()
	);
}else {
	$response = array(
		'success' => true,
		'hasError' => true,
		'errorMessage' => $Editor->getErrors()
	);
}
} else {
	$response = array(
		'success' => true,
		'noObject' => true,
		'hasError' => false
	);
}
EventManager::attachActionResponse($response, 'json');
