<?php
	require(DIR_WS_CLASSES . 'rental_queue.php');
	require('../includes/classes/product.php');
	$processed = array(
		'noneInQueue' => array(),
		'noInventory' => array()
	);
	
	function autoSendRentals(){
		global $userAccount, $processed, $appExtension, $messageStack;
		$rentalsSent = false;
		$returnInfo = array(
			'noneInQueue' => 0,
			'sentFromQueue' => 0
		);
		$Customers = Doctrine_Query::create()
		->select('customers_id')
		->from('CustomersMembership')
		->where('ismember', 'M')
		->andWhere('activate', 'Y')
		->execute();
		foreach($Customers->toArray() as $customer){
			if (in_array($customer['customers_id'], $processed['noneInQueue'])) continue;

			$userAccount = new rentalStoreUser($customer['customers_id']);
			$userAccount->loadPlugins();
			$membership =& $userAccount->plugins['membership'];
			if ($membership->isRentalMember()){
				$addressBook =& $userAccount->plugins['addressBook'];

				$rentalQueue = new rentalQueue_admin($customer['customers_id']);

				$totalRented = $rentalQueue->count_rented();
				$totalCanSend = $membership->getRentalsAllowed() - $totalRented;
				if ($totalCanSend > 0){
					if ($rentalQueue->isEmpty()){
						$rentalsSent = true;
						$returnInfo['noneInQueue']++;
						$email_event = new emailEvent('rental_queue_empty', $userAccount->getLanguageId());
						$email_event->sendEmail(array(
							'name'  => $userAccount->getFullName(),
							'email' => $userAccount->getEmailAddress()
						));

						$processed['noneInQueue'][] = $customer['customers_id'];
					}else{
						$errorAdd = '';
						if ($appExtension->isEnabled('inventoryCenters')){
							$centerID = $addressBook->getAddressInventoryCenter($membership->getRentalAddressId());
							$errorAdd .= '<br />Inventory Center ID: ' . (int) $centerID;
						}
						$products = $rentalQueue->getProducts();
						for($i=0, $n=sizeof($products); $i<$n; $i++){
							$barcodeId = false;
							$purchaseTypeCls = $products[$i]['productClass']->getPurchaseType('rental');
							$productInv =& $purchaseTypeCls->inventoryCls->invMethod->trackMethod;
							$productInv->invUnavailableStatus = array(
								'B',
								'O',
								'P',
								'R'
							);
							if (isset($centerID)){
								$productInv->invData['useCenterId'] = $centerID;
							}
							$invItems = $purchaseTypeCls->getInventoryItems();

							if (is_array($invItems) && sizeof($invItems) > 0){
								foreach($invItems as $invItem){
									if (isset($centerID) && isset($invItem['center_id'])){
										if (isset($invItem['center_id']) && $invItem['center_id'] == $centerID){
											$barcodeId = $invItem['id'];
											break;
										}
									}else{
										$barcodeId = $invItem['id'];
										break;
									}
								}
								$errorAdd .= '<br />Total Inventory Checked: ' . sizeof($invItems);
							}else{
								$errorAdd .= '<br />Total Inventory Checked: N/A';
							}

							if ($barcodeId !== false){
								$rentalsSent = true;
								$returnInfo['sentFromQueue']++;

								$QproductsQueue = Doctrine_Query::create()
								->select('customers_queue_id, customers_id, products_id, date_added')
								->from('RentalQueueTable')
								->where('customers_id = ?', $customer['customers_id'])
								->andWhere('products_id = ?', $products[$i]['id'])
								->fetchOne();

								$rentalQueue->incrementTopRentals($products[$i]['id']);

								$shipmentDate = date('Y-m-d');
								$arrivalDate = date('Y-m-d', mktime(0,0,0,date('m'),date('d')+RENTAL_QUEUE_DAYS_INTERVAL,date('Y')));

								$NewRentedQueue = new RentedQueue();
								$NewRentedQueue->customers_id = $QproductsQueue['customers_id'];
								$NewRentedQueue->products_id = $QproductsQueue['products_id'];
								$NewRentedQueue->products_barcode = $barcodeId;
								$NewRentedQueue->shipment_date = $shipmentDate;
								$NewRentedQueue->arrival_date = $arrivalDate;
								$NewRentedQueue->save();

								$rentedProductId = $NewRentedQueue->customers_queue_id;

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

								$rentalQueue->removeFromQueue($QproductsQueue['products_id']);

								Doctrine_Query::create()
								->update('ProductsInventoryBarcodes')
								->set('status', '?', 'O')
								->where('barcode_id = ?', $barcodeId)
								->execute();

								$emailEvent = new emailEvent('rental_sent', $userAccount->getLanguageId());
								$emailEvent->setVars(array(
									'firstname'     => $userAccount->getFirstName(),
									'lastname'      => $userAccount->getLastName(),
									'full_name'     => $userAccount->getFullName(),
									'rentedProduct' => tep_get_products_name($QproductsQueue['products_id']),
									'requestDate'   => tep_date_short($QproductsQueue['date_added']),
									'shipmentDate'  => tep_date_short($shipmentDate),
									'arrivalDate'   => tep_date_short($arrivalDate)
								));

								$emailEvent->sendEmail(array(
									'name'  => $full_name,
									'email' => $userAccount->getEmailAddress()
								));

								$rentalQueue->fixPriorities();

								$totalRented = $rentalQueue->count_rented();
								$totalCanSend = $membership->getRentalsAllowed() - $totalRented;
								break;
							}else{
								$messageStack->addSession('pageStack', 'No Barcode Available' . 
									'<br />Customer Name: ' . $userAccount->getFullName() . 
									'<br />Product Name: ' . $products[$i]['productClass']->getName() . 
									$errorAdd
								, 'error');
							}
						}
					}
				}
			}
		}

		if ($rentalsSent === true){
			return $returnInfo;
		}else{
			return false;
		}
	}

	$rentalInfo = array(
		'totalSent' => 0,
		'totalWithoutItems' => 0
	);
	while(($returnInfo = autoSendRentals()) !== false){
		$rentalInfo['totalSent'] += $returnInfo['sentFromQueue'];
		$rentalInfo['totalWithoutItems'] += $returnInfo['noneInQueue'];
	}

	$messageStack->addSession('pageStack', 'Rentals Sent: ' . $rentalInfo['totalSent'] . '<br />Customers That Need Rental Items, But Have No Items In Queue: ' . $rentalInfo['totalWithoutItems'], 'success');

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action'))), 'redirect');
?>