<?php

	if (isset($_GET['rID'])){
		$rID = $_GET['rID'];
	}else{
		$rID = 0;
	}

	$type = $_GET['type'];
	
	if($type == 'reservation'){
		if ($rID != 0){
			$orderProductsReservation = Doctrine_Core::getTable('OrdersProductsReservation')->find($rID);
			$orderProductsReservation->delete();
		}
	}else{
		if ($rID != 0){
			$rentedProducts = Doctrine_Core::getTable('RentedProducts')->find($rID);
			$rentedProducts->delete();
		}
	}
	$json = array(
			'success'   => true
	);

	EventManager::attachActionResponse($json, 'json');

?>