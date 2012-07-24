<?php
	if(!isset($_POST['start_time']) && !isset($_POST['end_time']) || sysConfig::get('EXTENSION_PAY_PER_RENTALS_ENABLE_TIME_DROPDOWN') == 'False'){
	$starting_date = date('Y-m-d H:i:s', strtotime($_POST['start_date']));
	$ending_date = date('Y-m-d H:i:s', strtotime($_POST['end_date']));
	}else{
		$starting_date = date('Y-m-d H:i:s', strtotime($_POST['start_date'].' '.$_POST['start_time']));
		$ending_date = date('Y-m-d H:i:s', strtotime($_POST['end_date'] .' '.$_POST['end_time']));
	}
 	//$isSemester = (isset($_POST['isSemester'])?true:false);
  	$semName = (isset($_POST['semester_name'])?$_POST['semester_name']:'');
	$success = false;
	$price = 0;
	$totalPrice = 0;
	$totalPrice1 = 0;
	$message = '';
	$htmlShipping = '';
	$shipVal = 0;
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

        if(isset($_GET['freeTrialButton']) && $_GET['freeTrialButton'] == '1'){
            $freeOn = explode(',',$_GET['freeTrial']);
            $pricing = $purchaseTypeClass->getReservationPrice($starting_date, $ending_date, $rInfo, $semName, false, true ,true);
        }
        else{
            $pricing = $purchaseTypeClass->getReservationPrice($starting_date, $ending_date, $rInfo, $semName, isset($_POST['hasInsurance'])?true:false, $onlyShow);
        }

		$Module = OrderShippingModules::getModule($purchaseTypeClass->shipModuleCode);
		$selectedMethod = '';

		$weight = 0;
		if($Module->getType() == 'Order' && $App->getEnv() == 'catalog'){
			if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_SHOW_SHIPPING_ON_CALENDAR_IF_ORDER') == 'False'){
				$dontShow = 'none';
			}
			foreach($ShoppingCart->getProducts() as $cartProduct) {
				if ($cartProduct->hasInfo('reservationInfo') === true){
					$reservationInfo1 = $cartProduct->getInfo('reservationInfo');
					if(isset($reservationInfo1['shipping']) && isset($reservationInfo1['shipping']['module']) && $reservationInfo1['shipping']['module'] == 'zonereservation'){
						$selectedMethod = $reservationInfo1['shipping']['id'];
						$cost = 0;
						if(isset($reservationInfo1['shipping']['cost'])){
							$cost = $reservationInfo1['shipping']['cost'];
						}
						$totalPrice1 += $cartProduct->getFinalPrice(true) * $cartProduct->getQuantity() - $cost * $cartProduct->getQuantity();
						$dontShow = '';
						break;
					}
					$weight += $cartProduct->getWeight();

				}
			}

		}

		if(isset($_POST['rental_qty'])){
			$prod_weight = (int)$_POST['rental_qty'] * $product->getWeight();
		}else{
			$prod_weight = $product->getWeight();
		}

		$weight += $prod_weight;
	    $totalPrice1 += $pricing['price'];

		$quotes = array($Module->quote($selectedMethod, $weight, $totalPrice1));

		if (sizeof($quotes[0]['methods']) > 0 && ($Module->getType() == 'Product' || sysConfig::get('EXTENSION_PAY_PER_RENTALS_SHOW_SHIPPING_ON_CALENDAR_IF_ORDER') == 'True')){
			$htmlShipping .=  $purchaseTypeClass->parseQuotes($quotes) ;
		}

		$popArr = array();
		$bookings = array();
		$timeBookings = array();
		$startTime = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		$minTime = 60;
		$rQty = isset($_POST['rental_qty'])?$_POST['rental_qty']:1;
		$usableBarcodes = array();
			$reservArr = array();
			$barcodesBooked = array();
			$bookingsF = $purchaseTypeClass->getBookedDaysArray(date('Y-m-d', $startTime), $rQty, &$reservArr, &$barcodesBooked, $usableBarcodes);
			if($bookingsF === false){
				$bookingsF = array();
			}
			$timeBookingsF = $purchaseTypeClass->getBookedTimeDaysArray(date('Y-m-d', $startTime), $rQty, $minTime, $reservArr, $barcodesBooked);
			$bookings = array_merge($bookings, $bookingsF);
			$timeBookings = array_merge($timeBookings, $timeBookingsF);
		$toRemoveStart = array();
		$toRemoveEnd = array();
		foreach ($timeBookings as $iBook) {
			$timeDateParse = date_parse($iBook);
			$postStartDate = isset($_POST['start_date'])?date_parse($_POST['start_date']):date_parse(date('Y-m-d'));
			$postEndDate = isset($_POST['end_date'])?date_parse($_POST['end_date']):date_parse(date('Y-m-d'));
			if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_ENABLE_TIME_DROPDOWN') == 'True'){
				if($timeDateParse['year'] == $postStartDate['year'] && $timeDateParse['month'] == $postStartDate['month'] && $timeDateParse['day'] == $postStartDate['day']){
					$toRemoveStart[] = $timeDateParse['hour'];
				}
				if($timeDateParse['year'] == $postEndDate['year'] && $timeDateParse['month'] == $postEndDate['month'] && $timeDateParse['day'] == $postEndDate['day']){
					$toRemoveEnd[] = $timeDateParse['hour'];
				}
			}
		}
		$timeInputs =  sysLanguage::get('ENTRY_RENTAL_TIMES_SELECTED');
		$starttimeVal = (int) sysConfig::get('EXTENSION_PAY_PER_RENTALS_START_TIME');
		$endtimeVal = (int) sysConfig::get('EXTENSION_PAY_PER_RENTALS_END_TIME');
		      if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_TIME_INCREMENT') == '1/2'){
				  $time_increment = 30;
			  }else{
				  $time_increment = 60;
			  }
		      $hourStart = htmlBase::newElement('selectbox')
					  ->setName('start_time')
					  ->addClass('start_time');
		      $hourEnd = htmlBase::newElement('selectbox')
					  ->setName('end_time')
					  ->addClass('end_time');
		      if(isset($_POST['start_time'])){
				  $hourStart->selectOptionByValue($_POST['start_time']);
			  }
				if(isset($_POST['end_time'])){
					$hourEnd->selectOptionByValue($_POST['end_time']);
				}
		      for($i=$starttimeVal;$i<=$endtimeVal;$i++){
				  $notAddStart = false;
				  $notAddEnd = false;
				  if(in_array($i, $toRemoveStart)){
					  $notAddStart = true;
				  }
				  if(in_array($i, $toRemoveEnd)){
					  $notAddEnd = true;
				  }
				  if((int)$i < 10){
					  $val = '0'.$i.':00:00';
					  if($i > 12){
						  $am = ' PM';
						  $j = $i % 12;
					  }elseif($i == 12){
						  $am = 'PM';
						  $j = '12';
					  }
					  else{
						  $am = ' AM';
						  $j = $i;
					  }
					  $myVal = '0'.$j.':00'. $am;
				  }else{
					  $val = $i.':00:00';
					  if($i > 12){
						  $am = ' PM';
						  $j = $i % 12;
					  }elseif($i == 12){
						  $am = ' PM';
						  $j = '12';
					  }
					  else{
						  $am = ' AM';
						  $j = $i;
					  }
					  $myVal = $j.':00'.$am;
				  }
				  if(!$notAddStart){
					  $hourStart->addOption($val, $myVal);
				  }
				  if(!$notAddEnd){
					  $hourEnd->addOption($val, $myVal);
				  }
				  if($time_increment == 30 && $i<$endtimeVal){
					  if((int)$i < 10){
						  $val = '0'.$i.':30:00';
						  $myVal = '0'.$i.':30';
					  }else{
						  $val = $i.':30:00';
						  $myVal = $i.':30';
					  }
					  $hourStart->addOption($val, $myVal);
					  $hourEnd->addOption($val, $myVal);
				  }
			  }
			$timeInputs .= sysLanguage::get('EXTENSION_PAY_PER_RENTALS_START_TIME_DELIVERY'). $hourStart->draw();
		  	$timeInputs .= '&nbsp;&nbsp;&nbsp;&nbsp;'.sysLanguage::get('EXTENSION_PAY_PER_RENTALS_END_TIME_PICKUP').$hourEnd->draw();
	    if (is_array($pricing) && is_numeric($pricing['price'])){
		    $price += $pricing['price'];
		    $totalPrice += $pricing['totalPrice'];
		    $message .= strip_tags($pricing['message']);
			if($Module->getType() == 'Order' && isset($pricing['orderShipping'])){
				$shipVal = $pricing['orderShipping'];
			}
		    $success = true;
	    }
	}


	EventManager::attachActionResponse(array(
		'success' => $success,
		'price'   => $currencies->format($price),
		'totalPrice'   => $currencies->format($_POST['rental_qty'] * $totalPrice - ($_POST['rental_qty'] - 1) * $shipVal),
		'htmlShipping' => $htmlShipping,
		'timeInputs' => $timeInputs,
		'message' => $message
	), 'json');
?>