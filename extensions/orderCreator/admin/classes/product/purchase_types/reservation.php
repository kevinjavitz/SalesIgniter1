<?php
require(sysConfig::getDirFsCatalog() . 'extensions/payPerRentals/catalog/classes/product/purchase_types/reservation.php');

class OrderCreatorProductPurchaseTypeReservation extends PurchaseType_reservation {
	
	public function addToOrdersProductCollection(&$ProductObj, &$CollectionObj){
		$ResInfo = $ProductObj->getInfo('reservationInfo');
		$allInfo = $ProductObj->getPInfo();
		if (isset($allInfo['aID_string']) && !empty($allInfo['aID_string'])){
			$this->inventoryCls->invMethod->trackMethod->aID_string = $allInfo['aID_string'];
		}
		$ShippingInfo = $ResInfo['shipping'];

		$StartDateArr = date_parse($ResInfo['start_date']);
		$EndDateArr = date_parse($ResInfo['end_date']);
		$StartDateFormatted = $this->formatDateArr('Y-m-d H:i:s', $StartDateArr);
		$EndDateFormatted = $this->formatDateArr('Y-m-d H:i:s', $EndDateArr);
		$Insurance = (isset($ResInfo['insurance']) ? $ResInfo['insurance'] : 0);
		$TrackMethod = $this->inventoryCls->getTrackMethod();

		$EventName ='';
		$EventDate = '0000-00-00 00:00:00';
		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'True'){
			$EventName = $ResInfo['event_name'];
			$EventDate = $ResInfo['event_date'];
		}

		$Reservations =& $CollectionObj->OrdersProductsReservation;
		$existingInfo = $ProductObj->getInfo();
		$QexitingOrders = Doctrine::getTable('OrdersProducts')->find($existingInfo['orders_products_id']);
		if ($QexitingOrders){
			$QexitingOrders->OrdersProductsReservation->delete();
		}

		$excludedBarcode = array();
		$excludedQuantity = array();
		for($count=1; $count <= $ResInfo['quantity']; $count++){
			$Reservation = new OrdersProductsReservation();
			$Reservation->start_date = $StartDateFormatted;
			$Reservation->end_date = $EndDateFormatted;
			$Reservation->insurance = $Insurance;
			$Reservation->event_name = $EventName;
			$Reservation->event_date = $EventDate;
			$Reservation->track_method = $TrackMethod;
			$Reservation->rental_state = 'reserved';
			if (isset($ShippingInfo['id']) && !empty($ShippingInfo['id'])){
				$Reservation->shipping_method_title = $ShippingInfo['title'];
				$Reservation->shipping_method = $ShippingInfo['id'];
				$Reservation->shipping_days_before = $ShippingInfo['days_before'];
				$Reservation->shipping_days_after = $ShippingInfo['days_after'];
				$Reservation->shipping_cost = $ShippingInfo['cost'];
			}

			if ($TrackMethod == 'barcode'){
				$Reservation->barcode_id = $this->getAvailableBarcode($ProductObj, $excludedBarcode);
				$excludedBarcode[] = $Reservation->barcode_id;
				$Reservation->ProductsInventoryBarcodes->status = 'R';
			}elseif ($TrackMethod == 'quantity'){
				$Reservation->quantity_id = $this->getAvailableQuantity($ProductObj, $excludedQuantity);
				$excludedQuantity[] = $Reservation->quantity_id;
				$Reservation->ProductsInventoryQuantity->available -= 1;
				$Reservation->ProductsInventoryQuantity->reserved += 1;
			}
			EventManager::notify('ReservationOnInsertOrderedProduct', $Reservation, &$ProductObj);

			$Reservations->add($Reservation);
		}
	}
	public function getNewReservations($start, $end, $newReservations){ //todo this needs revised
		$booked = ReservationUtilities::getReservations(
			$this->productInfo['id'],
			$start,
			$end,
			$this->overBookingAllowed()
		);

		foreach($newReservations as $reservationProductAll){
			$reservationProduct = $reservationProductAll->getInfo();
			if (isset($reservationProduct['OrdersProductsReservation'])){
				/*print_r($booked);
				echo 'dsds';
				itwExit();*/
				foreach($reservationProduct['OrdersProductsReservation'] as $ResInfo){
					$startDateArr = date_parse($ResInfo['start_date']);
					$endDateArr = date_parse($ResInfo['end_date']);
					$startTime = mktime($startDateArr['hour'],$startDateArr['minute'],$startDateArr['second'],$startDateArr['month'],$startDateArr['day']-$ResInfo['shipping_days_before'],$startDateArr['year']);
					$endTime = mktime($endDateArr['hour'],$endDateArr['minute'],$endDateArr['second'],$endDateArr['month'],$endDateArr['day']+$ResInfo['shipping_days_before'],$endDateArr['year']);

					while($startTime <= $endTime){
						$dateFormatted = date('Y-n-j', $startTime);
						unset($booked['barcode'][$dateFormatted]);
						$startTime += 60*60*24;
					}
				}
			}

		}

		return $booked;
	}

	public function processAddToCartNew(&$pInfo, $resInfo){
		$shippingInfo = array(
			'',
			''
		);
		if (isset($resInfo['rental_shipping']) && $resInfo['rental_shipping'] !== false){
			$shippingInfo = explode('_', $resInfo['rental_shipping']);
		}
		 //echo 'dd'. print_r($pInfo);
		 //echo 'hh'. print_r($_POST);
		$this->processAddToOrderOrCart(array(
			'shipping_module' => $shippingInfo[0],
			'shipping_method' => $shippingInfo[1],
			'start_date'      => $resInfo['start_date'],
			'end_date'        => $resInfo['end_date'],
			'quantity'        => $resInfo['rental_qty']
		), $pInfo);

		EventManager::notify('ReservationProcessAddToCart', $pInfo['reservationInfo']);
	}

}
?>