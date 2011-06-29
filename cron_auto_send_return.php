<?php
require('includes/application_top.php');
if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_AUTO_SEND') == 'True'){
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
	//->whereIn('opr.orders_products_reservations_id', $_GET['sendRes'])
	->where('opr.start_date <= ?', date('Y-m-d H:i:s'))//substract shipping days before
	->andWhere('oa.address_type = ?', 'customer')
	->andWhere('opr.parent_id IS NULL');

	EventManager::notify('OrdersListingBeforeExecute', &$Qreservations);

	$Qreservations = $Qreservations->execute();
	if ($Qreservations->count() > 0){
		foreach($Qreservations as $oInfo){
			foreach($oInfo->OrdersProducts as $opInfo){
				foreach($opInfo->OrdersProductsReservation as $oprInfo){
					if (isset($oprInfo->Packaged)){
						foreach($oprInfo->Packaged as $opprInfo){
							$opprInfo->rental_state = 'out';
							if ($opprInfo->track_method == 'barcode'){
								$opprInfo->ProductsInventoryBarcodes->status = 'O';
							}elseif ($opprInfo->track_method == 'quantity'){
								$opprInfo->track_method->ProductsInventoryQuantity->reserved -= 1;
								$opprInfo->track_method->ProductsInventoryQuantity->qty_out += 1;
							}
						}
					}

					$oprInfo->rental_state = 'out';
					$oprInfo->date_shipped = $oprInfo['start_date'];//date('Y-m-d');

					if ($oprInfo->track_method == 'barcode'){
						$oprInfo->ProductsInventoryBarcodes->status = 'O';
					}elseif ($oprInfo->track_method == 'quantity'){
						$oprInfo->ProductsInventoryQuantity->reserved -= 1;
						$oprInfo->ProductsInventoryQuantity->qty_out += 1;
					}

					$emailEvent = new emailEvent('reservation_sent', $oInfo->Customers->language_id);

					$emailEvent->setVars(array(
						'full_name' => $oInfo->OrdersAddresses['customer']->entry_name,
						'rented_product' => $opInfo->products_name,
						'due_date' => tep_date_long($oprInfo->end_date),
						'email_address' => $oInfo->customers_email_address
					));
					
					$emailEvent->sendEmail(array(
						'email' => $oInfo->customers_email_address,
						'name' => $oInfo->OrdersAddresses['customer']->entry_name
					));
				}
			}
		}
		$Qreservations->save();
	}
}

if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_AUTO_RETURN') == 'True'){
	$ReservationQuery = Doctrine_Query::create()
		->from('Orders o')
		->leftJoin('o.Customers c')
		->leftJoin('o.OrdersAddresses oa')
		->leftJoin('o.OrdersProducts op')
		->leftJoin('op.OrdersProductsReservation opr')
		->leftJoin('opr.ProductsInventoryBarcodes ib')
		->leftJoin('ib.ProductsInventory ibi')
		->leftJoin('opr.ProductsInventoryQuantity iq')
		->leftJoin('iq.ProductsInventory iqi')
		//->where('opr.orders_products_reservations_id = ?', $bID)
		->where('opr.end_date <= ?', date('Y-m-d H:i:s')) //addshipping_day_after
		->andWhere('oa.address_type = ?', 'customer')
		->andWhere('parent_id IS NULL');

		if ($appExtension->isInstalled('inventoryCenters') && $appExtension->isEnabled('inventoryCenters')){
			$extInventoryCenters = $appExtension->getExtension('inventoryCenters');
			if ($extInventoryCenters->stockMethod == 'Store'){
				$ReservationQuery->leftJoin('ib.ProductsInventoryBarcodesToStores b2s')
				->leftJoin('b2s.Stores');
			}else{
				$ReservationQuery->leftJoin('ib.ProductsInventoryBarcodesToInventoryCenters b2c')
				->leftJoin('b2c.ProductsInventoryCenters');
			}
		}

		EventManager::notify('OrdersListingBeforeExecute', &$ReservationQuery);

		$Reservation = $ReservationQuery->execute();
		foreach($Reservation as $oInfo){
			foreach($oInfo->OrdersProducts as $opInfo){
				foreach($opInfo->OrdersProductsReservation as $oprInfo){
					$reservationId = $oprInfo->orders_products_reservations_id;
					$trackMethod = $oprInfo->track_method;

					$oprInfo->rental_state = 'returned';
					$oprInfo->date_returned = $oprInfo['end_date'];//date('Y-m-d h:i:s');
					$oprInfo->broken = '0';

					if (!empty($comment)){
						if ($reservationId == 'barcode'){
							$oprInfo->ProductsInventoryBarcodes->ProductsInventoryBarcodesComments[]->comments = $comment;
						}elseif ($reservationId == 'quantity'){
							$oprInfo->ProductsInventoryQuantity->ProductsInventoryQuantitysComments[]->comments = $comment;
						}
					}

					if (isset($extInventoryCenters)){
						$invCenterChanged = false;
						if (isset($oprInfo['inventory_center_dropoff'])){
							$invCenter = $oprInfo['inventory_center_dropoff'];
							if ($trackMethod == 'barcode'){
								if ($extInventoryCenters->stockMethod == 'Store'){
									$Barcode = $oprInfo->ProductsInventoryBarcodes->ProductsInventoryBarcodesToStores;
									if ($Barcode->inventory_store_id != $invCenter){
										$Barcode->inventory_store_id = $invCenter;
										$invCenterChanged = true;
									}
								}else{
									$Barcode = $oprInfo->ProductsInventoryBarcodes->ProductsInventoryBarcodesToInventoryCenters;
									if ($Barcode->inventory_center_id != $invCenter){
										$Barcode->inventory_center_id = $invCenter;
										$invCenterChanged = true;
									}
								}
							}elseif ($trackMethod == 'quantity'){
								$Quantity = $oprInfo->ProductsInventoryQuantity;
								if ($extInventoryCenters->stockMethod == 'Store'){
									if ($Quantity->inventory_store_id != $invCenter){
										$Qupdate = Doctrine_Query::create()
										->update('ProductsInventoryQuantity')
										->where('inventory_store_id = ?', $invCenter)
										->andWhere('inventory_id = ?', $Quantity->inventory_id);
										if ($status == 'B' || $status == 'L'){
											$Qupdate->set('broken = broken+1');
										}else{
											$Qupdate->set('available = available+1');
										}
										$Qupdate->execute();
										$invCenterChanged = true;
									}
								}else{
									if ($Quantity->inventory_center_id != $invCenter){
										$Qupdate = Doctrine_Query::create()
										->update('ProductsInventoryQuantity')
										->where('inventory_center_id = ?', $invCenter)
										->andWhere('inventory_id = ?', $Quantity->inventory_id);
										if ($status == 'B' || $status == 'L'){
											$Qupdate->set('broken = broken+1');
										}else{
											$Qupdate->set('available = available+1');
										}
										$Qupdate->execute();
										$invCenterChanged = true;
									}
								}
							}
						}
					}else{

						if ($trackMethod == 'barcode'){
							$oprInfo->ProductsInventoryBarcodes->status = $status;
						}elseif ($trackMethod == 'quantity'){
							$oprInfo->ProductsInventoryQuantity->qty_out--;
							if ($status == 'B' || $status == 'L'){
								$oprInfo->ProductsInventoryQuantity->broken++;
							}else{
								$oprInfo->ProductsInventoryQuantity->available++;
							}
						}
					}

					/*
					//send email on autoreturn?
					$emailEvent = new emailEvent('reservation_returned', $oInfo->Customers->language_id);
					if (date('Y-m-d') > $oprInfo->end_date){
						$dateArr = date_parse($oprInfo->end_date);
						$days_late = (mktime(0,0,0) - mktime(0,0,0,$dateArr['month'],$dateArr['day'],$dateArr['year'])) / (60 * 60 * 24);
					}else{
						$days_late = 0;
					}
					$emailEvent->setVar('days_late', $days_late);
					$emailEvent->setVar('full_name', $oInfo->OrdersAddresses['customer']->entry_name);
					$emailEvent->setVar('email_address', $oInfo->customers_email_address);
					$emailEvent->setVar('rented_product', $opInfo->products_name);

					$emailEvent->sendEmail(array(
					'email' => $oInfo->customers_email_address,
					'name'  => $oInfo->OrdersAddresses['customer']->entry_name
					));*/
				}
			}
		}
		$Reservation->save();
}
	require('includes/application_bottom.php');
?>