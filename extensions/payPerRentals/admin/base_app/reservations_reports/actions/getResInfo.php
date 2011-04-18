<?php
    $type = $_GET['type'];
    $rID = $_GET['rID'];
    $tooltip = '';
    if ($type == 'reservation'){
			$Qorders = Doctrine_Query::create()
					->from('OrdersProductsReservation ops')
					->leftJoin('ops.OrdersProducts op')
					->leftJoin('op.Orders o')
					->where('ops.orders_products_reservations_id = ?', $rID)
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if (count($Qorders) > 0){
				$oID = $Qorders[0]['OrdersProducts']['Orders']['orders_id'];
				$cID = $Qorders[0]['OrdersProducts']['Orders']['customers_id'];
				$orderDate = $Qorders[0]['OrdersProducts']['Orders']['date_purchased'];
				if ($oID && $cID){
					$tooltip .= 'Order ID: '. $oID . '<br/>';
					$tooltip .= 'Order Date: '. $orderDate . '<br/>';       
					$customers = Doctrine_Core::getTable('Customers')->findOneByCustomersId($cID);
					$custName = $customers['customers_firstname'] . " " . $customers['customers_lastname'];
					$tooltip .= 'Customer Name: '. $custName . '<br/>';
				}else{
					$tooltip .= 'Added by admin, not in any order'. '<br/>';
				}
				$reservationStartDate = $Qorders[0]['start_date'];
				$reservationEndDate = $Qorders[0]['end_date'];
				$shipDaysBefore = $Qorders[0]['shipping_days_before'];
				$shipDaysAfter = $Qorders[0]['shipping_days_after'];
				$shipDate = $Qorders[0]['date_shipped'];

				if ($shipDate == '0000-00-00'){
					$shipDate = date('Y-m-d', strtotime('-' . $shipDaysBefore . ' days' ,strtotime($reservationStartDate)));
					$tooltip .= 'Reservation Shipping Date: '. $shipDate . '<br/>';
				}else{
					$tooltip .= 'Reservation Shipped Date: '. $shipDate . '<br/>';
				}
				$tooltip .= 'Shipping Days Before: '. $shipDaysBefore . '<br/>';
				$tooltip .= 'Shipping Days After: '. $shipDaysAfter . '<br/>';
				$tooltip .= 'Reservation Start Date: '. $reservationStartDate . '<br/>';
				$tooltip .= 'Reservation End Date: '. $reservationEndDate . '<br/>';

				$returnedDate = $Qorders[0]['date_returned'];
				if ($returnedDate != '0000-00-00'){
					$tooltip .= 'Reservation Returned Date: '. $returnedDate . '<br/>';
				}else{
					$shipReturnDate = date('Y-m-d', strtotime('+' . $shipDaysAfter . ' days' ,strtotime($reservationEndDate)));
					$tooltip .= 'Reservation Should be returned Date: '. $shipReturnDate . '<br/>';
				}

			}
            unset($Qorders);
		}else if ($type == 'rental'){
		   	  $Qrental = Doctrine_Query::create()
					     ->from('RentedProducts')
					    ->where('rented_products_id = ?', $rID)
						->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			  if (count($Qrental) > 0){
					$cID = $Qrental[0]['customers_id'];
					$customers = Doctrine_Core::getTable('Customers')->findOneByCustomersId($cID);
					$custName = $customers['customers_firstname'] . " " . $customers['customers_lastname'];
					$tooltip .= 'Customer Name: '. $custName . '<br/>';
					$reservationStartDate = $Qrental[0]['date_added'];
					$reservationReturnedDate = $Qrental[0]['return_date'];
					$shipDate = $Qrental[0]['shipment_date'];
				    $arrivalDate = $Qrental[0]['arrival_date'];
				    $tooltip .= 'Date added to queue: ' . $reservationStartDate . '<br/>';
					$tooltip .= 'Date shiped: ' . $shipDate . '<br/>';
				    $tooltip .= 'Shipping Days: ' . sysConfig::get('RENTAL_QUEUE_DAYS_INTERVAL') . '<br/>';
					$tooltip .= 'Arrived at client date: ' . $arrivalDate . '<br/>';
					if ($reservationReturnedDate != '0000-00-00'){
						$tooltip .= 'Date returned: ' . $reservationReturnedDate . '<br/>';
					}else{
						$tooltip .= 'Not yet returned' . '<br/>';
					}

			  }

		}
    $json = array(
			'success' => true,
			'tooltip' => $tooltip
	);

	EventManager::attachActionResponse($json, 'json');
?>