<?php
	$pID_string = $_POST['pID'];
	$product = new product($_POST['pID']);
	$purchaseTypeClass = $product->getPurchaseType('reservation');
 	$pprTable = Doctrine_Core::getTable('ProductsPayPerRental')->findOneByProductsId($pID_string);
	$calendar = ReservationUtilities::getCalendar($pID_string, $product, $purchaseTypeClass, (isset($_POST['rental_qty'])?$_POST['rental_qty']:1), true);

	EventManager::attachActionResponse(array(
		'success' => true,
		'calendar' => $calendar
	), 'json');
?>