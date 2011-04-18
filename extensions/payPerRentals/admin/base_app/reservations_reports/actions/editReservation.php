<?php

	if (isset($_GET['rID'])){
		$rID = $_GET['rID'];
	}else{
		$rID = 0;
	}

	$type = $_GET['type'];

	$json = array(
			'success'   => true
	);

	if ($type == 'reservation' && (empty($_POST['start_date_edit']) || empty($_POST['end_date_edit']) || empty($_POST['rental_status_edit']))){
		$json = array(
			'success'   => false
		);
	}

	if ($type == 'rental' && (empty($_POST['date_added_edit']) || empty($_POST['rental_status_edit']) || empty($_POST['customers_edit']))){
		$json = array(
			'success'   => false
		);
	}

	if ($json['success'] === true){
		if($type == 'reservation'){
			if ($rID != 0){
				$orderProductsReservation = Doctrine_Core::getTable('OrdersProductsReservation')->find($rID);
				$orderProductsReservation->start_date = $_POST['start_date_edit'];
				$orderProductsReservation->end_date = $_POST['end_date_edit'];
				$orderProductsReservation->rental_status_id = $_POST['rental_status_edit'];
				$orderProductsReservation->save();
			}else{
                if (isset($_GET['selectedBarcodes']) && is_array($_GET['selectedBarcodes']) && !empty($_GET['selectedBarcodes'][0])){
                    $selectedBarcodes = explode(',', $_GET['selectedBarcodes'][0]);
                    if (is_array($selectedBarcodes)){
                        foreach ($selectedBarcodes as $barcodeId){
                            /*check if inventory exists for the barcode_id for the start_end dates*/
                            $bookingInfo = array(
                                'item_type' => 'barcode',
                                'item_id'   => $barcodeId
                            );

                            $startDate = date_parse($_POST['start_date_edit']);
                            $endDate = date_parse($_POST['end_date_edit']);
                            $bookingInfo['start_date'] = mktime(
                                $startDate['hour'],
                                $startDate['minute'],
                                $startDate['second'],
                                $startDate['month'],
                                $startDate['day'],
                                $startDate['year']
                            );
                            $bookingInfo['end_date'] = mktime(
                                $endDate['hour'],
                                $endDate['minute'],
                                $endDate['second'],
                                $endDate['month'],
                                $endDate['day'],
                                $endDate['year']
                            );

                            $bookingInfo['quantity'] = 1;
                            $bookingInfo['shipping_days_before'] = 0;
                            $bookingInfo['shipping_days_after'] = 0;
                            $QProduct = Doctrine_Query::create()
                                        ->select('pi.products_id, pib.barcode')
                                        ->from('ProductsInventory pi')
                                        ->leftJoin('pi.ProductsInventoryBarcodes pib')
                                        ->where('pib.barcode_id = ?', $barcodeId)
                                        ->limit(1)
                                        ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
                            //$productClass = new product($QProduct[0]['products_id']);
                            //$QProduct->free();
                            //$purchaseTypeClass = $productClass->getPurchaseType('reservation');
                            //print_r($bookingInfo);
                            $numBookings = ReservationUtilities::CheckBooking($bookingInfo);

                            /*end dates*/
                            if ($numBookings == 0){
                                $orderProductsReservation = new OrdersProductsReservation();
                                $orderProductsReservation->start_date = $_POST['start_date_edit'];
                                $orderProductsReservation->end_date = $_POST['end_date_edit'];
                                $orderProductsReservation->track_method = 'barcode';
                                $orderProductsReservation->barcode_id = $barcodeId;
                                $orderProductsReservation->rental_state = 'out';
                                $orderProductsReservation->rental_status_id = $_POST['rental_status_edit'];
                                $orderProductsReservation->save();
                                $orderProductsReservation->free(true);
                            }else{
                                $messageStack->addSession('pageStack',sprintf('Barcode %s is reserved for the selected dates', $QProduct[0]['ProductsInventoryBarcodes'][0]['barcode']), 'error');
                            }
                            unset($bookingInfo);
                        }
                    }
                }else{
                     $orderProductsReservation = new OrdersProductsReservation();
                     $orderProductsReservation->start_date = $_POST['start_date_edit'];
                     $orderProductsReservation->end_date = $_POST['end_date_edit'];
                     $orderProductsReservation->track_method = 'barcode';
                     $orderProductsReservation->barcode_id = $_GET['barcode_id'];
                     $orderProductsReservation->rental_state = 'out';
                     $orderProductsReservation->rental_status_id = $_POST['rental_status_edit'];
                     $orderProductsReservation->save();
                     $orderProductsReservation->free(true);
                }
			}
		}else{
			if ($rID != 0){
				$rentedProducts = Doctrine_Core::getTable('RentedProducts')->find($rID);
				$rentedProducts->customers_id = $_POST['customers_edit'];
				$rentedProducts->date_added = $_POST['date_added_edit'];
				$rentedProducts->return_date = $_POST['return_date_edit'];
				$rentedProducts->rental_status_id = $_POST['rental_status_edit'];
				$rentedProducts->save();
				//add rental queue table reference
			}else{
				$rentedProducts = new RentedProducts();
				$rentedProducts->customers_id = $_POST['customers_edit'];
				$rentedProducts->date_added = $_POST['date_added_edit'];
				$rentedProducts->shipment_date = $_POST['date_added_edit'];
				$rentedProducts->arrival_date = date('Y-m-d',strtotime('+' . sysConfig::get('RENTAL_QUEUE_DAYS_INTERVAL') .' days', strtotime($_POST['date_added_edit'])));
				$rentedProducts->return_date = $_POST['return_date_edit'];
				$rentedProducts->rental_status_id = $_POST['rental_status_edit'];
				$rentedProducts->products_barcode = $_GET['barcode_id'];
				$rentedProducts->products_id = $_GET['products_id'];				
				$rentedProducts->save();
				//add rental queue table reference
			}
		}
		 $json = array(
			'success'   => true
		);
	}


	EventManager::attachActionResponse($json, 'json');

?>