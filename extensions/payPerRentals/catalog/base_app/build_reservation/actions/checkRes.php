<?php
	$starting_date = date('Y-m-d H:i:s', strtotime($_POST['start_date']));
	$ending_date = date('Y-m-d H:i:s', strtotime($_POST['end_date']));
 	//$isSemester = (isset($_POST['isSemester'])?true:false);
  	$semName = (isset($_POST['semester_name'])?$_POST['semester_name']:'');
	$product = new product($_POST['pID']);
	$purchaseTypeClass = $product->getPurchaseType('reservation');
	global $total_weight;
 	$total_weight = (int)$_POST['rental_qty'] * $product->getWeight();
	OrderShippingModules::calculateWeight();
	$pricing = $purchaseTypeClass->getReservationPrice($starting_date, $ending_date, '', $semName);

	EventManager::attachActionResponse(array(
		'success' => (!is_array($pricing) ? 'not_supported' : true),
		'price'   => (is_array($pricing) && is_numeric($pricing['price']) ? $currencies->format($pricing['price']) : $pricing),
		'message' => $pricing['message']
	), 'json');
?>