<?php
	$OrderProduct = $Editor->ProductManager->get((int) $_GET['id']);
   	$OrderProduct->setPurchaseType($_GET['purchase_type']);
	$Product = $OrderProduct->productClass;
	$purchaseTypeClass = $OrderProduct->purchaseTypeClass;
 	if (isset($_POST['id']['reservation']) && !empty($_POST['id']['reservation'])){
		$purchaseTypeClass->inventoryCls->invMethod->trackMethod->aID_string = attributesUtil::getAttributeString($_POST['id']['reservation']);
	}
	$pprTable = Doctrine_Core::getTable('ProductsPayPerRental')->findOneByProductsId($Product->getID());
    	$total_weight = $Product->getWeight();
	OrderShippingModules::calculateWeight();
 	/*periods*/
  	$QPeriods = Doctrine_Query::create()
	->from('ProductsPayPerPeriods')
	->where('products_id=?', $Product->getID())
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

    $maxShippingDays = -1;
	if ($purchaseTypeClass->shippingIsNone() === false && $purchaseTypeClass->shippingIsStore() === false){
	     	$maxShippingDays =  $purchaseTypeClass->getMaxShippingDays(date('Y-m-d', $startTime));
    }
    if (isset( $_GET['qty'])){
		$_POST['rental_qty'] = $_GET['qty'];
	}else{
		$_POST['rental_qty'] = 1;
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
		//$stringStart = '"'.$timeDateParse['year'].','.($timeDateParse['month']-1).','.$timeDateParse['day'].','.$timeDateParse['hour'].','.$timeDateParse['minute'].'"';
		//$stringEnd = '"'.$timeDateParse['year'].','.($timeDateParse['month']-1).','.$timeDateParse['day'].','.$timeDateParse['hour'].','.($timeDateParse['minute']+1).'"';
		if($timeDateParse['month']<10){
			$zerom = '0';
		}else{
			$zerom = '';
		}
		if($timeDateParse['minute']<10){
			$zero = '0';
		}else{
			$zero = '';
		}
		/*if($timeDateParse['minute']+1<10){
			$zerop = '0';
		}else{
			$zerop = '';
		} */
		$stringStartDate = ''.$timeDateParse['year'].'-'.$zerom.($timeDateParse['month']).'-'.$timeDateParse['day'].' '.$timeDateParse['hour'].':'.$zero.($timeDateParse['minute']).':00';
		//$stringStartDate = strtotime($iBook);
		//$stringEndDate = '\''.$timeDateParse['year'].'-'.$zerom.($timeDateParse['month']).'-'.$timeDateParse['day'].' '.$timeDateParse['hour'].':'.$zerop.($timeDateParse['minute']+1).':00\'';
		//$timeBooked[] = "{title:'Not Available',start:".$stringStartDate.",end:".$stringEndDate.", allDay:false}";
		$timeBookedDate[] = $stringStartDate;
	}

	$disabledDates = array();

	EventManager::attachActionResponse(array(
		'success' => true,
		'calStartDate' => (int) sysConfig::get('EXTENSION_PAY_PER_RENTALS_DATE_PADDING'),
		'minTime' => sysConfig::get('EXTENSION_PAY_PER_RENTALS_START_TIME'),
		'maxTime' => sysConfig::get('EXTENSION_PAY_PER_RENTALS_END_TIME'),
		'minRentalPeriod' => $minRentalPeriod,
		'maxRentalPeriod' => $maxRentalPeriod,
		//'startArray' => $timeBooked,
		'bookedTimesArr' => $timeBookedDate,
		'allowSelectionBefore' => true,
		'allowSelectionAfter' => true,
		'allowSelection' => true,
		'allowSelectionMin' => true,
		'allowSelectionMax' => true,
		'allowHourly' => $allowHourly,
		'bookedDates' => $booked,
		'disabledDays' => $disabledDays,
		'disabledDates' => $disabledDates,
		'shippingDaysPadding' => $shippingDaysPadding,
		'shippingDaysArray' => $shippingDaysArray
	), 'json');
?>
