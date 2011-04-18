<?php

	$pay_per_rental_types_name = $_POST['pay_per_rental_types_name'];
	$minutes = $_POST['minutes'];
	//$available = $_POST['rental_status_available'];

	$rentalType = Doctrine_Core::getTable('PayPerRentalTypes');
	if (isset($_GET['rID'])){
		$rentalType = $rentalType->find((int)$_GET['rID']);
	}else{
		$rentalType = new PayPerRentalTypes();
	}
	

	$rentalType->pay_per_rental_types_name = $pay_per_rental_types_name;
	$rentalType->minutes = $minutes;
	//$rentalStatus->rental_status_available = $available;
	$rentalType->save();

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'rID')) . 'rID=' . $rentalType->pay_per_rental_types_id, null, 'default'), 'redirect');
?>