<?php

	//deleteForm
	//'Are you sure you want to delete this order?'
	$htmlForm = htmlBase::newElement('div')
				->attr('id', 'deleteForm');

	$oID = $_GET['oID'];
	$checkBoxDeleteReservation = htmlBase::newElement('checkbox')
								->setName('deleteReservationRestock')
								->attr('id', 'deleteReservationRestock')
								->setLabel('Delete reservations')
								->setChecked(true)
								->setValue('1');
	$checkBoxDeleteRestock = htmlBase::newElement('checkbox')
								->setName('deleteRestockNoReservation')
								->attr('id','deleteRestockNoReservation')
								->setLabel('Restock quantity based inventory')
								->setChecked(true)
								->setValue('1');

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
	$isreservation = false;
	$isquantity = false;
	foreach ($Qorders as $oInfo) {
		foreach ($oInfo->OrdersProducts as $opInfo) {

			foreach ($opInfo->OrdersProductsReservation as $oprInfo) {
				$isreservation = true;
			}

			if ($opInfo['purchase_type'] == 'new ' || $opInfo['purchase_type'] == 'used') {
				$isquantity = true;
			}
		}

	}

	if ($isreservation) {
		$htmlForm->append($checkBoxDeleteReservation);
	}

	if ($isquantity) {
		$htmlForm->append($checkBoxDeleteRestock);
	}


	EventManager::attachActionResponse(array(
			'success' => true,
			'html'	=> $htmlForm->draw()
	), 'json');
?>