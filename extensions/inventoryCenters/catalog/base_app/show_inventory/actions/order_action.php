<?php
	$type = $_GET['type'];
	$oID = (int)$_GET['order_id'];
	
	if ($type == 'approve'){
		$statusCheck = 'pprove';
	}else{
		$statusCheck = 'ancell';

		//a way to see when the orderproductsreservation rental_state is completed

		$ReservationQuery = Doctrine_Query::create()
		->from('Orders o')
		->leftJoin('o.OrdersAddresses oa')
		->leftJoin('o.OrdersProducts op')
		->leftJoin('op.OrdersProductsReservation opr')
		->leftJoin('opr.ProductsInventoryBarcodes ib')
		->leftJoin('ib.ProductsInventory ibi')
		->leftJoin('opr.ProductsInventoryQuantity iq')
		->leftJoin('iq.ProductsInventory iqi')
		->where('o.orders_id = ?', $oID)
		//->where('opr.start_date <= ?', date('Y-m-d'))
		->andWhere('oa.address_type = ?', 'customer')
		->andWhere('parent_id IS NULL');

		$Reservation = $ReservationQuery->execute();
		foreach($Reservation as $oInfo){
			foreach($oInfo->OrdersProducts as $opInfo){
				foreach($opInfo->OrdersProductsReservation as $oprInfo){
					$reservationId = $oprInfo->orders_products_reservations_id;
					$trackMethod = $oprInfo->track_method;

					if ($trackMethod == 'barcode'){
						$oprInfo->ProductsInventoryBarcodes->status = 'A';
					}elseif ($trackMethod == 'quantity'){
						$oprInfo->ProductsInventoryQuantity->qty_out--;
						if ($status == 'B' || $status == 'L'){
							$oprInfo->ProductsInventoryQuantity->broken++;
						}else{
							$oprInfo->ProductsInventoryQuantity->available++;
						}
					}

				}

				$opInfo->OrdersProductsReservation->delete();//delete OrdersProducts to?
			}
		}
		$Reservation->save();
	}
	
	$QordersStatus = Doctrine_Query::create()
	->from('OrdersStatus s')
	->leftJoin('s.OrdersStatusDescription sd')
	->where('sd.orders_status_name like ?', '%' . $statusCheck . '%')
	->andWhere('sd.language_id = ?', Session::get('languages_id'))
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		
	Doctrine_Query::create()
	->update('Orders')
	->set('orders_status', '?', $QordersStatus[0]['orders_status_id'])
	->where('orders_id = ?', $oID)
	->execute();

	$Qorder = Doctrine_Query::create()
	->select('o.date_purchased, o.customers_email_address, oa.entry_name')
	->from('Orders o')
	->leftJoin('o.OrdersAddresses oa')
	->where('o.orders_id = ?', $oID)
	->andWhere('oa.address_type = ?', 'customer')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	$emailEvent = new emailEvent('order_update_inventory');
	$emailEvent->setVars(array(
		'full_name' => $Qorder[0]['OrdersAddresses'][0]['entry_name'],
		'orderID' => $oID,
		'status' => $QordersStatus[0]['OrdersStatusDescription']['orders_status_name'],
		'datePurchased' => tep_date_long($Qorder[0]['date_purchased'])
	));

	$emailEvent->sendEmail(array(
		'email' => $Qorder[0]['customers_email_address'],
		'name' => $Qorder[0]['OrdersAddresses'][0]['entry_name']
	));
		
	EventManager::attachActionResponse(itw_app_link(null, 'account', 'view_orders'), 'redirect');
?>