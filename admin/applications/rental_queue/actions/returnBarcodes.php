<?php
	$oneTimeToSend = array();
	$rentalToSend = array();
	$returnedRentals = array();

	$error = true;
	$returnBarcodes = array();
	for($i=0, $n=sizeof($_POST['barcode']); $i<$n; $i++){
		if (!empty($_POST['barcode'][$i])){
			$error = false;
			$returnBarcodes[] = array(
				'barcode' => $_POST['barcode'][$i],
				'broken'  => (isset($_POST['broken'][$i]) ? $_POST['broken'][$i] : 0),
				'comment' => $_POST['comment'][$i]
			);
		}
	}

	if ($error === true){
		$messageStack->addSession('pageStack', sysLanguage::get('TEXT_ERROR_NO_BARCODE_ENTERED'), 'error');
	}else{
		$msgError = array();
		$msgSuccess = array();
		$msgWarning = array();
		for($i=0;$i<sizeof($returnBarcodes);$i++){
			$error = false;
			$Qbarcode = Doctrine_Query::create()
			->select('i.type, b.barcode_id')
			->from('ProductsInventory i')
			->leftJoin('i.ProductsInventoryBarcodes b')
			->where('b.barcode = ?', $returnBarcodes[$i]['barcode'])
			->fetchOne();
			if ($Qbarcode === false){
				$msgWarning[] = sprintf(
				sysLanguage::get('TEXT_BARCODE_NOT_RECOGNIZED'),
				$returnBarcodes[$i]['barcode']
				);
				continue;
			}

			$inventory = $Qbarcode->toArray(true);
			$barcode = $inventory['ProductsInventoryBarcodes'][0];

			$isReservation = false;
			if ($inventory['type'] == 'reservation'){
				$isReservation = true;
				$Qbooking = Doctrine_Query::create()
				->from('RentalBookings')
				->where('barcode_id = ?', $barcode['barcode_id'])
				->andWhere('rental_state = ?', 'out')
				->andWhere('parent_id is null')
				->fetchOne();
				if ($Qbooking === false){
					$error = true;
				}else{
					$booking = $Qbooking->toArray();

					$RentalBooking = Doctrine_Core::getTable('RentalBookings')
					->findOneByRentalBookingId($booking['rental_booking_id']);

					$RentalBooking->rental_state = 'returned';
					$RentalBooking->date_returned = date('Y-m-d h:i:s');
					$RentalBooking->lost = '0';
					$RentalBooking->damaged = $returnBarcodes[$i]['broken'];
					$RentalBooking->save();

					$QpackageProducts = Doctrine_Query::create()
					->from('RentalBookings')
					->where('parent_id = ?', $booking['rental_booking_id'])
					->execute();
					if ($QpackageProducts !== false){
						foreach($QpackageProducts->toArray() as $packProduct){
							$RentalBooking = Doctrine_Core::getTable('RentalBookings')
							->findOneByRentalBookingId($packProduct['rental_booking_id']);

							$RentalBooking->rental_state = 'returned';
							$RentalBooking->date_returned = date('Y-m-d h:i:s');
							$RentalBooking->lost = '0';
							$RentalBooking->damaged = $returnBarcodes[$i]['broken'];
							$RentalBooking->save();

							if ($packProduct['track_method'] == 'barcode'){
								if (isset($returnBarcodes[$i]['comment']) && !empty($returnBarcodes[$i]['comment'])){
									$Comment = new ProductsInventoryBarcodesComments();
									$Comment->barcode_id = $packProduct['barcode_id'];
									$Comment->comments = $returnBarcodes[$i]['comment'];
									$Comment->save();
								}

								$status = 'A';
								if (!empty($returnBarcodes[$i]['broken']) && $returnBarcodes[$i]['broken'] == 1){
									$status = 'B';
								}
								Doctrine_Query::create()
								->update('ProductsInventoryBarcodes')
								->set('status', '?', $status)
								->where('barcode_id = ?', $packProduct['barcode_id'])
								->execute();
							}
						}
					}

					$customerId = $booking['customers_id'];
					$productsId = $booking['products_id'];
					$productsName = tep_get_products_name($productsId);
				}
			}else{
				$Qcheck = Doctrine_Query::create()
				->select('customers_queue_id, customers_id, date_added, products_id')
				->from('RentedQueue')
				->where('products_barcode = ?', $barcode['barcode_id'])
				->fetchOne();
				if ($Qcheck === false){
					$error = true;
				}else{
					$rentedQueue = $Qcheck->toArray(true);

					$RentedProduct = Doctrine_Core::getTable('RentedProducts')
					->findOneByRentedProductsId($rentedQueue['customers_queue_id']);

					if (!empty($returnBarcodes[$i]['broken']) && $returnBarcodes[$i]['broken'] == 1){
						$RentedProduct->broken = '0';
					} else {
						$RentedProduct->broken = '1';
					}
					$RentedProduct->save();

					Doctrine_Query::create()
					->delete('RentedQueue')
					->where('products_barcode = ?', $barcode['barcode_id'])
					->execute();

					$customerId = $rentedQueue['customers_id'];
					$productsId = $rentedQueue['products_id'];
					$productsName = tep_get_products_name($productsId);
				}
			}

			if ($error === true){
				$msgError[] = sprintf(
					sysLanguage::get('TEXT_BARCODE_NOT_RENTED'),
					$returnBarcodes[$i]['barcode']
				);
			}else{
				$msgSuccess[] = sprintf(
					sysLanguage::get('TEXT_PRODUCT_RETURNED'),
					tep_get_products_name($productsId),
					$returnBarcodes[$i]['barcode']
				);

				if (isset($returnBarcodes[$i]['comment']) && !empty($returnBarcodes[$i]['comment'])){
					$Comment = new ProductsInventoryBarcodesComments();
					$Comment->barcode_id = $barcode['barcode_id'];
					$Comment->comments = $returnBarcodes[$i]['comment'];
					$Comment->save();
				}

				$status = 'A';
				if (!empty($returnBarcodes[$i]['broken']) && $returnBarcodes[$i]['broken'] == 1){
					$status = 'B';
				}
				Doctrine_Query::create()
				->update('ProductsInventoryBarcodes')
				->set('status', '?', $status)
				->where('barcode_id = ?', $barcode['barcode_id'])
				->execute();

				$Qcustomer = Doctrine_Query::create()
				->select('customers_firstname, customers_lastname, customers_email_address, language_id')
				->from('Customers')
				->where('customers_id = ?', $customerId)
				->fetchOne();
				if ($Qcustomer !== false){
					$customer = $Qcustomer->toArray(true);
					$emailEvent = new emailEvent(null, $customer['language_id']);
					
					if ($isReservation){
						$emailEvent->setEvent('reservation_returned');
					}else{
						$emailEvent->setEvent('rental_returned');
					}
					
					$emailEvent->setVars(array(
						'firstname' => $customer['customers_firstname'],
						'lastname' => $customer['customers_lastname'],
						'full_name' => $customer['customers_firstname'] . ' ' . $customer['customers_lastname'],
						'email_address' => $customer['customers_email_address'],
						'rented_product' => $productsName
					));

					if ($isReservation === true){
						$nowTime = mktime(0,0,0);
						$endArr = date_parse($booking['end_date']);
						$endTime = mktime(0,0,0,$endArr['month'],$endArr['day'],$endArr['year']);
						if ($nowTime > $endTime){
							$days_late = ($nowTime - $endTime) / (60 * 60 * 24);
						}else{
							$days_late = 0;
						}
						$emailEvent->setVar('days_late', $days_late);
					}

					$emailEvent->sendEmail(array(
						'email' => $customer['customers_email_address'],
						'name'  => $customer['customers_firstname'] . ' ' . $customer['customers_lastname']
					));
				}
			}
		}

		if (isset($msgError) && sizeof($msgError) > 0){
			$messageStack->addSessionMultiple('pageStack', $msgError, 'error', 'ordered_list');
		}

		if (isset($msgSuccess) && sizeof($msgSuccess) > 0){
			$messageStack->addSessionMultiple('pageStack', $msgSuccess, 'success');
		}

		if (isset($msgWarning) && sizeof($msgWarning) > 0){
			$messageStack->addSessionMultiple('pageStack', $msgWarning, 'warning');
		}
	}

	EventManager::attachActionResponse(itw_app_link(null, 'rental_queue', 'return_barcode'), 'redirect');
?>