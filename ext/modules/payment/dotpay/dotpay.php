<?php
/*
  Copyright (c) 2001-2008 Dotpay.pl
  Requires PHP 4.1.0 or above.
  Author: Dotpay.pl
*/

$textArr = explode('-', $_POST['osCsid']);
/*$_GET['osCID'] = $textArr[0];
$_REQUEST['osCID'] = $_POST["osCsid"];
$_POST['osCID'] = $_POST["osCsid"];*/

$_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en';

//if (!isset($_POST['PIN']) || $_POST['PIN']!='2367324438394793'){
//	die('Hacking Attempt');
//}else{
 $myVar = print_r($_POST, true);
  $myFile = "/home/kinodomk/public_html/ses_alpha/file2.txt";
				$fh = fopen($myFile, 'a') or die("can't open file");
				fwrite($fh, "\nDonePffayllll9hhh1AAAABBBBBBFFF.\n". $myVar);
				fclose($fh);

chdir('../../../../');
include('includes/application_top.php');


if (in_array($_POST['t_status'], array('0','1','2'))) {
	$e=array();
	if ($_POST['id'] != OrderPaymentModules::getModule('dotpay')->getConfigData('MODULE_PAYMENT_DOTPAY_ID'))
		$e[]=1;
	if (strlen($_POST['t_id'])<5)
		$e[]=3;
	$orginal_amount = isset($_POST['orginal_amount'])?$_POST['orginal_amount']:'';
	$tab = explode(" ", $orginal_amount);
	$orginal_amount = $tab[0];
	$control = $_POST['control'];
	$kwota = $control;
	if (number_format($orginal_amount,2) != number_format($kwota, 2))
		$e[]=2;
	if ($_POST['control'] != $_POST['control'])
		$e[]=4;
	$aptid="Transakcja Dotpay" . " numer: " . $_POST['t_id'];
	$m5 = OrderPaymentModules::getModule('dotpay')->getConfigData('MODULE_PAYMENT_DOTPAY_URLCPIN') . ':' . OrderPaymentModules::getModule('dotpay')->getConfigData('MODULE_PAYMENT_DOTPAY_ID') . ':' . $control . ':' . $_POST['t_id'] .
    		':' . $_POST['amount'] . ':' . $_POST['email'] . ':' . (isset($_POST['service'])?$_POST['service']:''). ':' . (isset($_POST['code'])?$_POST['code']:'') . ':' . (isset($_POST['username'])?$_POST['username']:'') .
			':' . (isset($_POST['password'])?$_POST['password']:'') . ':' . $_POST['t_status'];
	if (md5($m5) != $_POST['md5'])
		$e[]=5;
	//@ob_end_flush();
	if (count($e)!=0) {
		print "AP-OSC PROBLEM: $e[0]";
    	itwExit();
	}
	else {
		print "OK";
		
	}
}
else{
	print "OK";
}

	$orderID = (int)$textArr[2];
	$customerID = (int)$textArr[1];
    $account_action = $textArr[3];

	$order_query = tep_db_query("select currency, currency_value from orders where orders_id = " . $orderID . " and customers_id = " . $customerID . "");

	if (tep_db_num_rows($order_query) > 0){
				$order_db = tep_db_fetch_array($order_query);
				$userAccount = new rentalStoreUser($customerID);
          		$userAccount->loadPlugins();
			    require('includes/classes/order.php');
				$order = new OrderProcessor($orderID);

				$total_query = tep_db_query("select value from orders_total where orders_id = '" . $orderID . "' and (module_type = 'ot_total' OR module_type = 'total') limit 1");
				$total = tep_db_fetch_array($total_query);

				//$comment_status = $paymentStatus . ' (' . ucfirst($_POST['payer_status']) . '; ' . $currencies->format($_POST['amount'], false, $_POST['mc_currency']) . ')';
		        $comment_status = '';
				$order_status_id = sysConfig::get('DEFAULT_ORDERS_STATUS_ID');

				// modified AlexStudio's Rounding error bug fix
				// variances of up to 0.05 on either side (plus / minus) are ignored
				$pricing = ((number_format($total['value'] * $order_db['currency_value'], $currencies->get_decimal_places($order_db['currency']))) - $_POST['amount']);
			    /*$myFile = "/home/itweb1/public_html/salesIgniter_alpha/file2.txt";
				$fh = fopen($myFile, 'a') or die("can't open file");
				fwrite($fh, "\nDonePffayllll9hhh1AAAABBBBBBFFF.\n". $pricing);
				fclose($fh);*/
		//die('dsdsds'.OrderPaymentModules::getModule('dotpay')->getConfigData('MODULE_PAYMENT_DOTPAY_COMP_ORDER_STATUS_ID').'  '.$pricing);
				if ($pricing <= 0.05 && $pricing >= -0.05){
					// Terra -> modified update.
					// If payment status is "completed" than a completed order status is chosen based on the admin settings
					if (((int)OrderPaymentModules::getModule('dotpay')->getConfigData('MODULE_PAYMENT_DOTPAY_COMP_ORDER_STATUS_ID') > 0)){
						if(!empty($account_action)){
							$updateArray = array(
								'activate' => 'Y'
							);
							tep_db_perform('customers_membership', $updateArray, 'update', 'customers_id = "' . $customerID . '"');
						}
						$order_status_id = OrderPaymentModules::getModule('dotpay')->getConfigData('MODULE_PAYMENT_DOTPAY_COMP_ORDER_STATUS_ID');
						$newStatus = new OrdersPaymentsHistory();
						$newStatus->orders_id = $orderID;
						$newStatus->payment_module = 'dotpay';
						$newStatus->payment_method = 'Dotpay';
						$newStatus->gateway_message = 'Successfull payment';
						$newStatus->payment_amount = $_POST['amount'];
						$newStatus->card_details = 'NULL';
						$newStatus->save();
						$order->sendNewOrderEmail();
					}elseif (OrderPaymentModules::getModule('dotpay')->getConfigData('MODULE_PAYMENT_DOTPAY_ORDER_STATUS_ID') > 0){
						$order_status_id = OrderPaymentModules::getModule('dotpay')->getConfigData('MODULE_PAYMENT_DOTPAY_ORDER_STATUS_ID');
					}
				}
				$customer_notified = '1';
				tep_db_query("update orders set orders_status = '" . $order_status_id . "', last_modified = now() where orders_id = '" . $orderID . "'");

				$sql_data_array = array(
					'orders_id' => $orderID,
					'orders_status_id' => $order_status_id,
					'date_added' => 'now()',
					'customer_notified' => $customer_notified,
					'comments' => 'Dotpay Verified [' . $comment_status . ']'
				);
				tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
	}

  require('includes/application_bottom.php');
//}
?>
