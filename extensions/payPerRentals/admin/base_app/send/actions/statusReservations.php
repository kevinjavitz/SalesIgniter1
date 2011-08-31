<?php

$Qreservations = Doctrine_Query::create()
	->from('Orders o')
	->leftJoin('o.Customers c')
	->leftJoin('o.OrdersAddresses oa')
	->leftJoin('o.OrdersProducts op')
	->leftJoin('op.OrdersProductsReservation opr')
	->leftJoin('opr.ProductsInventoryBarcodes ib')
	->leftJoin('ib.ProductsInventory i')
	->leftJoin('opr.ProductsInventoryQuantity iq')
	->leftJoin('iq.ProductsInventory i2')
	->whereIn('opr.orders_products_reservations_id', (isset($_POST['sendRes'])?$_POST['sendRes']:array()))
	->andWhere('oa.address_type = ?', 'customer')
	->andWhere('opr.parent_id IS NULL')
	->execute();

	if ($Qreservations->count() > 0){
		foreach($Qreservations as $oInfo){

			foreach($oInfo->OrdersProducts as $opInfo){
				foreach($opInfo->OrdersProductsReservation as $oprInfo){
					$status = (isset($_POST['rental_status'][$oprInfo->orders_products_reservations_id])?$_POST['rental_status'][$oprInfo->orders_products_reservations_id]:'');
					if(!empty($status)){
						$oprInfo->rental_status_id = $status;
					}

				}

			}
		}
		$Qreservations->save();
	}



	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>