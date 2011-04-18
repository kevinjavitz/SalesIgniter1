<?php

	$text = $_POST['rental_status_text'];
	$color = $_POST['rental_status_color'];
	//$available = $_POST['rental_status_available'];

	$rentalStatus = Doctrine_Core::getTable('RentalStatus');
	if (isset($_GET['rID'])){
		$rentalStatus = $rentalStatus->find((int)$_GET['rID']);
	}else{
		$rentalStatus = new RentalStatus();
	}
	

	$rentalStatus->rental_status_text = $text;
	$rentalStatus->rental_status_color = $color;
	//$rentalStatus->rental_status_available = $available;
	$rentalStatus->save();

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'rID')) . 'rID=' . $rentalStatus->rental_status_id, null, 'default'), 'redirect');
?>