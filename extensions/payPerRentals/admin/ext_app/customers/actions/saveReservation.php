<?php
	/*$bookingId = $_POST['reservation_id'];
	$barcode = $_POST['barcode'];
	$startDate = $_POST['start_date'];
	$endDate = $_POST['end_date'];
	$shippingMethod = $_POST['shipping'];
	$rentalState = $_POST['rental_state'];
	$productId = (int)$_POST['products_id'];

	require('../includes/classes/product.php');
	$product = new product($productId);
	
	$RentalBooking = Doctrine_Core::getTable('OrdersProductsReservation')->findOneByOrdersProductsReservationId($bookingId);
	if ($startDate == $RentalBooking->start_date && $endDate == $RentalBooking->end_date){
		$RentalBooking->products_barcode = $barcode;
		$findBarcode = false;
	}else{
		$findBarcode = true;
	}
	$RentalBooking->start_date = $startDate;
	$RentalBooking->end_date = $endDate;
	$RentalBooking->shipping_method = $shippingMethod;
	$RentalBooking->rental_state = $rentalState;
	$RentalBooking->products_id = $productId;
	
	if ($findBarcode === true){
		$Qtotal = Doctrine_Query::create()
		->select('count(*) as total')
		->from('OrdersProductsReservation')
		->where('barcode_id = ?', $barcode)
		->andWhere('((start_date between CAST("' . $startDate . '" as DATE) and CAST("' . $endDate . '" as DATE)) or (end_date between CAST("' . $startDate . '" as DATE) and CAST("' . $endDate . '" as DATE)))')
		->andWhere('(rental_state = "out" || rental_state = "reserved")')
		->andWhere('rental_booking_id != ?', $bookingId)
		->execute();
		if ($Qtotal && $Qtotal[0]['total'] > 0){
			$barcodes = $product->getInventoryItems();
			foreach($barcodes as $bInfo){
				$Qtotal = Doctrine_Query::create()
				->select('count(*) as total')
				->from('RentalBookings')
				->where('barcode_id = ?', $bInfo['id'])
				->andWhere('((start_date between CAST("' . $startDate . '" as DATE) and CAST("' . $endDate . '" as DATE)) or (end_date between CAST("' . $startDate . '" as DATE) and CAST("' . $endDate . '" as DATE)))')
				->andWhere('(rental_state = "out" || rental_state = "reserved")')
				->andWhere('rental_booking_id != ?', $bookingId)
				->execute();
				if ($Qtotal && $Qtotal[0]['total'] <= 0){
					$RentalBooking->products_barcode = $bInfo['id'];
					break;
				}
			}
		}
	}
	
	$RentalBooking->save();
	
	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');*/
?>