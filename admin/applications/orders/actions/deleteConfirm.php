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
		->andWhere('oa.address_type = ?', 'customer')
		->andWhere('parent_id IS NULL');

		$Qorders = $QOrdersQuery->execute();
		foreach($Qorders as $oInfo){
			foreach($oInfo->OrdersProducts as $opInfo){
				if (isset($_POST['deleteReservationRestock']) && $_POST['deleteReservationRestock'] == '1'){
					foreach($opInfo->OrdersProductsReservation as $oprInfo){
						$reservationId = $oprInfo->orders_products_reservations_id;
						$trackMethod = $oprInfo->track_method;

						if ($trackMethod == 'barcode'){
							$oprInfo->ProductsInventoryBarcodes->status = 'A';
						}elseif ($trackMethod == 'quantity'){
							$oprInfo->ProductsInventoryQuantity->qty_out--;
							$oprInfo->ProductsInventoryQuantity->available++;
						}
					 	$oprInfo->save();
					}
					$opInfo->OrdersProductsReservation->delete();//delete OrdersProducts to?
				}

				if (isset($_POST['deleteRestockNoReservation']) && $_POST['deleteRestockNoReservation'] == '1'){
					$productClass = new product($opInfo['products_id']);
					$purchaseClass = $productClass->getPurchaseType($opInfo['purchase_type']);//what happens for rental
					$trackMethod = $purchaseClass->getTrackMethod();
					$invItems = $purchaseClass->getInventoryItems();
					if ($opInfo['purchase_type'] == 'new ' || $opInfo['purchase_type'] == 'used'){
						if (!empty($opInfo['barcode_id']) && $trackMethod == 'barcode' ){
							$ProductInventoryBarcodes = Doctrine_Core::getTable('ProductsInventoryBarcodes')->findOneByBarcodeId($opInfo['barcode_id']);
							$ProductInventoryBarcodes->status = 'A';
							$ProductInventoryBarcodes->save();
						}else if($trackMethod == 'quantity'){
							$invId = $invItems[0]['inventory_id'];
							if (!empty($invId)){
								$ProductsInventoryQuantity = Doctrine_Core::getTable('ProductsInventoryQuantity')->findOneByInventoryId($invId);
								$ProductsInventoryQuantity->purchased--;
								$ProductsInventoryQuantity->available++;
								$ProductsInventoryQuantity->save();
							}
						}
					}
				}
			}
			$oInfo->delete();
			$messageStack->addSession('pageStack', 'The order has been deleted.', 'success');
		}

		EventManager::attachActionResponse(array(
			'success' => true
	), 'json');
?>