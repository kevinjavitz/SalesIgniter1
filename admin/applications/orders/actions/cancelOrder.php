<?php
    $oID = $_GET['oID'];
	$QOrdersQuery = Doctrine_Query::create()
	->from('Orders o')
	->leftJoin('o.OrdersAddresses oa')
	->leftJoin('o.OrdersProducts op')
	->leftJoin('op.ProductsInventoryBarcodes ib')
	->leftJoin('ib.ProductsInventory ibi')
	->leftJoin('op.ProductsInventoryQuantity iq')
	->leftJoin('iq.ProductsInventory iqi')
	->where('o.orders_id = ?', $oID)
	->andWhere('oa.address_type = ?', 'customer');
    if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_ENABLED') == 'True') {
        $QOrdersQuery->leftJoin('op.OrdersProductsReservation opr')
                ->andWhere('parent_id IS NULL');
    }

	$Qorders = $QOrdersQuery->execute();
	foreach ($Qorders as $oInfo) {
		foreach ($oInfo->OrdersProducts as $opInfo) {
			$productClass = new product($opInfo['products_id']);

            if ($opInfo['purchase_type'] !== 'membership') {
                $purchaseClass = $productClass->getPurchaseType($opInfo['purchase_type']); //what happens for rental
                $trackMethod = $purchaseClass->getTrackMethod();
                $invItems = $purchaseClass->getInventoryItems();
            }
			if ($opInfo['purchase_type'] == 'new' || $opInfo['purchase_type'] == 'used') {
				if (!empty($opInfo['barcode_id']) && $trackMethod == 'barcode') {
					$ProductInventoryBarcodes = Doctrine_Core::getTable('ProductsInventoryBarcodes')->findOneByBarcodeId($opInfo['barcode_id']);
					$ProductInventoryBarcodes->status = 'A';
					$ProductInventoryBarcodes->save();
				} else if ($trackMethod == 'quantity') {
					$invId = $invItems[0]['inventory_id'];
					if (!empty($invId)) {
						$ProductsInventoryQuantity = Doctrine_Core::getTable('ProductsInventoryQuantity')->findOneByInventoryId($invId);
						$ProductsInventoryQuantity->purchased--;
						$ProductsInventoryQuantity->available++;
						$ProductsInventoryQuantity->save();
					}
				}
			}
		}
	}
	EventManager::notify('CancelOrderAfterExecute', $oID);
    $Order = Doctrine_Core::getTable('Orders')->findOneByOrdersId($oID);
	$newHistory =& $Order->OrdersStatusHistory;
	$idx = $newHistory->count();
	$Order->OrdersStatusHistory[$idx]->orders_status_id = sysConfig::get('ORDERS_STATUS_CANCELLED_ID');
	$Order->orders_status = sysConfig::get('ORDERS_STATUS_CANCELLED_ID');
	$Order->save();
	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');

?>