<?php
	$pID_string	= array();
	$purchaseTypeClasses = array();
	$OrderProduct = $Editor->ProductManager->get((int)$_POST['idP']);

   	$OrderProduct->setPurchaseType('reservation');
	$Product = $OrderProduct->productClass;

    $pInfo = $OrderProduct->getPInfo();
	$pID_string[] = $pInfo['products_id'];
	$purchaseTypeClasses[] = $OrderProduct->purchaseTypeClass;

	$usableBarcodes = array();
	if(isset($_POST['barcode']) && ($_POST['barcode'] != 'undefined')){
		$usableBarcodes[] = $_POST['barcode'];
	}

	$pInfo = $OrderProduct->getPInfo();
	$pInfo['usableBarcodes'] = $usableBarcodes;
    $OrderProduct->setPInfo($pInfo);
	$calendar = ReservationUtilities::getCalendar($pID_string, $purchaseTypeClasses, (isset($_POST['rental_qty'])?$_POST['rental_qty']:1), true, 'catalog', $usableBarcodes);

	EventManager::attachActionResponse(array(
		'success'   => true,
		'calendar'  => $calendar
	   ), 'json');
?>
