<?php
	if (isset($_GET['rID'])){
		$rID = $_GET['rID'];
	}else{
		$rID = 0;
	}

	$status = '1';
	$startDate = date('Y-m-d');
	$endDate = date('Y-m-d');
	$customerId = 0;

	if ($_GET['type'] == 'reservation'){
		$header = '<br/>';
		$windowTitle = 'Edit Reservation';
		if ($rID > 0){
			$orderProductsReservation = Doctrine_Core::getTable('OrdersProductsReservation')->find($rID);
			$status = $orderProductsReservation->rental_status_id;
			$startDate = $orderProductsReservation->start_date;
			$endDate = $orderProductsReservation->end_date;
			$Qorders = Doctrine_Query::create()
						->from('OrdersProductsReservation ops')
						->leftJoin('ops.OrdersProducts op')
						->leftJoin('op.Orders o')
						->where('ops.orders_products_reservations_id = ?', $rID)
						->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if (count($Qorders) > 0){
				$oID = $Qorders[0]['OrdersProducts']['Orders']['orders_id'];
				if($oID){
					$header .= 'Order ID: <a target=\'_blank\' href=\'' . itw_app_link('oID='. $oID, 'orders', 'details') . '\'>'. $oID . '(click to view order)</a><br/>';
				}else{
					$header .= 'Added by admin. Not in any order';
				}
			}
		}

	}else {
		$header = '<br/>';
		$windowTitle = 'Edit Rental Membership';
			if ($rID > 0){
			$rentedProducts = Doctrine_Core::getTable('RentedProducts')->find($rID);
			$status = $rentedProducts->rental_status_id;
			$startDate = $rentedProducts->date_added;
			$endDate = $rentedProducts->return_date;
			if ($endDate == '0000-00-00'){
				$endDate = '';
			}
			$customerId = $rentedProducts->customers_id;
		}
	}

	$json = array(
			'success' => true,
			'status'  => $status,
			'startDate' => $startDate,
			'endDate'   => $endDate,
			'customer'   => $customerId,
			'type'  => $_GET['type'], 
			'header'    => $header,
			'title' => $windowTitle
	);

	EventManager::attachActionResponse($json, 'json');
?>