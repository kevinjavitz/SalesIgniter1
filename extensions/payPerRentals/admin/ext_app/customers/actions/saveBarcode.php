<?php
	$total = 0;
	if (!isset($_GET['ignoreConflict'])){
		$Qcheck = Doctrine_Query::create()
		->select('count(*) as total')
		->from('OrdersProductsReservation')
		->where('products_barcode = ?', $_GET['newBarcode'])
		->andWhere('start_date between "' . $_GET['curStart'] . '" and "' . $_GET['curEnd'] . '"')
		->andWhere('(rental_state = "out" || rental_state = "reserved")')
		->execute();
		if ($Qcheck){
			$total = $Qcheck[0]['total'];
		}
	}

	if ($total <= 0){
		Doctrine_Query::create()
		->update('OrdersProductsReservation')
		->set('products_barcode = ?', $_GET['newBarcode'])
		->where('rental_booking_id = ?', $_GET['rID'])
		->execute();
	}
	
	EventManager::attachActionResponse(array(
		'success'    => ($total > 0 ? 'false' : 'true'),
		'newBarcode' => $_GET['newBarcode']
	), 'json');
?>