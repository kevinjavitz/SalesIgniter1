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
    $errMsg = '';
	if ($Qreservations->count() > 0){
		foreach($Qreservations as $oInfo){
			$Qhistory = Doctrine_Query::create()
			->from('OrdersPaymentsHistory')
			->where('orders_id = ?', $oInfo['orders_id'])
			->orderBy('payment_history_id DESC')
			->limit(1)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if(isset($Qhistory[0])){
				$paymentHistory = $Qhistory[0];
				$historyId = $paymentHistory['payment_history_id'];
				$paymentModule = OrderPaymentModules::getModule($paymentHistory['payment_module']);

				foreach($oInfo->OrdersProducts as $opInfo){
					foreach($opInfo->OrdersProductsReservation as $oprInfo){

						if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_PROCESS_SEND') == 'True'){
							$payedAmount = (isset($_POST['amount_payed'][$oprInfo->orders_products_reservations_id])?$_POST['amount_payed'][$oprInfo->orders_products_reservations_id]:'');
							//getpayment history for order..get payment data for order..uiset hem to pay add a new message for the reservation barcode
							$paymentModule->processPayment($oInfo['orders_id'], $payedAmount);
							$QhistoryLast = Doctrine_Query::create()
							->from('OrdersPaymentsHistory')
							->where('orders_id = ?', $oInfo['orders_id'])
							->orderBy('payment_history_id DESC')
							->limit(1)
							->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
							if($QhistoryLast[0]['payment_history_id'] > $historyId){
								if($QhistoryLast[0]['success'] == '0'){
									$errMsg .= $QhistoryLast[0]['gateway_message'].' for order ID:'.$oInfo['orders_id'].' and barcode ID:'.$oprInfo['barcode_id'];
								}else{
									$oprInfo->amount_payed = $payedAmount;
								}
							}else{
								$errMsg .= 'Transaction did not take place for order ID:'.$oInfo['orders_id'].' and barcode ID:'.$oprInfo['barcode_id'];
							}
						}

					}

				}
			}
		}
		$Qreservations->save();
	}



	EventManager::attachActionResponse(array(
		'success' => true,
		'errMsg' => $errMsg
	), 'json');
?>