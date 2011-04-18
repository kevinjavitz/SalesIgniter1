<?php
	$type = $_GET['type'];
	$oID = (int)$_GET['order_id'];
	
	if ($type == 'approve'){
		$statusCheck = 'pprove';
		$messageStack->addSession('pageStack', 'The order has been approved.', 'success');
	}else{
		$statusCheck = 'ancell';
	}
	
	$QordersStatus = Doctrine_Query::create()
	->select('s.orders_status_id, sd.orders_status_name')
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

	require(sysConfig::getDirFsCatalog() . 'includes/classes/Order/Base.php');
	$Order = new Order($_GET['order_id']);

	$orderedProducts = '<table>';
 foreach($Order->getProducts() as $OrderProduct){
	$orderedProducts .= '          <tr>' . "\n" .
	'            <td class="main" align="right" valign="top" width="30">' . $OrderProduct->getQuantity() . '&nbsp;x</td>' . "\n" .
	'            <td class="main" valign="top">' . $OrderProduct->getNameHtml();

	$orderedProducts .= '</td>' . "\n";

	if ($Order->hasTaxes() === true) {
		$orderedProducts .= '            <td class="main" valign="top" align="right">' . tep_display_tax_value($OrderProduct->getTaxRate()) . '%</td>' . "\n";
	}

	$orderedProducts .= '            <td class="main" align="right" valign="top">' . $currencies->format($OrderProduct->getFinalPrice(true, true)) . '</td>' . "\n" .
	'          </tr>' . "\n";
}
 	$orderedProducts .= '</table>';

	$full_name = $Qorder[0]['OrdersAddresses'][0]['entry_name'];
	$orderID = $oID;
	$status = $QordersStatus[0]['OrdersStatusDescription'][0]['orders_status_name'];
	$datePurchased = tep_date_long($Qorder[0]['date_purchased']);

	$pickupz = Doctrine_Query::create()
						->from('Orders o')
						->leftJoin('o.OrdersProducts op')
						->leftJoin('op.OrdersProductsReservation ops')
						->where('o.orders_id =?', (int)$_GET['order_id'])
						->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

$inv_address = "\n\t";
$contents = EventManager::notifyWithReturn('OrderInfoAddBlock', (int)$_GET['order_id']);
	if (!empty($contents)){
		foreach($contents as $content){
			$inv_address .= $content;
		}
	}

	$Qinv = Doctrine_Core::getTable('ProductsInventoryCenters')->findOneByInventoryCenterId($pickupz[0]['OrdersProducts'][0]['OrdersProductsReservation'][0]['inventory_center_pickup']);
	$inv_address .= "\n\tSpot Address: ".$Qinv->inventory_center_specific_address;
	$deliveryInstructions = "\n\tDelivery Instructions: ".$Qinv->inventory_center_delivery_instructions;				

	$emailEvent = new emailEvent('order_update_inventory');
 	$emailEvent->setVar('order_id', $orderID);
  	$emailEvent->setVar('invoice_link', itw_app_link('order_id=' . $orderID, 'account', 'history_info', 'SSL', false));
	$emailEvent->setVar('date_ordered', strftime(sysLanguage::getDateFormat('long')));
	$emailEvent->setVar('ordered_products', $orderedProducts);
 	$emailEvent->setVar('status', $status);
	$emailEvent->setVars(array(
		'inv_address' => $inv_address,
		'deliveryInstructions' => $deliveryInstructions
	));
	$emailEvent->sendEmail(array(
		'email' => $Qorder[0]['customers_email_address'],
		//'email' => "cristian@itwebexperts.com",
		'name' => $full_name
	));

	if($statusCheck == 'ancell'){
		//a way to see when the orderproductsreservation rental_state is completed
		$messageStack->addSession('pageStack', 'The order has been cancelled.', 'success');
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

	EventManager::attachActionResponse(itw_app_link('appExt=inventoryCenters', 'account_addon', 'view_orders_inventory', 'SSL'), 'redirect');
?>