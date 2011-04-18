<?php
	$Order = Doctrine_Core::getTable('Orders')->findOneByOrdersId((int)$_GET['oID']);
	$Order->rental_notes = $_POST['rental_notes'];
	$Order->save();
	$messageStack->addSession('pageStack', sysLanguage::get('SUCCESS_ORDER_RENTAL_NOTES_UPDATED'), 'success');
	if((int)$_POST['status'] == sysConfig::get('ORDERS_STATUS_CANCELLED_ID')){
		$QOrdersQuery = Doctrine_Query::create()
		->from('Orders o')
		->leftJoin('o.OrdersAddresses oa')
		->leftJoin('o.OrdersProducts op')
		->leftJoin('op.OrdersProductsReservation opr')
		->leftJoin('opr.ProductsInventoryBarcodes ib')
		->leftJoin('ib.ProductsInventory ibi')
		->leftJoin('opr.ProductsInventoryQuantity iq')
		->leftJoin('iq.ProductsInventory iqi')
		->where('o.orders_id = ?', $oID)
		->andWhere('oa.address_type = ?', 'customer')
		->andWhere('parent_id IS NULL');

		$Qorders = $QOrdersQuery->execute();
		foreach($Qorders as $oInfo){
			foreach($oInfo->OrdersProducts as $opInfo){
				foreach ($opInfo->OrdersProductsReservation as $oprInfo) {
					$reservationId = $oprInfo->orders_products_reservations_id;
					$trackMethod = $oprInfo->track_method;

					if ($trackMethod == 'barcode') {
						$oprInfo->ProductsInventoryBarcodes->status = 'A';
					} elseif ($trackMethod == 'quantity') {
						$oprInfo->ProductsInventoryQuantity->qty_out--;
						$oprInfo->ProductsInventoryQuantity->available++;
					}
					$oprInfo->save();
				}
				$opInfo->OrdersProductsReservation->delete(); //delete OrdersProducts to?
			}
		}
	}
?>