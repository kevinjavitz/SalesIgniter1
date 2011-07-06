<?php
	$oID = $_GET['oID'];
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
	->andWhere('o.orders_status = ?', sysConfig::get('ORDERS_STATUS_ESTIMATE_ID'))
	->andWhere('oa.address_type = ?', 'customer')
	->andWhere('parent_id IS NULL');

	$Qorders = $QOrdersQuery->execute();
	foreach($Qorders as $oInfo){
		foreach($oInfo->OrdersProducts as $opInfo){
			$opInfo->OrdersProductsReservation->delete();//delete OrdersProducts to?
		}
		$oInfo->delete();
		$messageStack->addSession('pageStack', 'The estimate has been deleted.', 'success');
	}

	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>