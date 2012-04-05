<?php
	$OrderProduct = $Editor->ProductManager->get((int) $_POST['idP']);
	$OrderProduct->setPurchaseType($_POST['purchase_type']);
	$Product = $OrderProduct->productClass;
	$PurchaseType = $OrderProduct->purchaseTypeClass;
 	$reservationInfo = $OrderProduct->getPInfo();
$html2  =  '';
$nr = 0;
$goodDates = '';
$events_date = '';
$selectedDates = array();
    if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'False'){
		if ((isset($_POST['start_date']) && $_POST['start_date'] != 'undefined')&&(isset($_POST['end_date']) && $_GET['end_date'] != 'undefined')){
			$resInfo['start_date'] = $_POST['start_date']; //. $myStartTime;
			$resInfo['end_date'] = $_POST['end_date'];//.$myEndTime;
			$starting_date = date('Y-m-d H:i:s', strtotime($_GET['start_date']));
			$ending_date = date('Y-m-d H:i:s', strtotime($_GET['end_date']));
		}

	}else if (isset($_POST['event']) && $_POST['event'] != 'undefined'){
		$event_duration = 1;
		$Qevent = Doctrine_Query::create()
		->from('PayPerRentalEvents')
		->where('events_id = ?', $_POST['event'])
		->fetchOne();
		if($Qevent){
			$start_date = strtotime($Qevent->events_date);
			if(!isset($_POST['days_before'])){
				$_POST['days_before'] = 0;
			}
			if(!isset($_POST['days_after'])){
				$_POST['days_after'] = 0;
			}
			$starting_date = date("Y-m-d H:i:s", strtotime('- '.$_POST['days_before'].' days', mktime(date("h",$start_date),date("i",$start_date), date("s",$start_date), date("m",$start_date), date("d",$start_date), date("Y",$start_date))));
			$ending_date = date("Y-m-d H:i:s", strtotime('+ '.$_POST['days_after'].' days',mktime(date("h",$start_date),date("i",$start_date), date("s",$start_date), date("m",$start_date), date("d",$start_date)+$event_duration, date("Y",$start_date))));
			//$resInfo['start_date'] = $starting_date;
			//$resInfo['end_date'] = $ending_date;
			/*check if date is booked*/
			$OrderProduct = $Editor->ProductManager->get((int) $_POST['idP']);

			//$startTime = mktime(0,0,0,date('m'), 1, date('Y'));
			//$endTime = mktime(0,0,0,date('m'), 0, date('Y')+3);

			//$bookings = $PurchaseType->getNewReservations(date('Y-m-d H:i:s', $startTime), date('Y-m-d H:i:s', $endTime), $Editor->ProductManager->getContents());

			if (isset($_POST['qty']) && $_POST['qty'] != 'undefined'){
				$rQty = $_POST['qty'];
			}else{
				$rQty = 1;
			}

			//$startTime = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
			//$endTime = mktime(0,0,0,date('m'), 1, date('Y')+3);

			//this part under a for i merge array
			$reservArr = array();
			$barcodesBooked = array();
			$bookings = $PurchaseType->getBookedDaysArrayNew(date('Y-m-d', strtotime($start_date)), $rQty, &$reservArr, &$barcodesBooked, $Editor->ProductManager->getContents());

			$startingTime = strtotime($starting_date);//here can be multiple dates...right now for event are two
			$endingTime = strtotime($ending_date);
			$dateIsReserved = false;
			while($startingTime <= $endingTime){
				$dateFormatted = date('Y-n-j', $startingTime);
				if (in_array($dateFormatted, $bookings)){
					$dateIsReserved = true;
					break;
				}
				$startingTime += 60*60*24;
			}

			if (!$dateIsReserved){
				$resInfo['start_date'] = date('Y-m-d H:i:s', strtotime($Qevent->events_date));
				$resInfo['end_date'] = date('Y-m-d H:i:s', strtotime('+'. $event_duration. ' days', strtotime($Qevent->events_date)));
			}

		}else{

		}

	}

	if (isset($resInfo['start_date']) && isset($resInfo['end_date'])){

		if(isset($_POST['shipping']) && $_POST['shipping'] != 'undefined'){
			$shippingInfo = explode('_', $_POST['shipping']);
			$resInfo['shipping_module'] = $shippingInfo[0];
			$resInfo['shipping_method'] = $shippingInfo[1];
			$resInfo['days_before'] = (isset($_POST['days_before'])?$_POST['days_before']:0);
			$resInfo['days_after'] = (isset($_POST['days_after'])?$_POST['days_after']:0);
		}else{
			$resInfo['rental_shipping'] = false;
		}

		if (isset($_POST['qty']) && $_POST['qty'] != 'undefined'){
			$resInfo['quantity'] = $_POST['qty'];
		}

		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'True' && $Qevent){
			$resInfo['event_name'] = $Qevent->events_name;
			$resInfo['event_date'] = $starting_date;
			if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_GATES') == 'True' && isset($_POST['gate'])){
				$Qgate = Doctrine_Query::create()
				->from('PayPerRentalGates')
				->where('gates_id = ?', $_POST['gate'])
				->fetchOne();
				if($Qgate){
					$resInfo['event_gate'] = $Qgate->gate_name;
				}
			}

		}

		$PurchaseType->processAddToCartNew($reservationInfo, $resInfo);

		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'True' && $Qevent){
			$reservationInfo['reservationInfo']['event_name'] = $Qevent->events_name;
			if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_GATES') == 'True' && isset($_POST['gate'])){
				if($Qgate){
					$reservationInfo['reservationInfo']['event_gate'] = $Qgate->gate_name;
				}else{
					$Qgate1 = Doctrine_Query::create()
					->from('PayPerRentalGates')
					->where('gates_id = ?',  $Qevent->default_gate)
					->fetchOne();
					$reservationInfo['reservationInfo']['event_gate'] = $Qgate1->gate_name;
				}
			}
			$reservationInfo['reservationInfo']['event_date'] = $starting_date;


			$events_date = date('m/d/Y', strtotime($Qevent->events_date));
			 //print_r($_POST);
			if($Qevent->events_days>1){
				$myDates = '';
				if(isset($_POST['multiple_dates'])){
					$datesArr = explode(',',$_POST['multiple_dates']);


					if(isset($datesArr[0]) && !empty($datesArr[0])){
						$selectedDates = $datesArr;
						$reservationInfo['reservationInfo']['multiple_dates'] = $datesArr;
						$myDates = '<div class="mydates">';
						foreach($datesArr as $iDate){
							$myDates .= '<input type="hidden" class="mpDates" name="multiple_dates[]" value="'.$iDate.'">';
						}
						$myDates .= '</div>';
					}
				}
				$html2 = '<div style="position:relative"><div class="allCalendar"><div class="myTextCalendar" style="color:red;background-color:#ffffff;width:200px;padding:10px;padding-top:5px;padding-bottom:5px;">Select Dates</div><div class="myCalendar"></div> </div><div class="calDone">Choose Dates</div><span class="closeCal ui-icon ui-icon-closethick"></span></div>'.$myDates;
				$startTimePadding = strtotime($Qevent->events_date);
				$endTimePadding = strtotime('+' . $Qevent->events_days . ' days', $startTimePadding);
				$booked = array();
				while ($startTimePadding <= $endTimePadding) {
					$dateFormatted = date('Y-n-j', $startTimePadding);
					$booked[] = $dateFormatted;
					$startTimePadding += 60 * 60 * 24;
				}
				$goodDates =  $booked;
			}

		}
		/*todo move into attributes*/
		if (isset($_POST['id']['reservation']) && !empty($_POST['id']['reservation'])){
			$attrValue = attributesUtil::getAttributeString($_POST['id']['reservation']);
			if(!empty($attrValue)){
				$reservationInfo['aID_string'] = $attrValue;
			}
		}
		if(isset($_POST['hasInsurance']) && $_POST['hasInsurance'] == '1'){
			$payPerRentals = Doctrine_Query::create()
			->select('insurance')
			->from('ProductsPayPerRental')
			->where('products_id = ?', $Product->getID())
			->fetchOne();

			$reservationInfo['reservationInfo']['insurance'] = $payPerRentals->insurance;
			//$reservationInfo['price'] += $payPerRentals->insurance;
		}
		if(count($selectedDates) > 0){
			$reservationInfo['price'] = count($selectedDates) * $reservationInfo['price'];
		}
		//}
		$OrderProduct->setPInfo($reservationInfo);

		EventManager::attachActionResponse(array(
		'success' => true,
		'calendar' => $html2,
		'events_date' => $events_date,
		'selectedDates' => $selectedDates,
		'goodDates' => $goodDates,
		'price'	=> (isset($reservationInfo['price'])?$reservationInfo['price']:0)
	), 'json');
	}else{
		EventManager::attachActionResponse(array(
		'success' => false,
		'price'	=> (isset($reservationInfo['price'])?$reservationInfo['price']:0)
	), 'json');
	}




?>