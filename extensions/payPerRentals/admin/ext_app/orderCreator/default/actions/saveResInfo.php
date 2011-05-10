<?php
    $OrderProduct = $Editor->ProductManager->get((int) $_GET['id']);
	$OrderProduct->setPurchaseType($_GET['purchase_type']);
	$Product = $OrderProduct->productClass;
	$PurchaseType = $OrderProduct->purchaseTypeClass;
 	$reservationInfo = $OrderProduct->getPInfo();
    if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'False'){
		if ((isset($_GET['start_date']) && $_GET['start_date'] != 'undefined')&&(isset($_GET['end_date']) && $_GET['end_date'] != 'undefined')){
			$resInfo['start_date'] = $_GET['start_date'];
			$resInfo['end_date'] = $_GET['end_date'];
			$starting_date = date('Y-m-d H:i:s', strtotime($_GET['start_date']));
			$ending_date = date('Y-m-d H:i:s', strtotime($_GET['end_date']));
		}

	}else if (isset($_GET['event']) && $_GET['event'] != 'undefined'){
		$event_duration = 1;
		$Qevent = Doctrine_Query::create()
		->from('PayPerRentalEvents')
		->where('events_id = ?', $_GET['event'])
		->fetchOne();
		if($Qevent){
			$start_date = strtotime($Qevent->events_date);
			$starting_date = date("Y-m-d H:i:s", mktime(date("h",$start_date),date("i",$start_date), date("s",$start_date), date("m",$start_date), date("d",$start_date), date("Y",$start_date)));
			$ending_date = date("Y-m-d H:i:s", mktime(date("h",$start_date),date("i",$start_date), date("s",$start_date), date("m",$start_date), date("d",$start_date)+$event_duration, date("Y",$start_date)));
			//$resInfo['start_date'] = $starting_date;
			//$resInfo['end_date'] = $ending_date;
			/*check if date is booked*/
			$OrderProduct = $Editor->ProductManager->get((int) $_GET['id']);

			$startTime = mktime(0,0,0,date('m'), 1, date('Y'));
			$endTime = mktime(0,0,0,date('m'), 0, date('Y')+3);

			$bookings = $PurchaseType->getNewReservations(date('Y-m-d H:i:s', $startTime), date('Y-m-d H:i:s', $endTime), $Editor->ProductManager->getContents());

			if (isset( $_GET['qty'])){
				$_POST['rental_qty'] = $_GET['qty'];
			}else{
				$_POST['rental_qty'] = 1;
			}

			$booked = array();

			if ($Product->hasPackageProducts('reservation')){
				$products = $Product->getPackageProducts('reservation');
				while($startTime <= $endTime){
					$dateFormatted = date('Y-n-j', $startTime);
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
						$booked[] = $dateFormatted;
					}
					$startTime += 60*60*24;
				}
			}else{
				$invItems = $PurchaseType->getInventoryItems();
				//print_r($invItems);
				while($startTime <= $endTime){
					$dateFormatted = date('Y-n-j', $startTime);
					if ($PurchaseType->dateIsBooked($dateFormatted, $bookings, $invItems, (int) $_POST['rental_qty'])){
						$booked[] = $dateFormatted;
					}
					$startTime += 60*60*24;
				}
			}
			$startingTime = strtotime($starting_date);//here can be multiple dates...right now for event are two
			$endingTime = strtotime($ending_date);
			$dateIsReserved = false;
			while($startingTime <= $endingTime){
				$dateFormatted = date('Y-n-j', $startingTime);
				if (in_array($dateFormatted, $booked)){
					$dateIsReserved = true;
					break;
				}
				$startingTime += 60*60*24;
			}
			if (!$dateIsReserved){
				$resInfo['start_date'] = $starting_date;
				$resInfo['end_date'] = $ending_date;
			}

		}else{

		}

	}
	if (isset($resInfo['start_date']) && isset($resInfo['end_date'])){
		if(isset($_GET['shipping']) && $_GET['shipping'] != 'undefined'){
			$resInfo['rental_shipping'] = $_GET['shipping'];
		}else{
			$resInfo['rental_shipping'] = false;
		}
		if (isset($_GET['qty']) && $_GET['qty'] != 'undefined'){
			$resInfo['rental_qty'] = $_GET['qty'];
		}
		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'True' && $Qevent){
			$resInfo['event_name'] = $Qevent->events_name;
			$resInfo['event_date'] = $starting_date;
		}

		$PurchaseType->processAddToCartNew($reservationInfo, $resInfo);

		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'True' && $Qevent){
			$reservationInfo['reservationInfo']['event_name'] = $Qevent->events_name;
			$reservationInfo['reservationInfo']['event_date'] = $starting_date;
		}
		/*todo move into attributes*/
		if (isset($_POST['id']['reservation']) && !empty($_POST['id']['reservation'])){
			$reservationInfo['aID_string'] = attributesUtil::getAttributeString($_POST['id']['reservation']);//'{1}2';
		}
		//echo 'jj'. $reservationInfo['aID_string'];
		//itwExit();
		//print_r($reservationInfo);
		//itwExit();
		$OrderProduct->setPInfo($reservationInfo);
		EventManager::attachActionResponse(array(
		'success' => true,
		'price'	=> (isset($reservationInfo['price'])?$reservationInfo['price']:0)
	), 'json');
	}else{
		EventManager::attachActionResponse(array(
		'success' => false,
		'price'	=> (isset($reservationInfo['price'])?$reservationInfo['price']:0)
	), 'json');
	}




?>