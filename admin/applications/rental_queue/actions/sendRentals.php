<?php
	$queueItems = (isset($_POST['queueItem']) ? $_POST['queueItem'] : '');
	$cID = $_GET['cID'];


    if($usePickupRequest){
		$QCustomersToPickupRequest = Doctrine_Query::create()
		->from('PickupRequests pr')
		->leftJoin('pr.CustomersToPickupRequests rptpr')
		->andWhere('pr.start_date >= ?', date('Y-m-d'))
		->andWhere('rptpr.customers_id = ?', $cID)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	    if(isset($QCustomersToPickupRequest[0])){
	        $shipmentDate = $QCustomersToPickupRequest[0]['start_date'];
		    $pShip = date_parse($shipmentDate);
	        $arrivalDate = date('Y-m-d', mktime($pShip['hour'],$pShip['minute'],$pShip['second'],$pShip['month'],$pShip['day']+sysConfig::get('RENTAL_QUEUE_DAYS_INTERVAL'), $pShip['year']));
	    }else{
		    $error = true;
		    $messageStack->addSession('pageStack', 'No Pickup Request for client id: '.$cID, 'error');
	    }
    }else{
	    $shipmentDate = date('Y-m-d');
	    $arrivalDate = date('Y-m-d', mktime(0,0,0,date('m'),date('d')+sysConfig::get('RENTAL_QUEUE_DAYS_INTERVAL'),date('Y')));
    }


	$error = false;
	if (!is_array($queueItems) || sizeof($queueItems) <= 0){
		$error = true;
		$messageStack->addSession('pageStack', sysLanguage::get('TEXT_NO_MOVIE_SELECTED'), 'error');
	}else{
		$totalInQueue = $rentalQueue->count_contents();
		$totalRented = $rentalQueue->count_rented();

		$allowedRentals = $membership->getRentalsAllowed() - $totalRented;
		if (sizeof($queueItems) > $allowedRentals){
			$error = true;
			$customers_name = $userAccount->getFullName();
			$messageStack->addSession('pageStack', sysLanguage::get('TEXT_INFO_TOO_MANY_MOVIES') . ' ' . $customers_name, 'warning');
		}
	}

	if ($error === false){
		foreach($queueItems as $queueID){
			$barcodeId = $_POST['barcode'][$queueID];
			
			$QproductsQueue = Doctrine_Query::create()
			->select('customers_id, products_id, date_added')
			->from('RentalQueueTable')
			->where('customers_queue_id = ?', $queueID)
			->fetchOne();

			$rentalQueue->incrementTopRentals($QproductsQueue['products_id']);

			$NewRenedQueue = new RentedQueue();
			$NewRenedQueue->customers_id = $QproductsQueue['customers_id'];
			$NewRenedQueue->products_id = $QproductsQueue['products_id'];
			$NewRenedQueue->products_barcode = $barcodeId;
			$NewRenedQueue->shipment_date = $shipmentDate;
			$NewRenedQueue->arrival_date = $arrivalDate;
			$NewRenedQueue->save();
							
			$rentedProductId = $NewRenedQueue->customers_queue_id;
			
			/*
			 * @TODO: Does this even need to happen?
			 */
			$NewRentedProduct = new RentedProducts();
			$NewRentedProduct->customers_id = $QproductsQueue['customers_id'];
			$NewRentedProduct->products_id = $QproductsQueue['products_id'];
			$NewRentedProduct->rented_products_id = $rentedProductId;
			$NewRentedProduct->products_barcode = $barcodeId;
			$NewRentedProduct->shipment_date = $shipmentDate;
			$NewRentedProduct->arrival_date = $arrivalDate;
			$NewRentedProduct->save();
			EventManager::notify('RentalQueueProductSent', &$NewRentedProduct, $QproductsQueue);

			$rentalQueue->removeFromQueue($QproductsQueue['products_id']);

			Doctrine_Query::create()
			->update('ProductsInventoryBarcodes')
			->set('status', '?', 'O')
			->where('barcode_id = ?', $barcodeId)
			->execute();

			$emailEvent = new emailEvent('rental_sent', $userAccount->getLanguageId());
			$emailEvent->setVars(array(
				'firstname' => $userAccount->getFirstName(),
				'lastname' => $userAccount->getLastName(),
				'full_name' => $userAccount->getFullName(),
				'rentedProduct' => tep_get_products_name($QproductsQueue['products_id'], $userAccount->getLanguageId()),
				'requestDate' => tep_date_short($QproductsQueue['date_added']),
				'shipmentDate' => tep_date_short($shipmentDate),
				'arrivalDate' => tep_date_short($arrivalDate)
			));

			$emailEvent->sendEmail(array(
				'name'  => $userAccount->getFullName(),
				'email' => $userAccount->getEmailAddress()
			));
		}
		$rentalQueue->fixPriorities();
		$messageStack->addSession('pageStack', sysLanguage::get('TEXT_INFO_MOVIES_RENTED'), 'success');
	}
	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action'))), 'redirect');
?>