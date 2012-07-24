<?php
require(sysConfig::getDirFsCatalog() . 'extensions/payPerRentals/catalog/classes/product/purchase_types/reservation.php');

class OrderCreatorProductPurchaseTypeReservation extends PurchaseType_reservation {
	
	public function addToOrdersProductCollection(&$ProductObj, &$CollectionObj){
		global $Editor;
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
			if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_GATES') == 'True'){
				$EventGate = $ResInfo['event_gate'];
			}

		}

		$Reservations =& $CollectionObj->OrdersProductsReservation;
		$existingInfo = $ProductObj->getInfo();
		$QexitingOrders = Doctrine::getTable('OrdersProducts')->find($existingInfo['orders_products_id']);
		if ($QexitingOrders){
			$QexitingOrders->OrdersProductsReservation->delete();
		}

		$excludedBarcode = array();
		$excludedQuantity = array();
        $barcodes = explode(',',$ResInfo['bar_id']);

		for($count=1; $count <= $ResInfo['quantity']; $count++){
			$Reservation = new OrdersProductsReservation();
			$Reservation->insurance = $Insurance;
			$Reservation->event_name = $EventName;

			if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_GATES') == 'True'){
				$Reservation->event_gate = $EventGate;
			}
			$Reservation->event_date = $EventDate;
			$Reservation->track_method = $TrackMethod;

            if($this->consumptionAllowed() === '1'){
                $barcode = substr($barcodes[$count -1 ],0,strrpos($barcodes[$count -1 ],'(',0));
                $dates = substr($barcodes[$count -1 ],strrpos($barcodes[$count -1 ],'(',0) + 1, strrpos($barcodes[$count -1 ],')',0) - strlen($barcode) - 1);
                $dates =   explode('-',$dates);
                $StartDateFormatted = date('Y-m-d H:i:s', strtotime($dates[0]));
                $EndDateFormatted = date('Y-m-d H:i:s', strtotime($dates[1]));
                $Reservation->rental_state = 'out';
                $Reservation->start_date = $StartDateFormatted;
                $Reservation->end_date = $EndDateFormatted;
            }else{
                $Reservation->rental_state = 'reserved';
                $Reservation->start_date = $StartDateFormatted;
                $Reservation->end_date = $EndDateFormatted;
            }

			if(isset($_POST['estimateOrder'])){
				$Reservation->is_estimate = 1;
			}else{
				$Reservation->is_estimate = 0;
			}
			if (isset($ShippingInfo['id']) && !empty($ShippingInfo['id'])){
				$Reservation->shipping_method_title = $ShippingInfo['title'];
				$Reservation->shipping_method = $ShippingInfo['id'];
				$Reservation->shipping_days_before = $ShippingInfo['days_before'];
				$Reservation->shipping_days_after = $ShippingInfo['days_after'];
				$Reservation->shipping_cost = $ShippingInfo['cost'];
			}
			if(!isset($_POST['estimateOrder'])){
				if ($TrackMethod == 'barcode'){
                                        if($this->consumptionAllowed() === '1' && isset($barcode)){
                                            $Reservation->barcode_id = $barcode;
                                        }
                                        else{
                                            $barId = $this->getAvailableBarcode($ProductObj, $excludedBarcode, $allInfo['usableBarcodes']);
                                            if($barId != -1){
                                                    $Reservation->barcode_id = $barId;
                                            }
                                            else{
                                                    $Editor->addErrorMessage('si'.$ResInfo['bar_id'].'Reservation already taken for the date. Please reselect');
                                            }
                                        }
					$excludedBarcode[] = $Reservation->barcode_id;
					$Reservation->ProductsInventoryBarcodes->status = 'R';
				}elseif ($TrackMethod == 'quantity'){
					$qtyId = $this->getAvailableQuantity($ProductObj, $excludedQuantity);
					if($qtyId != -1){
						$Reservation->quantity_id = $qtyId;
					}else{
						$Editor->addErrorMessage('Reservation already taken for the date. Please reselect');
					}
					$excludedQuantity[] = $Reservation->quantity_id;
					$Reservation->ProductsInventoryQuantity->available -= 1;
					$Reservation->ProductsInventoryQuantity->reserved += 1;
				}
			}
			EventManager::notify('ReservationOnInsertOrderedProduct', $Reservation, &$ProductObj);

			$Reservations->add($Reservation);
		}
	}
	public function getBookedDaysArrayNew($starting, $qty, &$reservArr, &$bookedDates, $newReservations){
		$bookingsArr = array();

		foreach($this->getResArr() as $productId1 => $val1){
			foreach($val1 as $ptype1 => $qtyDate2){
				foreach($qtyDate2 as $Type => $qtyDate1){
					foreach($qtyDate1 as $date1 => $qty1){
						$timeDateParseStart = date('Y-n-j', strtotime($date1));

						if($Type == 'days'){
							if(/*$this->maxQty -*/ $qty1 - $qty < 0){
								$bookingsArr[] = $timeDateParseStart;
							}
						}
					}
				}
			}
		}
		if($this->getMaxQty() < $qty){
			return false;
		}
		return $bookingsArr;
	}

	public function processAddToCartNew(&$pInfo, $resInfo){
		$shippingInfo = array(
			'',
			''
		);
		if (isset($resInfo['rental_shipping']) && $resInfo['rental_shipping'] !== false){
			$shippingInfo = explode('_', $resInfo['rental_shipping']);
		}
		$dataArray = array(
			'shipping_module' => (isset($shippingInfo[0])?$shippingInfo[0]:''),
			'shipping_method' => (isset($shippingInfo[1])?$shippingInfo[1]:''),
			'start_date'      => $resInfo['start_date'],
			'end_date'        => $resInfo['end_date'],
			'days_before'     => $resInfo['days_before'],
			'days_after'      => $resInfo['days_after'],
			'quantity'        => $resInfo['quantity'],
            'bar_id'          => $resInfo['bar_id']
		);
		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'True'){
			$dataArray['event_name'] = $resInfo['event_name'];
			$dataArray['event_date'] = $resInfo['event_date'];
			if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_GATES') == 'True'){
				if(isset($resInfo['event_gate'])){
					$dataArray['event_gate'] = $resInfo['event_gate'];
				}
			}
		}
		EventManager::notify('SaveResInfoOrderCreatorNew', &$dataArray, $resInfo);
		$this->processAddToOrderOrCart($dataArray, $pInfo);

		EventManager::notify('ReservationProcessAddToCart', $pInfo['reservationInfo']);
	}

}
?>