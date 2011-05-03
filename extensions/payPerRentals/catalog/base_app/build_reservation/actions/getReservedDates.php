<?php
	$pID_string = $_POST['pID'];
	$product = new product($_POST['pID']);
	$purchaseTypeClass = $product->getPurchaseType('reservation');
 	$pprTable = Doctrine_Core::getTable('ProductsPayPerRental')->findOneByProductsId($pID_string);

   	global $total_weight;
 	$total_weight = (int)$_POST['rental_qty'] * $product->getWeight();
	OrderShippingModules::calculateWeight();

 	/*periods*/
  	$QPeriods = Doctrine_Query::create()
	->from('ProductsPayPerPeriods')
	->where('products_id=?', $_GET['products_id'])
	->andWhere('price > 0')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	$semDates = array();

	$sDate = array();
    if(count($QPeriods)){
		$QPeriodsNames = Doctrine_Query::create()
		->from('PayPerRentalPeriods')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	foreach($QPeriods as $iPeriod){
				$periodName = '';
				foreach($QPeriodsNames as $periodNames){
					if($periodNames['period_id'] == $iPeriod['period_id']){
						$periodName = $periodNames;
						break;
					}
				}
				if($periodName != ''){
					$sDate['start_date'] = $periodName['period_start_date'];
					$sDate['end_date'] = $periodName['period_end_date'];
					$sDate['period_id'] = $iPeriod['period_id'];
					$sDate['period_name'] = $periodName['period_name'];
					$sDate['price'] = $iPeriod['price'];
					$semDates[] = $sDate;
				}
		}
    }
    /*end periods*/

	$allowHourly = (sysConfig::get('EXTENSION_PAY_PER_RENTALS_ALLOW_HOURLY') == 'True')?true:false;
	$minTime = 15;//slotMinutes

    if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_GLOBAL_MIN_RENTAL_DAYS') == 'False'){
       	$minRentalPeriod = ReservationUtilities::getPeriodTime($pprTable->min_period, $pprTable->min_type) * 60*1000;

    }else{
       	$minRentalPeriod = (int)sysConfig::get('EXTENSION_PAY_PER_RENTALS_MIN_RENTAL_DAYS') *24*60*60*1000;
    }

    $minRentalPeriod = 0;
	$insurancePrice = $pprTable->insurance;
	$maxRentalPeriod = -1;

	if($pprTable->max_period > 0){
		$maxRentalPeriod = ReservationUtilities::getPeriodTime($pprTable->max_period, $pprTable->max_type) * 60* 1000;
	}

	$startTime = mktime(0,0,0,date('m'), date('d'), date('Y'));
	//$endTime = mktime(0,0,0,date('m'), 1, date('Y')+3);
    $reservArr = array();
	$barcodesBooked = array();
	$bookings = $purchaseTypeClass->getBookedDaysArray(date('Y-m-d', $startTime), 1, &$reservArr, &$barcodesBooked);
	$timeBookings = $purchaseTypeClass->getBookedTimeDaysArray(date('Y-m-d', $startTime), 1, $minTime, $reservArr, $barcodesBooked);
    //here I have to add an array for Times Booked

	$_POST['rental_qty'] = 1;
    $maxShippingDays = -1;
	if ($purchaseTypeClass->shippingIsNone() === false && $purchaseTypeClass->shippingIsStore() === false){
	        $shippingTable = $purchaseTypeClass->buildShippingTable(true, false);
	     	$maxShippingDays =  $purchaseTypeClass->getMaxShippingDays(date('Y-m-d', $startTime));
    }
/**
 * Days Bookings
 */
	$booked = array();
    $shippingDaysPadding = array();
    $shippingDaysArray = array();
	$paddingDays = array();
	foreach($bookings as  $iBook){
		$booked[] = '"' . $iBook . '"';

		//period
		$op = 0;
		foreach ($semDates as $sDate) {
			if (strtotime($iBook) >= strtotime($sDate['start_date']) && strtotime($iBook) <= strtotime($sDate['end_date'])) {
				unset($semDates[$op]);
			}
			$op++;
		}
		$semDates = array_values($semDates);
		//end period
		$startTime = strtotime($iBook);
		for ($i = 0; $i <= $maxShippingDays; $i++) {
			$dateFormattedS = date('Y-n-j', strtotime('-' . $i . ' days', $startTime));
			$valDate = '"' . $dateFormattedS . '"';
			$valPos = array_search($valDate, $shippingDaysPadding);
			if ($valPos === false) {
				$shippingDaysPadding[] = $valDate;
				$shippingDaysArray[] = '"' . ($i) . '"';
				//period
				$op = 0;
				foreach ($semDates as $sDate) {
					if (strtotime($dateFormattedS) >= strtotime($sDate['start_date']) && strtotime($dateFormattedS) <= strtotime($sDate['end_date'])) {
						unset($semDates[$op]);
					}
					$op++;
				}
				$semDates = array_values($semDates);
				//end period
			} else {
				if ((int) substr($shippingDaysArray[$valPos], 1, strlen($shippingDaysArray[$valPos]) - 2) > $i) {
					$shippingDaysArray[$valPos] = '"' . $i . '"';
				}
			}
		}
		for ($i = 0; $i <= $maxShippingDays; $i++) {
			$dateFormattedS = date('Y-n-j', strtotime('+' . $i . ' days', $startTime));
			$valDate = '"' . $dateFormattedS . '"';
			$valPos = array_search($valDate, $shippingDaysPadding);
			if ($valPos === false) {
				$shippingDaysPadding[] = $valDate;
				$shippingDaysArray[] = '"' . ($i) . '"';
				//period
				$op = 0;
				foreach ($semDates as $sDate) {
					if (strtotime($dateFormattedS) >= strtotime($sDate['start_date']) && strtotime($dateFormattedS) <= strtotime($sDate['end_date'])) {
						unset($semDates[$op]);
					}
					$op++;
				}
				$semDates = array_values($semDates);
				//end period
			} else {
				if ((int) substr($shippingDaysArray[$valPos], 1, strlen($shippingDaysArray[$valPos]) - 2) > $i) {
					$shippingDaysArray[$valPos] = '"' . $i . '"';
				}
			}
		}
	}

	$disabledDays = sysConfig::explode('EXTENSION_PAY_PER_RENTALS_DISABLED_DAYS', ',');
	$startTimePadding = strtotime(date('Y-m-d'));
	$endTimePadding = strtotime('+' . (int)sysConfig::get('EXTENSION_PAY_PER_RENTALS_DATE_PADDING').' days', $startTimePadding);
        while($startTimePadding <= $endTimePadding){
			$dateFormatted = date('Y-n-j', $startTimePadding);
			$paddingDays[] = '"' . $dateFormatted . '"';
			//period
			$op = 0;
			foreach($semDates as $sDate){
				if(strtotime($dateFormatted) >= strtotime($sDate['start_date']) && strtotime($dateFormatted) <= strtotime($sDate['end_date'])){
					unset($semDates[$op]);
				}
				$op++;
			}
			$semDates = array_values($semDates);
			//end period
			$startTimePadding += 60*60*24;
	}

    $QBlockedDates = Doctrine_Query::create()
    ->from('PayPerRentalBlockedDates')
    ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	foreach($QBlockedDates as $bInfo){
		$startTimePaddingArr = array();
		$endTimePaddingArr = array();

		if($bInfo['recurring'] == 0){
			$startTimePaddingArr[] = strtotime($bInfo['block_start_date']);
			$endTimePaddingArr[] = strtotime($bInfo['block_end_date']);
			$i = 1;
		}else{
			$i = 0;
			while(true){
				$bstartDate = strtotime('+'.$i.' years', strtotime($bInfo['block_start_date']));
				$bendDate = strtotime('+'.$i.' years', strtotime($bInfo['block_end_date']));
				$startTimePaddingArr[] = $bstartDate;
				$endTimePaddingArr[] = $bendDate;
				$i++;
				if(date('Y',$bendDate) - 3 > date('Y')){
					break;
				}
			}

		}
		$j = 0;
		while($j<$i){
			$startTimePadding = $startTimePaddingArr[$j];
			$endTimePadding = $endTimePaddingArr[$j];
			while($startTimePadding <= $endTimePadding){
					$dateFormatted = date('Y-n-j', $startTimePadding);
					$paddingDays[] = '"' . $dateFormatted . '"';
					//period
					$op = 0;
					foreach($semDates as $sDate){
						if(strtotime($dateFormatted) >= strtotime($sDate['start_date']) && strtotime($dateFormatted) <= strtotime($sDate['end_date'])){
							unset($semDates[$op]);
						}
						$op++;
					}
					$semDates = array_values($semDates);
					//end period
					$startTimePadding += 60*60*24;
			}
			$j++;
		}
    }

	$op = 0;
    $dateFormatted = date('Y-n-j');
	foreach($semDates as $sDate){
		if(strtotime($dateFormatted) >= strtotime($sDate['start_date'])){
			unset($semDates[$op]);
		}
		$op++;
	}
 	$semDates = array_values($semDates);
   /**
	* Time Bookings
   */
    $timeBooked = array();
	$timeBookedDate =  array();
	foreach($timeBookings as  $iBook){
		$timeDateParse = date_parse($iBook);
		$stringStart = 'new Date('.$timeDateParse['year'].','.($timeDateParse['month']-1).','.$timeDateParse['day'].','.$timeDateParse['hour'].','.$timeDateParse['minute'].')';
		$stringEnd = 'new Date('.$timeDateParse['year'].','.($timeDateParse['month']-1).','.$timeDateParse['day'].','.$timeDateParse['hour'].','.($timeDateParse['minute']+1).')';
		$timeBooked[] = "{title:'Not Available',start:".$stringStart.",end:".$stringEnd.", allDay:false}";
		$timeBookedDate[] = $stringStart;
	}


	EventManager::attachActionResponse(array(
		'success' => true,
		'bookedDates'   => $booked,
		'shippingDaysPadding'   => $shippingDaysPadding,
		'shippingDaysArray'   => $shippingDaysArray,
		'disabledDatesPadding' => $paddingDays,
	    'disabledDays' =>  $disabledDays,
		'semData' => $semOptions
	), 'json');
?>