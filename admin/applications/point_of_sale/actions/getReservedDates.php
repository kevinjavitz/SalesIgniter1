<?php
	$product = new product($_POST['pID']);
	$bookings = $product->getReservations($_POST['start_date'], $_POST['end_date']);

	$startArr = date_parse($_POST['start_date']);
	$endArr = date_parse($_POST['end_date']);

	$start = mktime(0, 0, 0, $startArr['month'], $startArr['day'], $startArr['year']);
	$end = mktime(0, 0, 0, $endArr['month'], $endArr['day'], $endArr['year']);

	$booked = array();
	if ($product->hasPackageProducts('reservation')){
		$products = $product->getPackageProducts('reservation');
		while($start <= $end){
			$dateFormatted = date('Y-n-j', $start);
			$dateBooked = false;
			foreach($products as $pInfo){
				$pID = $pInfo['productClass']->getID();
				if (!isset($invItems[$pID])){
					$invItems[$pID] = $pInfo['productClass']->getInventoryItems('reservation');
				}

				if ($product->dateIsBooked($dateFormatted, $bookings, $invItems[$pID], ($pInfo['packageQuantity']*$_POST['rental_qty']))){
					$dateBooked = true;
					break;
				}
			}

			if ($dateBooked === true){
				$booked[] = '"' . $dateFormatted . '"';
			}
			$start += 60*60*24;
		}
	}else{
		$invItems = $product->getInventoryItems('reservation');
		while($start <= $end){
			$dateFormatted = date('Y-n-j', $start);
			if ($product->dateIsBooked($dateFormatted, $bookings, $invItems, (int)$_POST['rental_qty'])){
				$booked[] = '"' . $dateFormatted . '"';
			}
			$start += 60*60*24;
		}
	}

	$bookedDatesJS = '[' . implode(',', $booked) . ']';

	$QdisabledDates = tep_db_query('select * from blackout_dates');
	$disabledJS = array();
	while($disabledDates = tep_db_fetch_array($QdisabledDates)){
		$dateFrom = explode('-', $disabledDates['date_from']);
		foreach($dateFrom as $index => $number){
			$dateFrom[$index] = (int)$number;
		}

		$dateTo = explode('-', $disabledDates['date_to']);
		foreach($dateTo as $index => $number){
			$dateTo[$index] = (int)$number;
		}

		$disabledJS[] = '[[' . implode(',', $dateFrom) . '], [' . implode(',', $dateTo) . '], ' .
		'"' . $disabledDates['repeats'] . '"' .
		']';
	}
	$disabledJS = '[' . implode(',', $disabledJS) . ']';
	$disabledDaysJS = '["' . implode('", "', explode(',', EXTENSION_PAY_PER_RENTALS_DISABLED_DAYS)) . '"]';
	
	EventManager::attachActionResponse(array(
		'success'       => true,
		'dates'         => $bookedDatesJS,
		'disabledDays'  => $disabledDaysJS,
		'disabledDates' => $disabledJS
	), 'json');
?>