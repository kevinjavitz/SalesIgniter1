<?php
if (
	isset($_POST['products_type']) &&
	(!is_array($_POST['products_type']) && $_POST['products_type'] == 'rental') ||
	(is_array($_POST['products_type']) && in_array('rental', $_POST['products_type']))
){
	PurchaseTypeModules::loadModule('rental');
	$PurchaseType = PurchaseTypeModules::getModule('rental');

	$Product->ProductsRentalSettings->price = $_POST['products_price_rental'];
	$Product->ProductsRentalSettings->rental_period = $PurchaseType->getConfigData('MAXIMUM_ALLOWED_OUT');
}else{
	$Product->ProductsRentalSettings->remove();
}
$Product->save();
