<?php
	$pID_string	= array();
	$purchaseTypeClasses = array();
	$OrderProduct = $Editor->ProductManager->get((int)$_POST['idP']);
	$calendar = '';
    if(is_object($OrderProduct)){
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
		$success = true;
	}else{
		$success = false;
	}
	EventManager::attachActionResponse(array(
		'success'   => $success,
		'calendar'  => $calendar
	   ), 'json');
?>
