<?php
	$OrderProduct = $Editor->ProductManager->get((int) $_GET['id']);
	$Product = $OrderProduct->productClass;
	$PurchaseType = $OrderProduct->purchaseTypeClass;
	
	if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_GLOBAL_MIN_RENTAL_DAYS') == 'False'){
		$minRentalDays = $PurchaseType->getMinRentalDays();
	}else{
		$minRentalDays = (int) sysConfig::get('EXTENSION_PAY_PER_RENTALS_MIN_RENTAL_DAYS');
	}

	$startTime = mktime(0,0,0,date('m'), 1, date('Y'));
	$endTime = mktime(0,0,0,date('m'), 0, date('Y')+3);

	$bookings = $PurchaseType->getReservations(date('Y-m-d', $startTime), date('Y-m-d', $endTime));
	if ($PurchaseType->shippingIsNone() === false && $PurchaseType->shippingIsStore() === false){
		$maxShippingDays = $PurchaseType->getMaxShippingDays();
	}

	$_POST['rental_qty'] = 1;

	$booked = array();
    $shippingDaysPadding = array();
    $shippingDaysArray = array();

	$invItems = $PurchaseType->getInventoryItems();
	//print_r($invItems);
	while($startTime <= $endTime){
			$dateFormatted = date('Y-n-j', $startTime);
			if ($PurchaseType->dateIsBooked($dateFormatted, $bookings, $invItems, (int) $_POST['rental_qty'])){
				$booked[] = $dateFormatted;

                for($i = 0; $i <= $maxShippingDays; $i++){
					$valDate = date('Y-n-j', strtotime('-' . $i . ' days', $startTime));
					$valPos = array_search($valDate, $shippingDaysPadding);
					if ($valPos === false){
						$shippingDaysPadding[] = $valDate;
						$shippingDaysArray[] = $i;
					}elseif ($shippingDaysArray[$valPos] < $i){
						$shippingDaysArray[$valPos] = $i;
					}
				}

				for($i = 0; $i <= $maxShippingDays; $i++){
					$valDate = date('Y-n-j', strtotime('+' . $i . ' days', $startTime));
					$valPos = array_search($valDate, $shippingDaysPadding);
					if ($valPos === false){
						$shippingDaysPadding[] = $valDate;
						$shippingDaysArray[] = $i;
					}elseif ($shippingDaysArray[$valPos] < $i){
						$shippingDaysArray[$valPos] = $i;
					}
				}
			}
			$startTime += 60*60*24;
	}

	$disabledDays = sysConfig::explode('EXTENSION_PAY_PER_RENTALS_DISABLED_DAYS', ',');

	EventManager::attachActionResponse(array(
		'success' => true,
		'calStartDate' => (int) sysConfig::get('EXTENSION_PAY_PER_RENTALS_DATE_PADDING'),
		'minRentalDays' => $minRentalDays,
		'allowSelectionBefore' => true,
		'allowSelectionAfter' => true,
		'allowSelection' => true,
		'allowSelectionMin' => true,
		'bookedDates' => $booked,
		'disabledDays' => $disabledDays,
		'disabledDates' => array(),
		'shippingDaysPadding' => $shippingDaysPadding,
		'shippingDaysArray' => $shippingDaysArray
	), 'json');
?>
