<?php


foreach($_POST['barcode_replacement'] as $resId => $barcode){
		$Qres = Doctrine_Core::getTable('OrdersProductsReservation')->find($resId);
		$QBarcode = Doctrine_Query::create()
		->from('ProductsInventoryBarcodes')
		->andWhere('barcode = ?', $barcode)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if($QBarcode){
			$Qres->barcode_id = $QBarcode[0]['barcode_id'];
			$Qres->save();
		}
}

$Qreservations = Doctrine_Query::create()
	->from('OrdersProductsReservation opr')
	->leftJoin('opr.OrdersProducts op')
	->leftJoin('op.Orders o')
	->leftJoin('o.Customers c')
	->leftJoin('o.OrdersAddresses oa')
	->leftJoin('opr.ProductsInventoryBarcodes ib')
	->leftJoin('ib.ProductsInventory i')
	->leftJoin('opr.ProductsInventoryQuantity iq')
	->leftJoin('iq.ProductsInventory i2')
	->whereIn('opr.orders_products_reservations_id', (isset($_POST['sendRes'])?$_POST['sendRes']:array()))
	->andWhere('oa.address_type = "customer" or oa.address_type is null')
	->andWhere('opr.parent_id IS NULL')
	->execute();

	if ($Qreservations->count() > 0){
		foreach($Qreservations as $oprInfo){
			if(!is_null($oprInfo['orders_products_id']) && $oprInfo['orders_products_id'] > 0){
				$opInfo = $oprInfo->OrdersProducts;
				$oInfo = $opInfo->Orders;
				$finalPrice = $opInfo['final_price'];
				$productName = $opInfo->products_name;
				$customerEmailAddress = $oInfo->customers_email_address;
				$fullName = $oInfo->OrdersAddresses['customer']->entry_name;
			}else{
				$finalPrice = 0;
				$QQueue = Doctrine_Query::create()
					->from('QueueProductsReservation qpr')
					->leftJoin('qpr.PayPerRentalQueueToReservations pprqr')
					->where('pprqr.orders_products_reservations_id = ?', $oprInfo['orders_products_reservations_id'])
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
				$productName = $QQueue[0]['products_name'];
				$QCustomer = Doctrine_Query::create()
					->from('Customers c')
					->where('customers_id = ?', $QQueue[0]['customers_id'])
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
				$fullName = $QCustomer[0]['customers_firstname'].' '. $QCustomer[0]['customers_lastname'];
				$customerEmailAddress = $QCustomer[0]['customers_email_address'];
			}

			//foreach($oInfo->OrdersProducts as $opInfo){
			//	foreach($opInfo->OrdersProductsReservation as $oprInfo){

					if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_PROCESS_SEND') == 'True'){
						$payedAmount = (isset($_POST['amount_payed'][$oprInfo->orders_products_reservations_id])?$_POST['amount_payed'][$oprInfo->orders_products_reservations_id]:'');

						if($payedAmount - $finalPrice >= 0){

						}else{
							continue;
						}
					}

					$shippingNumber = (isset($_POST['shipping_number'][$oprInfo->orders_products_reservations_id])?$_POST['shipping_number'][$oprInfo->orders_products_reservations_id]:'');
					if(!empty($shippingNumber)){
						if(preg_match('/\b(1Z ?[0-9A-Z]{3} ?[0-9A-Z]{3} ?[0-9A-Z]{2} ?[0-9A-Z]{4} ?[0-9A-Z]{3} ?[0-9A-Z]|[\dT]\d\d\d ?\d\d\d\d ?\d\d\d)\b/i', $shippingNumber)){
							$shippingURL = '<a href="http://wwwapps.ups.com/etracking/tracking.cgi?InquiryNumber2=&InquiryNumber3=&InquiryNumber4=&InquiryNumber5=&TypeOfInquiryNumber=T&UPS_HTML_Version=3.0&IATA=us&Lang=en&submit=Track+Package&InquiryNumber1='.$shippingNumber.'">Track Order</a>';//UPS
							$oprInfo->tracking_number = $shippingNumber;
							$oprInfo->tracking_type = 'UPS';
							//$oInfo->ups_track_num = $shippingNumber;
						}elseif(preg_match('/\b((96\d\d\d\d\d ?\d\d\d\d|96\d\d) ?\d\d\d\d ?d\d\d\d( ?\d\d\d)?)\b/i', $shippingNumber)){
							$shippingURL = '<a href="http://www.fedex.com/Tracking?action=track&language=english&cntry_code=us&tracknumbers='.$shippingNumber.'">Track Order</a>';//FEDEX
							//$oInfo->fedex_track_num = $shippingNumber;
							$oprInfo->tracking_number = $shippingNumber;
							$oprInfo->tracking_type = 'FEDEX';
						}elseif(preg_match('/\b(91\d\d ?\d\d\d\d ?\d\d\d\d ?\d\d\d\d ?\d\d\d\d ?\d\d|91\d\d ?\d\d\d\d ?\d\d\d\d ?\d\d\d\d ?\d\d\d\d)\b/i', $shippingNumber)){
							$shippingURL = '<a href="http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do?origTrackNum='.$shippingNumber.'">Track Order</a>';//USPS
							//$oInfo->usps_track_num = $shippingNumber;
							$oprInfo->tracking_number = $shippingNumber;
							$oprInfo->tracking_type = 'USPS';
						}else{
							$shippingURL = '<a href="http://track.dhl-usa.com/atrknav.asp?action=track&language=english&cntry_code=us&ShipmentNumber='.$shippingNumber.'">Track Order</a>';//DHL-starts with JD
							//$oInfo->dhl_track_num = $shippingNumber;
							$oprInfo->tracking_number = $shippingNumber;
							$oprInfo->tracking_type = 'DHL';
						}
					}

					$oprInfo->rental_state = 'out';
					$oprInfo->date_shipped = date('Y-m-d');

					if ($oprInfo->track_method == 'barcode'){
						$oprInfo->ProductsInventoryBarcodes->status = 'O';
					}elseif ($oprInfo->track_method == 'quantity'){
						$oprInfo->ProductsInventoryQuantity->reserved -= 1;
						$oprInfo->ProductsInventoryQuantity->qty_out += 1;
					}
					if (isset($customerEmailAddress)){
						$emailEvent = new emailEvent('reservation_sent');

						$emailEvent->setVars(array(
							'full_name' => $fullName,
							'rented_product' => $productName,
							'due_date' => tep_date_long($oprInfo->end_date),
	 						'shipping_number'=> (isset($shippingURL)?$shippingURL:''),
							'email_address' => $customerEmailAddress
						));

						$emailEvent->sendEmail(array(
							'email' => $customerEmailAddress,
							'name' => $fullName
						));
					}
				}

			//}
		//}
		$Qreservations->save();
	}



	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>