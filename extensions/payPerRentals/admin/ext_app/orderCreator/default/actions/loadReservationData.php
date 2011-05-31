<?php
	$pID_string	= array();
	$purchaseTypeClasses = array();
	//foreach($_POST['pID'] as $pElem){
		$OrderProduct = $Editor->ProductManager->get($_POST['pID']);
   	    $OrderProduct->setPurchaseType('reservation');
		$Product = $OrderProduct->productClass;

        $pInfo = $OrderProduct->getPInfo();
		$pID_string[] = $pInfo['products_id'];
		$purchaseTypeClasses[] = $OrderProduct->purchaseTypeClass;
	//}

	$calendar = ReservationUtilities::getCalendar($pID_string, $purchaseTypeClasses, (isset($_POST['rental_qty'])?$_POST['rental_qty']:1), true, 'catalog');

	EventManager::attachActionResponse(array(
		'success'   => true,
		'calendar'  => $calendar
	   ), 'json');
?>
