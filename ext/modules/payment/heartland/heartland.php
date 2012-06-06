<?php

	$textArr = explode('-', $_POST['ClientSessionID']);
	$_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en';
	chdir('../../../../');
	include('includes/application_top.php');

	$orderID = (int)$textArr[2];
	$customerID = (int)$textArr[1];
    $account_action = $textArr[3];

	$order_query = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc("select currency, currency_value from orders where orders_id = " . $orderID . " and customers_id = " . $customerID . "");

	if (sizeof($order_query) > 0 && $_POST['ResultNum'] == '0'){
		$order_db = $order_query[0];
		$userAccount = new rentalStoreUser($customerID);
		$userAccount->loadPlugins();
		require('includes/classes/order.php');
		$order = new OrderProcessor($orderID);
	    OrderPaymentModules::loadModules();
		$total_query =  Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchAssoc("select value from orders_total where orders_id = '" . $orderID . "' and (module_type = 'ot_total' OR module_type = 'total') limit 1");
		$total = $total_query[0];

		//$comment_status = $paymentStatus . ' (' . ucfirst($_POST['payer_status']) . '; ' . $currencies->format($_POST['amount'], false, $_POST['mc_currency']) . ')';
		$comment_status = '';
		$order_status_id = sysConfig::get('DEFAULT_ORDERS_STATUS_ID');

		$pricing = ((number_format($total['value'] * $order_db['currency_value'], $currencies->get_decimal_places($order_db['currency']))) - $_POST['Amount']);
		if ($pricing <= 0.05 && $pricing >= -0.05){

			if (((int)OrderPaymentModules::getModule('heartland')->getConfigData('MODULE_PAYMENT_HEARTLAND_COMP_ORDER_STATUS_ID') > 0)){
				if($account_action == 'M'){
					$QUpdateCustomerMemberShip = Doctrine_Query::create()
						->update('CustomersMembership')
						->set('activate', '?', 'Y')
						->where('customers_id=?',$customerID)
						->execute();

				}
				$order_status_id = OrderPaymentModules::getModule('heartland')->getConfigData('MODULE_PAYMENT_HEARTLAND_COMP_ORDER_STATUS_ID');
				$newStatus = new OrdersPaymentsHistory();
				$newStatus->orders_id = $orderID;
				$newStatus->payment_module = 'heartland';
				$newStatus->payment_method = 'Heartland';
				$newStatus->gateway_message = 'Successfull payment';
				$newStatus->payment_amount = $_POST['Amount'];
				$newStatus->card_details = 'NULL';
				$newStatus->save();
				$order->sendNewOrderEmail();
			}elseif (OrderPaymentModules::getModule('heartland')->getConfigData('MODULE_PAYMENT_HEARTLAND_ORDER_STATUS_ID') > 0){
				$order_status_id = OrderPaymentModules::getModule('heartland')->getConfigData('MODULE_PAYMENT_HEARTLAND_ORDER_STATUS_ID');
			}
		}
		$customer_notified = '1';
		Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->exec("update orders set orders_status = '" . $order_status_id . "', last_modified = now() where orders_id = '" . $orderID . "'");

		$OrdersStatusHistory = new OrdersStatusHistory();
		$OrdersStatusHistory->orders_id = $orderID;
		$OrdersStatusHistory->orders_status_id = $order_status_id;
		$OrdersStatusHistory->date_added = date('Y-m-d H:i:s');
		$OrdersStatusHistory->customer_notified = $customer_notified;
		$OrdersStatusHistory->comments = 'Heartland Verified [' . $comment_status . ']';
		$OrdersStatusHistory->save();

	}else{
		switch($_POST['ResultNum']){
			case '0':
				$messageStack->addSession('pageStack', 'You have paid but an error appaeared on our side please contact us');
				break;
			case '1':
				$messageStack->addSession('pageStack', 'Canceled');
				break;
			case '2':
				$messageStack->addSession('pageStack', 'Back');
				break;
			case '3':
				$messageStack->addSession('pageStack', 'Max number of attempts reach');
				break;
			case '4':
				$messageStack->addSession('pageStack', 'Timeout');
				break;
		}
	}

    tep_redirect(itw_app_link(null, 'checkout', 'success', 'SSL'));
    require('includes/application_bottom.php');
//}

//
?>