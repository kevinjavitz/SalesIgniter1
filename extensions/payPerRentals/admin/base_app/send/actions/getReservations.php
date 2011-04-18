<?php
	$html = '';
	$Qreservations = Doctrine_Query::create()
	->from('Orders o')
	->leftJoin('o.OrdersAddresses oa')
	->leftJoin('o.OrdersProducts op')
	->leftJoin('op.OrdersProductsReservation opr')
	->leftJoin('opr.ProductsInventoryBarcodes ib')
	->leftJoin('ib.ProductsInventory i')
	->leftJoin('opr.ProductsInventoryQuantity iq')
	->leftJoin('iq.ProductsInventory i2')
	/*			->leftJoin('rb.RentalBookings rb_child')
	->leftJoin('rb_child.Orders o_child')
	->leftJoin('o_child.OrdersAddresses oa_child')
	->leftJoin('rb_child.OrdersProducts op_child')
	->leftJoin('rb_child.ProductsInventoryBarcodes ib_child')
	->leftJoin('ib_child.ProductsInventory i_child')
	->leftJoin('rb_child.ProductsInventoryQuantity iq_child')
	->leftJoin('iq_child.ProductsInventory i2_child')*/
	->where('opr.start_date BETWEEN "' . $_GET['start_date'] . '" AND "' . $_GET['end_date'] . '"')
	->andWhere('opr.rental_state = ?', 'reserved')
	->andWhere('oa.address_type = ?', 'delivery')
	->andWhere('opr.parent_id is null')
	->execute();
	if ($Qreservations !== false){
		$Orders = $Qreservations->toArray(true);
		foreach($Orders as $oInfo){
			foreach($oInfo['OrdersProducts'] as $opInfo){
				foreach($opInfo['OrdersProductsReservation'] as $rInfo){
					$orderAddress = $oInfo['OrdersAddresses']['delivery'];

					$orderId = $oInfo['orders_id'];
					$productName = $opInfo['products_name'];
					$customersName = $orderAddress['entry_name'];
					$trackMethod = $rInfo['track_method'];
					$useCenter = 0;

					$startArr = date_parse($rInfo['start_date']);
					$endArr = date_parse($rInfo['end_date']);

					$resStart = tep_date_short($rInfo['start_date']);
					$resEnd = tep_date_short($rInfo['end_date']);

					$padding_days_before = $rInfo['shipping_days_before'];
					$padding_days_after = $rInfo['shipping_days_after'];
					$shipOn = tep_date_short(date('Y-m-d', mktime(0,0,0,$startArr['month'],$startArr['day']-$padding_days_before,$startArr['year'])));
					$dueBack = tep_date_short(date('Y-m-d', mktime(0,0,0,$endArr['month'],$endArr['day']+$padding_days_after,$endArr['year'])));

					$Qcheck = Doctrine_Query::create()
					->from('OrdersProductsReservation opr')
					->leftJoin('opr.ProductsInventoryBarcodes ib')
					->leftJoin('ib.ProductsInventory i')
					->leftJoin('opr.ProductsInventoryQuantity iq')
					->leftJoin('iq.ProductsInventory i2')
					->where('parent_id = ?', $rInfo['orders_products_reservations_id'])
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

					$inventoryCenterName = "Default Store";
					if ($trackMethod == 'barcode'){
						$barcodeId = $rInfo['ProductsInventoryBarcodes']['barcode_id'];
						$barcodeNum = $rInfo['ProductsInventoryBarcodes']['barcode'];
						if ($appExtension->isInstalled('inventoryCenters') && isset($rInfo['ProductsInventoryBarcodes']['ProductsInventory'])){
							$useCenter = $rInfo['ProductsInventoryBarcodes']['ProductsInventory']['use_center'];
						}else{
							$useCenter = 0;
						}
						if ($Qcheck){
							$barcodeNum = array();
							foreach($Qcheck as $rInfo2){
								$barcodeNum[] = $rInfo2['ProductsInventoryBarcodes']['barcode'];
							}
							if (sizeof($barcodeNum) > 0){
								$barcodeNum = implode(', ', $barcodeNum);
							}
						}

						if ($appExtension->isInstalled('inventoryCenters') && $useCenter == '1'){
							//todo check for inventory centers where products barcodes resides or multistore

							if($rInfo['inventory_center_pickup'] != 0){
								$invCenter = Doctrine_Core::getTable('ProductsInventoryCenters')->findOneByInventoryCenterId($rInfo['inventory_center_pickup']);
								$inventoryCenterName = $invCenter->inventory_center_name;
							}else{
								//depending of the stock method store or inv center
								$invext = $appExtension->getExtension('inventoryCenters');

								if ($invext->stockMethod == 'Store'){
									$Qinvbs = Doctrine_Query::create()
														->from('ProductsInventoryBarcodesToStores b2s')
														->leftJoin('b2s.Stores s')
														->where('b2s.barcode_id = ?',$barcodeId)
														->fetchOne();
									if($Qinvbs)
										$inventoryCenterName = $Qinvbs->Stores->store_name;

								}else{
									$Qinvbs = Doctrine_Query::create()
														->from('ProductsInventoryBarcodesToInventoryCenters b2c')
														->leftJoin('b2c.ProductsInventoryCenters ic')
														->where('b2c.barcode_id = ?',$barcodeId)
														->fetchOne();
									if($Qinvbs)
										$inventoryCenterName = $Qinvbs->ProductsInventoryCenters->inventory_center_name;

								}

							}
						}
					}else{

						$quantityId = $rInfo['ProductsInventoryQuantity']['quantity_id'];

						$useCenter = $rInfo['ProductsInventoryQuantity']['ProductsInventory']['use_center'];

						$barcodeNum = 'Quantity Tracking';
						if ($Qcheck){
							$barcodeNum .= ' ( Package )';
						}

							if ($appExtension->isInstalled('inventoryCenters') && $useCenter == '1'){

							//todo check for inventory centers where products barcodes resides or multistore
							//get inventory center name for inventory_center_pickup
							if($rInfo['inventory_center_pickup'] != 0){
								$invCenter = Doctrine_Core::getTable('ProductsInventoryCenters')->findOneByInventoryCenterId($rInfo['inventory_center_pickup']);
								$inventoryCenterName = $invCenter->inventory_center_name;
							}else{
								//todo check getResservation for quantity tracking

								$invext = $appExtension->getExtension('inventoryCenter');
								if ($invext->stockMethod == 'Store'){
									$Qinvbs = Doctrine_Query::create()
														->from('ProductsInventoryQuantity b2s')
														->where('b2s.quantity_id = ?',$quantityId)
														->fetchOne();
									if($Qinvbs){
										$Store = Doctrine_Core::getTable('Stores')->findOneByStoreId($Qinvbs->inventory_store_id);
										$inventoryCenterName = $Store->store_name;
									}

								}else{
									$Qinvbs = Doctrine_Query::create()
											->from('ProductsInventoryQuantity b2s')
											->where('b2s.quantity_id = ?',$quantityId)
											->fetchOne();
									if($Qinvbs){
											$invCent = Doctrine_Core::getTable('ProductsInventoryCenters')->findOneByInventoryCenterId($Qinvbs->inventory_center_id);
											$inventoryCenterName = $invCent->inventory_center_name;
									}


								}
							}
						}

					}
					$trackNumber = '';
					if(!empty($oInfo['usps_track_num'])){
						$trackNumber = $oInfo['usps_track_num'];
					}
					if(!empty($oInfo['usps_track_num2'])){
						$trackNumber = $oInfo['usps_track_num2'];
					}
					if(!empty($oInfo['ups_track_num'])){
						$trackNumber = $oInfo['ups_track_num'];
					}
					if(!empty($oInfo['ups_track_num2'])){
						$trackNumber = $oInfo['ups_track_num2'];
					}
					if(!empty($oInfo['fedex_track_num'])){
						$trackNumber = $oInfo['fedex_track_num'];
					}
					if(!empty($oInfo['fedex_track_num2'])){
						$trackNumber = $oInfo['fedex_track_num2'];
					}
					if(!empty($oInfo['dhl_track_num'])){
						$trackNumber = $oInfo['dhl_track_num'];
					}
					if(!empty($oInfo['dhl_track_num2'])){
						$trackNumber = $oInfo['dhl_track_num2'];
					}

					$shippingTrackingNumber = htmlBase::newElement('input')
					->setName('shipping_number['.$rInfo['orders_products_reservations_id'].']')
					->setValue($trackNumber);
					//echo "kk".$invCenterName."--".print_r($rInfo);
					$html .= '<tr class="dataTableRow">' .
						'<td><input type="checkbox" name="sendRes[]" class="reservations" value="' . $rInfo['orders_products_reservations_id'] . '"></td>' .
						'<td class="dataTableContent">' . $customersName . '</td>' .
						'<td class="dataTableContent">' . $productName . '</td>' .
						'<td class="dataTableContent">' . $barcodeNum . '</td>' .
						'<td class="dataTableContent" align="center"><table cellpadding="2" cellspacing="0" border="0">' .
							'<tr>' .
								'<td class="dataTableContent">Ship On: </td>' .
								'<td class="dataTableContent">' . $shipOn . '</td>' .
							'</tr>' .
							'<tr>' .
								'<td class="dataTableContent">Res Start: </td>' .
								'<td class="dataTableContent">' . $resStart . '</td>' .
							'</tr>' .
							'<tr>' .
								'<td class="dataTableContent">Res End: </td>' .
								'<td class="dataTableContent">' . $resEnd . '</td>' .
							'</tr>' .
							'<tr>' .
								'<td class="dataTableContent">Due Back: </td>' .
								'<td class="dataTableContent">' . $dueBack . '</td>' .
							'</tr>' .

						'</table></td>' .
						'<td class="dataTableContent">' . $inventoryCenterName . '</td>' .
						'<td class="dataTableContent">' . $shippingTrackingNumber->draw() . '</td>' .
						'<td class="dataTableContent" align="center"><a href="' . itw_app_link('oID=' . $orderId, 'orders', 'details') . '">View Order</a></td>' .
					'</tr>';
				}
			}
		}
	}
	
	EventManager::attachActionResponse($html, 'html');
?>