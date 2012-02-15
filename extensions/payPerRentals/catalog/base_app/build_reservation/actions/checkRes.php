<?php
	$starting_date = date('Y-m-d H:i:s', strtotime($_POST['start_date']));
	$ending_date = date('Y-m-d H:i:s', strtotime($_POST['end_date']));
 	//$isSemester = (isset($_POST['isSemester'])?true:false);
  	$semName = (isset($_POST['semester_name'])?$_POST['semester_name']:'');
	$success = false;
	$price = 0;
	$totalPrice = 0;
	$message = '';
    foreach($_POST['products_id'] as $pElem){
		$product = new product($pElem);
		$purchaseTypeClass = $product->getPurchaseType('reservation');
		global $total_weight;
		$total_weight = (int)$_POST['rental_qty'] * $product->getWeight();
		OrderShippingModules::calculateWeight();
	    $rInfo = '';

	    $onlyShow = true;
	    if(sysconfig::get('EXTENSION_PAY_PER_RENTALS_SHORT_PRICE') == 'True'){
		    $onlyShow = false;
	    }
		$pricing = $purchaseTypeClass->getReservationPrice($starting_date, $ending_date, $rInfo, $semName, isset($_POST['hasInsurance'])?true:false, $onlyShow);
	    if (is_array($pricing) && is_numeric($pricing['price'])){
		    $price += $pricing['price'];
		    $totalPrice += $pricing['totalPrice'];
		    $message .= strip_tags($pricing['message']);
		    $success = true;
	    }
	}

	EventManager::attachActionResponse(array(
		'success' => $success,
		'price'   => $currencies->format($price),
		'totalPrice'   => $currencies->format($_POST['rental_qty'] * $totalPrice),
		'message' => $message
	), 'json');
?>