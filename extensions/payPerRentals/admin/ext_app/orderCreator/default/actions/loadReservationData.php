<?php

	$OrderProduct = $Editor->ProductManager->get((int) $_POST['pID']);
   	$OrderProduct->setPurchaseType('reservation');
	$Product = $OrderProduct->productClass;
	$purchaseTypeClass = $OrderProduct->purchaseTypeClass;

	/*if (isset($_POST['id']['reservation']) && !empty($_POST['id']['reservation'])){
		$purchaseTypeClass->inventoryCls->invMethod->trackMethod->aID_string = attributesUtil::getAttributeString($_POST['id']['reservation']);
	}*/

    $pInfo = $OrderProduct->getPInfo();
	$calendar = ReservationUtilities::getCalendar($pInfo['products_id'], $Product, $purchaseTypeClass, (isset($_POST['rental_qty'])?$_POST['rental_qty']:1), true, 'catalog');

	EventManager::attachActionResponse(array(
		'success'   => true,
		'calendar'  => $calendar
	   ), 'json');
?>
