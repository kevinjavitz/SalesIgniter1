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
	->where('products_id=?', $pID_string)
	->andWhere('price > 0')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	$semDates = array();
    if(count($QPeriods)){
		$QPeriodsNames = Doctrine_Query::create()
		->from('PayPerRentalPeriods')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	$excludedPeriodId = array();
	foreach($QPeriods as $iPeriod){
				foreach($QPeriodsNames as $periodNames){
					if($periodNames['period_id'] == $iPeriod['period_id']){
						$periodName = $periodNames;
						break;
					}
				}
				$sDate['start_date'] = $periodName['period_start_date'];
				$sDate['end_date'] = $periodName['period_end_date'];
				$sDate['period_id'] = $iPeriod['period_id'];
				$sDate['period_name'] = $periodName['period_name'];
				$sDate['price'] = $iPeriod['price'];
				$semDates[] = $sDate;
		}
    }
    /*end periods*/
    if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_GLOBAL_MIN_RENTAL_DAYS') == 'False'){
       	$minRentalDays = $pprTable->min_rental_days;
    }else{
       	$minRentalDays = (int)sysConfig::get('EXTENSION_PAY_PER_RENTALS_MIN_RENTAL_DAYS');
    }
	$insurancePrice = $pprTable->insurance;
	$maxRentalDays = -1;
	if ($purchaseTypeClass->hasMaxDays() || $purchaseTypeClass->hasMaxMonths()){
		$useDays = $purchaseTypeClass->hasMaxDays();
		$maxRentalDays = ($useDays ? $purchaseTypeClass->getMaxDays() : $purchaseTypeClass->getMaxMonths() * 30);
	}

	$startTime = mktime(0,0,0,date('m'), 1, date('Y'));
	$endTime = mktime(0,0,0,date('m'), 1, date('Y')+3);

	$bookings = $purchaseTypeClass->getReservations(date('Y-m-d', $startTime), date('Y-m-d', $endTime));

    $maxShippingDays = -1;
	if ($purchaseTypeClass->shippingIsNone() === false && $purchaseTypeClass->shippingIsStore() === false){
	        $shippingTable = $purchaseTypeClass->buildShippingTable(true, false);
	     	$maxShippingDays =  $purchaseTypeClass->getMaxShippingDays();
    }

	$booked = array();
        $shippingDaysPadding = array();
        $shippingDaysArray = array();
	if ($product->hasPackageProducts('reservation')){
		$products = $product->getPackageProducts('reservation');
		while($startTime <= $endTime){
			$dateFormatted = date('Y-n-j', $startTime);
			$dateBooked = false;
			foreach($products as $pInfo){
				$pID = $pInfo['productClass']->getID();
				if (!isset($invItems[$pID])){
					$invItems[$pID] = $pInfo['productClass']->getInventoryItems('reservation');
				}

				if ($purchaseTypeClass->dateIsBooked($dateFormatted, $bookings, $invItems[$pID], ($pInfo['packageQuantity']*$_POST['rental_qty']))){
					$dateBooked = true;
					break;
				}
			}

			if ($dateBooked === true){
				$booked[] = '"' . $dateFormatted . '"';
				/*periods*/
				$op = 0;
				foreach($semDates as $sDate){
					if(strtotime($dateFormatted) >= strtotime($sDate['start_date']) && strtotime($dateFormatted) <= strtotime($sDate['end_date'])){
						unset($semDates[$op]);
						//break;
					}
					$op++;
				}
				$semDates = array_values($semDates);
				/*end periods*/
			}
			$startTime += 60*60*24;
		}
	}else{
		$invItems = $purchaseTypeClass->getInventoryItems();
		//print_r($invItems);
		while($startTime <= $endTime){
			$dateFormatted = date('Y-n-j', $startTime);
			if ($purchaseTypeClass->dateIsBooked($dateFormatted, $bookings, $invItems, (int)$_POST['rental_qty'])){
				$booked[] = '"' . $dateFormatted . '"';
				/*period*/
						$op = 0;
						foreach($semDates as $sDate){
							if(strtotime($dateFormatted) >= strtotime($sDate['start_date']) && strtotime($dateFormatted) <= strtotime($sDate['end_date'])){
								unset($semDates[$op]);
								//break;
							}
							$op++;
						}
						$semDates = array_values($semDates);
							/*end period*/
                for($i=0;$i<=$maxShippingDays;$i++){
                    $dateFormattedS = date('Y-n-j', strtotime('-'.$i.' days',$startTime));
                    $valDate = '"' . $dateFormattedS . '"';
                    $valPos = array_search($valDate, $shippingDaysPadding);
                    if ($valPos === false){
                        $shippingDaysPadding[] = $valDate;
                        $shippingDaysArray[] = '"' . ($i) . '"';
							/*period*/
						$op = 0;
						foreach($semDates as $sDate){
							if(strtotime($dateFormattedS) >= strtotime($sDate['start_date']) && strtotime($dateFormattedS) <= strtotime($sDate['end_date'])){
								unset($semDates[$op]);
								//break;
							}
							$op++;
						}
						$semDates = array_values($semDates);
							/*end period*/
                    }else{
                        if ((int)substr($shippingDaysArray[$valPos],1,strlen($shippingDaysArray[$valPos])-2) > $i){
                            $shippingDaysArray[$valPos] = '"' . $i. '"';
                        }
                    }
                }
                for($i=0;$i<=$maxShippingDays;$i++){
                    $dateFormattedS = date('Y-n-j', strtotime('+'.$i.' days',$startTime));
                    $valDate = '"' . $dateFormattedS . '"';
                    $valPos = array_search($valDate, $shippingDaysPadding);
                    if ($valPos === false){
                        $shippingDaysPadding[] = $valDate;
                        $shippingDaysArray[] = '"' . ($i) . '"';
							/*period*/
						$op = 0;
						foreach($semDates as $sDate){
							if(strtotime($dateFormattedS) >= strtotime($sDate['start_date']) && strtotime($dateFormattedS) <= strtotime($sDate['end_date'])){
								unset($semDates[$op]);
								//break;
							}
							$op++;
						}
						$semDates = array_values($semDates);
							/*end period*/
                    }else{
                        if ((int)substr($shippingDaysArray[$valPos],1,strlen($shippingDaysArray[$valPos])-2) > $i){
                            $shippingDaysArray[$valPos] = '"' .$i. '"';
                        }
                    }
                }
			}
			$startTime += 60*60*24;
		}
	}
	$disabledDays = sysConfig::explode('EXTENSION_PAY_PER_RENTALS_DISABLED_DAYS', ',');
	$startTimePadding = strtotime(date('Y-m-d'));
	$endTimePadding = strtotime('+' . (int)sysConfig::get('EXTENSION_PAY_PER_RENTALS_DATE_PADDING').' days', $startTimePadding);
        while($startTimePadding <= $endTimePadding){
			$dateFormatted = date('Y-n-j', $startTimePadding);
			$paddingDays[] = '"' . $dateFormatted . '"';
			/*period*/
			$op = 0;
			foreach($semDates as $sDate){
				if(strtotime($dateFormatted) >= strtotime($sDate['start_date']) && strtotime($dateFormatted) <= strtotime($sDate['end_date'])){
					unset($semDates[$op]);
					//break;
				}
				$op++;
			}
			$semDates = array_values($semDates);
			/*end period*/
			$startTimePadding += 60*60*24;
	}
	$op = 0;
    $dateFormatted = date('Y-n-j');
	foreach($semDates as $sDate){
		if(strtotime($dateFormatted) >= strtotime($sDate['start_date'])){
			unset($semDates[$op]);
			//break;
		}
		$op++;
	}
 	$semDates = array_values($semDates);

	$semOptions = '';
 	$semOptions.='<option value="">'.sysLanguage::get('TEXT_SELECT_SEMESTER').'</option>';
	foreach($semDates as $sDate){
		$semOptions.='<option value="'.$sDate['period_name'].'" start_date="'.$sDate['start_date'].'" end_date="'.$sDate['end_date'].'">'.$sDate['period_name'].'</option>';
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