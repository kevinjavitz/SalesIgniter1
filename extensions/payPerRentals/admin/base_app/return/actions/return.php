<?php
	$msgError = array();
	$msgSuccess = array();

	$rentals = (isset($_POST['rental']) ? $_POST['rental'] : '');
	$damaged = (isset($_POST['damaged']) ? $_POST['damaged'] : array());
	$lost = (isset($_POST['lost']) ? $_POST['lost'] : array());
	$comment = (isset($_POST['comment']) ? $_POST['comment'] : array());
	$error = false;

	if (!is_array($rentals) || sizeof($rentals) <= 0){
		$error = true;
		$messageStack->addSession('pageStack', sysLanguage::get('TEXT_NO_BARCODE_ENTERED'), 'error');
	}

	if ($error === false){
		foreach($rentals as $bookingID => $info){
			$status = 'A';
			if (isset($damaged[$bookingID])) $status = 'B';
			if (isset($lost[$bookingID])) $status = 'L';		    
			ReservationUtilities::returnReservation(
				$bookingID,
				$status,
				(isset($comment[$bookingID]) ? $comment[$bookingID] : ''),
				(isset($lost[$bookingID]) ? '1' : '0'),
				(isset($damaged[$bookingID]) ? '1' : '0')
			);

			$Qcheck = Doctrine_Query::create()
			->from('OrdersProductsReservation')
			->where('parent_id = ?', $bookingID)
			->execute();
			if ($Qcheck !== false){
				foreach($Qcheck->toArray() as $childBooking){
					$packBookingID = $childBooking['orders_products_reservations_id'];

					$status = 'A';
					if (isset($damaged[$packBookingID])) $status = 'B';
					if (isset($lost[$packBookingID])) $status = 'L';

					ReservationUtilities::returnReservation(
						$packBookingID,
						$status,
						(isset($comment[$packBookingID]) ? $comment[$packBookingID] : ''),
						(isset($lost[$packBookingID]) ? '1' : '0'),
						(isset($damaged[$packBookingID]) ? '1' : '0')
					);
				}
			}
		}
		$messageStack->addSession('pageStack', sysLanguage::get('TEXT_SUCCESS_MOVIES_RETURNED'), 'success');
	}
	
	EventManager::attachActionResponse(itw_app_link('appExt=payPerRentals', 'return', 'default'), 'redirect');
?>