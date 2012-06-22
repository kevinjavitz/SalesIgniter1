<?php
$centersEnabled = false;
if ($appExtension->isInstalled('inventoryCenters') && $appExtension->isEnabled('inventoryCenters')){
	$extInventoryCenters = $appExtension->getExtension('inventoryCenters');
	$centersEnabled = true;
	$centersStockMethod = $extInventoryCenters->stockMethod;
	if ($centersStockMethod == 'Store'){
		$extStores = $appExtension->getExtension('multiStore');
		$invCenterArray = $extStores->getStoresArray();
	}else{
		$invCenterArray = $extInventoryCenters->getCentersArray();
	}
}

$Qreservations = Doctrine_Query::create()
->from('Orders o')
->leftJoin('o.OrdersAddresses oa')
->leftJoin('o.OrdersProducts op')
->leftJoin('op.OrdersProductsReservation opr')
->leftJoin('opr.ProductsInventoryBarcodes ib')
->leftJoin('ib.ProductsInventory i')
->leftJoin('opr.ProductsInventoryQuantity iq')
->leftJoin('iq.ProductsInventory i2')
->where('oa.address_type = ?', 'delivery')
->andWhere('opr.parent_id IS NULL')
->orderBy('opr.end_date');
if (isset($_GET['include_returned']) && isset($_GET['include_unsent'])){
	$Qreservations->andWhereIn('opr.rental_state', array('returned','reserved', 'out'));
}elseif (isset($_GET['include_returned'])){
		$Qreservations->andWhereIn('opr.rental_state', array('returned', 'out'));
}elseif (isset($_GET['include_unsent'])){
	$Qreservations->andWhereIn('opr.rental_state', array('reserved', 'out'));
	}else{
		$Qreservations->andWhere('opr.rental_state = ?', 'out');
	}

if (isset($_GET['start_date']) || isset($_GET['end_date'])){
	//$Qreservations->andWhere('opr.start_date between "' . $_GET['start_date'] . '" and "' . $_GET['end_date'] . '" OR opr.end_date between "' . $_GET['start_date'] . '" and "' . $_GET['end_date'] . '"');
	$Qreservations->andWhere('opr.end_date between "' . $_GET['start_date'] . '" and "' . $_GET['end_date'] . '"');
}
	if (isset($_GET['filter_shipping']) && !empty($_GET['filter_shipping'])){
		$Qreservations->andWhere('o.shipping_module LIKE ?', '%' . $_GET['filter_shipping'] . '%');
	}
	if (isset($_GET['filter_category']) && !empty($_GET['filter_category'])){
		$Qreservations->leftJoin('op.Products p')
			->leftJoin('p.ProductsToCategories ptc')
			->andWhere('ptc.categories_id = ?', $_GET['filter_category']);
	}
	

if ($centersEnabled === true){
	if ($centersStockMethod == 'Store'){
		$Qreservations->leftJoin('ib.ProductsInventoryBarcodesToStores b2s')
		->leftJoin('b2s.Stores s');
	}else{
		$Qreservations->leftJoin('ib.ProductsInventoryBarcodesToInventoryCenters b2c')
		->leftJoin('b2c.ProductsInventoryCenters ic');
	}
}

EventManager::notify('OrdersListingBeforeExecute', &$Qreservations);

$Qreservations->orderBy('oa.entry_name, o.orders_id');

$Result = $Qreservations->execute();

		$html = 'Client Name,' . 
			'Invoice Number,' . 
			'Delivery Address,' . 
			'Delivery City,' . 
			'Delivery State,' . 
			'Delivery Zip,' . 
			'Client Phone Number,' . 
			'Rental Or Retail,' . 
			'Items Ordered,' . 
			'Quantity,' . 
			'Return Date,' .
			'Delivery Method,' . 
			'Comments';
		$html .= "\n";
		
	if ($Result !== false){
		$Orders = $Result->toArray(true);
		foreach($Orders as $oInfo){
			foreach($oInfo['OrdersProducts'] as $opInfo){
			
					$orderAddress = $oInfo['OrdersAddresses']['delivery'];

					$orderId = $oInfo['orders_id'];
					$productName = $opInfo['products_name'];
					$shippingMethod = $oInfo['shipping_module'];

					$customersName = $orderAddress['entry_name'];
					foreach($opInfo['OrdersProductsReservation'] as $rInfo){
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
										$inventoryCenterName = $Qinvbs->Stores->stores_name;

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

						$useCenter = isset($rInfo['ProductsInventoryQuantity']['ProductsInventory']['use_center'])?$rInfo['ProductsInventoryQuantity']['ProductsInventory']['use_center']:'0';

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
										$inventoryCenterName = $Store->stores_name;
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
					$payedAmount = '';

					$statusSelectedText = '';
					$QrentalStatus = Doctrine_Query::create()
						->from('RentalStatus')
						->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
					foreach($QrentalStatus as $iStatus){
						if(is_null($rInfo['rental_status_id']) && $iStatus['rental_status_id'] == '2'){
							$statusSelectedText = $iStatus['rental_status_text'];
						}elseif ($rInfo['rental_status_id'] == $iStatus['rental_status_id']){
							$statusSelectedText = $iStatus['rental_status_text'];
						}
					}

					if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_PROCESS_SEND') == 'True'){
						$payedAmount = $opInfo['final_price'];

						if($rInfo['amount_payed'] > 0){
							$payedAmount -= $rInfo['amount_payed'];
						}
					}

						if (!isset($currentName) || $currentName != $customersName){
							$currentName = $customersName;
							$showName = $currentName;
						}else{
							$showName = '';
						}
						
						if (!isset($currentOrder) || $currentOrder != $oInfo['orders_id']){
							$currentOrder = $oInfo['orders_id'];
							$showOrder = $currentOrder;
						}else{
							$showOrder = '';
						}
					$Qhistory = Doctrine_Query::create()
					->from('OrdersStatusHistory')
					->where('orders_id = ?', $oInfo['orders_id'])
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
					$comments = '';
					foreach($Qhistory as $history){
						$comments .= stripslashes($history['comments'])."\n";
					}
						$html .= '"' . addslashes($showName) . '",' . 
							'"' . $showOrder . '",' . 
							'"' . addslashes($orderAddress['entry_street_address']) . '",' . 
							'"' . addslashes($orderAddress['entry_city']) . '",' . 
							'"' . addslashes($orderAddress['entry_state']) . '",' . 
							'"' . addslashes($orderAddress['entry_postcode']) . '",' . 
							'"' . $oInfo['customers_telephone'] . '",' . 
							'"Rental",' . 
							'"' . addslashes($productName) . '",' . 
							'"' .  $opInfo['products_quantity'] . '",' . //Hardcoded to 1 because each reservation is put in and reservations only allow 1 qty
							'"' . $dueBack . '",' .
							'"' . addslashes(strip_tags($oInfo['shipping_module'])) . '",' . 
						'"'.addslashes($comments).'"';						
							
						$html .= "\n";
				}
			}
		}
	}
	
		header("Content-type: text/csv");
		header("Content-disposition: attachment; filename=Reservations.csv");
		// Changed if using SSL, helps prevent program delay/timeout (add to backup.php also)
		//	header("Pragma: no-cache");
		if ($request_type== 'NONSSL'){
			header("Pragma: no-cache");
		} else {
			header("Pragma: ");
		}
		header("Expires: 0");
		echo $html;
		itwExit();
