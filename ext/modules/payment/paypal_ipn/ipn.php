	<?php


	$parameters = 'cmd=_notify-validate';

	foreach($_POST as $key => $value){
		$parameters .= '&' . $key . '=' . urlencode(stripslashes($value));
	}

	/*To Remove
	$myFile = "/home/itweb1/public_html/ses/1.6a/cristian/file2.txt";
				$fh = fopen($myFile, 'a') or die("can't open file");
				fwrite($fh, '\nFile acccessed\n->'. $parameters);
				fclose($fh);
	End Remove*/

	$_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en';
	$textArr = array();
	$textArr = explode(';', $_POST['custom']);
	$_GET['osCID'] = $textArr[0];
	$_REQUEST['osCID'] = $textArr[0];
	$_POST['osCID'] = $textArr[0];
	$customerTextId = $textArr[1];
	if(isset($textArr[2])){
		$customerIp = $textArr[2];
	}
	chdir('../../../../');
	require('includes/application_top.php');
	require('includes/classes/order.php');

	if (OrderPaymentModules::getModule('paypalipn')->getConfigData('MODULE_PAYMENT_PAYPALIPN_GATEWAY_SERVER') == 'Live'){
		$server = 'www.paypal.com';
	}else{
		$server = 'www.sandbox.paypal.com';
	}


	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, 'https://' . $server . '/cgi-bin/webscr');
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
	//curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded", "Content-Length: " . strlen($parameters)));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_TIMEOUT, 60);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

	$result = curl_exec($ch);

	curl_close($ch);

	if(isset($_POST['invoice'])){
		$orderID = (int)$_POST['invoice'];
	}

	$account_action = '';
	if(isset($_POST['payment_status'])){
		$paymentStatus = $_POST['payment_status'];
	}

    $customerID = (int)$customerTextId;


	if (isset($_POST['txn_type'])){
		switch($_POST['txn_type']){
			case 'subscr_modify':
				$account_action = 'upgrade';
				break;
			case 'subscr_payment':
				$account_action = 'payment';
				break;
			case 'subscr_eot':
				$account_action = 'endOfTerm';
				break;
			case 'subscr_cancel':
				$account_action = 'cancel';
				break;
			case 'subscr_signup':
				if (isset($_POST['period1'])){
					$account_action = 'free_trial';
					$paymentStatus = 'FreeTrial';
				}else{
					exit;
				}
				break;
		}
	}

	$planID = false;

	if (!empty($account_action)){
		$Qcustomer = Doctrine_Query::create()
					->from('Customers c')
					->leftJoin('c.CustomersMembership cm')
					->where('c.customers_id = ?', $customerID)
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		$QplanInfo = Doctrine_Query::create()
		->from('Membership m')
		->leftJoin('m.MembershipPlanDescription md')
		->where('plan_id=?', $Qcustomer[0]['CustomersMembership']['plan_id'])
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		$planInfo = $QplanInfo[0];
		$planID = (int) $planInfo['plan_id'];
		if (($account_action == 'upgrade' && (int) $_POST['amount3'] == (int) $membershipTemp['amount']) || $account_action == 'cancel'){

			$QDeleteMemebership = Doctrine_Query::create()
			->delete('MembershipUpdate')
			->where('customers_id=?', $customerID)
			->execute();
			$QDeleteMemebershipTemp = Doctrine_Query::create()
			->delete('MembershipUpdateTemp')
			->where('customers_id=?', $customerID)
			->execute();
		}

		if ($account_action == 'upgrade'){
			$new_amount = $membershipTemp['amount'];

			if (isset($_POST['amount3']) && (int)$_POST['amount3'] == (int) $new_amount){
				$next_billing_date = $membershipTemp['membership_days'];
				$membershipUpdate = new MembershipUpdate();
				$membershipUpdate->customers_id = $customerID;
				$membershipUpdate->plan_id = $planID;
				$membershipUpdate->upgrade_date = $next_billing_date;
				$membershipUpdate->save();
				itwExit();
			}
		}elseif ($account_action == 'payment'){

		}elseif ($account_action == 'endOfTerm' || $account_action == 'cancel'){
			$emailEvent = new emailEvent();

			$emailEvent->setVars(array(
				'full_name' => $Qcustomer[0]['customers_firstname'] . ' ' . $Qcustomer[0]['customers_lastname'],
				'emailAddress' => $Qcustomer[0]['customers_email_address'],
				'paymentMethod' => $Qcustomer[0]['CustomersMembership']['payment_method'],
				'subscriptionDate' => $Qcustomer[0]['CustomersMembership']['membership_date'],
				'planID' => $planInfo['plan_id'],
				'packageName' => $planInfo['MembershipPlanDescription'][0]['name'],
				'membershipIsDays' => false,
				'membershipIsMonths' => false,
				'numberOfRentals' => $planInfo['no_of_titles'],
				'freeTrialPeriod' => $planInfo['free_trial'],
				'price' => $currencies->format($planInfo['price'])
			));

			if ($planInfo['membership_days'] > 0){
				$emailEvent->setVar('membershipIsDays', true);
				$emailEvent->setVar('membershipPeriod', $planInfo['membership_days']);
			}else{
				$emailEvent->setVar('membershipIsMonths', true);
				$emailEvent->setVar('membershipPeriod', $planInfo['membership_months']);
			}

			if ($account_action == 'cancel'){
				//tep_db_query('update ' . TABLE_CUSTOMERS . ' set canceled = "1" where customers_id = "' . $customerID . '"');
				Doctrine_Query::create()
				->update('CustomersMembership')
				->set('canceled','?','1')
				->where('customers_id = ?', $customerID)
				->execute();

				$emailEvent->setEvent('membership_cancel_request');
				$emailEvent->sendEmail(array(
					'name' => sysConfig::get('STORE_OWNER'),
					'email' => sysConfig::get('STORE_OWNER_EMAIL_ADDRESS')
				));
			}else{
				Doctrine_Query::create()
				->update('CustomersMembership')
				->set('canceled','?','0')
				->set('ismember','?','U')
				->set('activate','?','N')
				->where('customers_id = ?', $customerID)
				->execute();

				//tep_db_query('update ' . 'customers_membership' . ' set canceled = "0", ismember = "U", activate = "N" where customers_id = "' . $customerID . '"');

				$emailEvent->setEvent('membership_expired');
				$emailEvent->sendEmail(array(
					'name' => $full_name,
					'email' => $emailAddress
				));
				$emailEvent->sendEmail(array(
					'name' => sysConfig::get('STORE_OWNER'),
					'email' => sysConfig::get('STORE_OWNER_EMAIL_ADDRESS')
				));
			}
			unset($emailEvent);
			itwExit();
		}
	}     

	if ($result == 'VERIFIED'){
		if (is_numeric($orderID) && $orderID > 0){
			
			//$order_query = tep_db_query("select currency, currency_value from " . TABLE_ORDERS . " where orders_id = '" . $orderID . "' and customers_id = '" . $customerID . "'");
			$QOrder = Doctrine_Query::create()
			->from('Orders')
			->where('orders_id = ?', $orderID)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			if (count($QOrder) > 0){

				//$order_db = tep_db_fetch_array($order_query);
				$order = new OrderProcessor($orderID);
				$QTotals = Doctrine_Query::create()
				->from('OrdersTotal')
				->where('orders_id=?',$orderID)
				->andWhereIn('module_type',array('ot_total','total'))
				->fetchOne();
				$comment_status = $paymentStatus . ' (' . ucfirst($_POST['payer_status']) . '; ' . $currencies->format($_POST['mc_gross'], false, $_POST['mc_currency']) . ')';

				if ($paymentStatus == 'Pending'){
					$comment_status .= '; ' . $_POST['pending_reason'];
				}elseif (($paymentStatus == 'Reversed') || ($paymentStatus == 'Refunded')){
					$comment_status .= '; ' . $_POST['reason_code'];
				}elseif (($paymentStatus == 'Completed') && (OrderPaymentModules::getModule('paypalipn')->getConfigData('MODULE_PAYMENT_PAYPALIPN_SHIPPING') == 'True')){
					$comment_status .= ", \n" . OrderPaymentModules::getModule('paypalipn')->getConfigData('MODULE_PAYMENT_PAYPALIPN_ID') . ": " . $_POST['address_name'] . ", " . $_POST['address_street'] . ", " . $_POST['address_city'] . ", " . $_POST['address_zip'] . ", " . $_POST['address_state'] . ", " . $_POST['address_country'] . ", " . $_POST['address_country_code'] . ", " . $_POST['address_status'];
				}
				$order_status_id = sysConfig::get('DEFAULT_ORDERS_STATUS_ID');

				$pricing = ((number_format($QTotals->value * $QOrder[0]['currency_value'], $currencies->get_decimal_places($QOrder[0]['currency']))) - $_POST['mc_gross']);

				if ($pricing <= 0.05 && $pricing >= -0.05){
					if (((int)OrderPaymentModules::getModule('paypalipn')->getConfigData('MODULE_PAYMENT_PAYPALIPN_COMP_ORDER_STATUS_ID') > 0) && ($paymentStatus == 'Completed')){
						if(!empty($account_action)){
							$QUpdateCustomerMemberShip = Doctrine_Query::create()
							->update('CustomersMembership')
							->set('activate', '?', 'Y')
							->where('customers_id=?',$customerID)
							->execute();

						}
						
						EventManager::notify('CheckoutSuccessRemoteFinish',$orderID, $_POST['mc_gross'], $customerIp);
						$order_status_id = OrderPaymentModules::getModule('paypalipn')->getConfigData('MODULE_PAYMENT_PAYPALIPN_COMP_ORDER_STATUS_ID');
						$newStatus = new OrdersPaymentsHistory();
						$newStatus->orders_id = $orderID;
						$newStatus->payment_module = 'paypal_ipn';
						$newStatus->payment_method = 'Paypal';
						$newStatus->gateway_message = 'Successfull payment';
						$newStatus->payment_amount = $_POST['mc_gross'];
						$newStatus->card_details = 'NULL';
						$newStatus->save();
						$order->info['payment_module'] = 'Paypal';
						$order->sendNewOrderEmail();
					}elseif (OrderPaymentModules::getModule('paypalipn')->getConfigData('MODULE_PAYMENT_PAYPALIPN_ORDER_STATUS_ID') > 0){
						$order_status_id = OrderPaymentModules::getModule('paypalipn')->getConfigData('MODULE_PAYMENT_PAYPALIPN_ORDER_STATUS_ID');
					}
				}				
				$customer_notified = '0';
				if (($paymentStatus == 'Pending') || ($paymentStatus == 'Completed')){
					$customer_notified = '1';
				}

				$QUpdateOrder = Doctrine_Query::create()
					->update('Orders')
					->set('orders_status', '?', $order_status_id)
					->set('last_modified', '?', date('Y-m-d H:i:s'))
					->where('orders_id=?',$orderID)
					->execute();

				/*tep_db_query("update " . TABLE_ORDERS . " set orders_status = '" . $order_status_id . "', last_modified = now() where orders_id = '" . $orderID . "'");

				$sql_data_array = array(
					'orders_id' => $orderID,
					'orders_status_id' => $order_status_id,
					'date_added' => 'now()',
					'customer_notified' => $customer_notified,
					'comments' => 'PayPal IPN Verified [' . $comment_status . ']'
				);
				tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);*/
				$OrdersStatusHistory = new OrdersStatusHistory();
				$OrdersStatusHistory->orders_id = $orderID;
				$OrdersStatusHistory->orders_status_id = $order_status_id;
				$OrdersStatusHistory->date_added = date('Y-m-d H:i:s');
				$OrdersStatusHistory->customer_notified = $customer_notified;
				$OrdersStatusHistory->comments = 'PayPal IPN Verified [' . $comment_status . ']';
				$OrdersStatusHistory->save();



				//insert payment History				
				if ($planID !== false && $paymentStatus == 'FreeTrial' && isset($_POST['period1'])){
					$period = explode(' ', $_POST['period1']);
					$periodTime = $period[0];
					if ($period[1] == 'M'){
						$periodType = 'month';
					}elseif ($period[1] == 'W'){
						$periodType = 'week';
					}else{
						$periodType = 'day';
					}


					$next_billing_date = date('Y-m-d h:i:s', strtotime('+' . $periodTime . ' day'));

					/*$updateArray = array(
						'ismember' => 'M',
						'activate' => 'Y',
						'membership_date' => date('Ymdhis'),
						'next_bill_date' => $next_billing_date,
						'plan_id' => $planID
					);
					tep_db_perform('customers_membership', $updateArray, 'update', 'customers_id = "' . $customerID . '"');*/
					Doctrine_Query::create()
						->update('CustomersMembership')
						->set('membership_date ', '?', date('Ymdhis'))
						->set('ismember','?','M')
						->set('activate','?','Y')
						->set('next_bill_date','?', $next_billing_date)
						->set('plan_id','?', $planID)
						->where('customers_id = ?', $customerID)
						->execute();


					/*$membershipArray = array(
						'customers_id' => $customerID,
						'plan_id' => $planID,
						'upgrade_date' => $next_billing_date
					);
					tep_db_perform(TABLE_MEMBERSHIP_UPDATE, $membershipArray);*/

					$MembersipUpdateInsert = new MembershipUpdate();
					$MembersipUpdateInsert->customers_id = $customerID;
					$MembersipUpdateInsert->plan_id = $planID;
					$MembersipUpdateInsert->upgrade_date = $next_billing_date;
					$MembersipUpdateInsert->save();

					/*$membershipBillingArray = array(
						'customers_id' => $customerID,
						'error' => 'Free Trial Started (' . $periodTime . ' ' . $periodType . ')',
						'date' => 'now()',
						'status' => 'F'
					);
					tep_db_perform(TABLE_MEMBERSHIP_BILLING_REPORT, $membershipBillingArray);*/
					$membershipBillingReportInsert = new MembershipBillingReport();
					$membershipBillingReportInsert->customers_id = $customerID;
					$membershipBillingReportInsert->error = 'Free Trial Started (' . $periodTime . ' ' . $periodType . ')';
					$membershipBillingReportInsert->date = date('Y-m-d H:i:s');
					$membershipBillingReportInsert->status = 'F';
					$membershipBillingReportInsert->save();


					//tep_db_query('delete from ' . TABLE_MEMBERSHIP_UPDATE_TEMP . ' where customers_id = "' . $customerID . '"');
					$QDeleteMemebershipTemp = Doctrine_Query::create()
						->delete('MembershipUpdateTemp')
						->where('customers_id=?', $customerID)
						->execute();
				}

				if ($planID !== false){
					if ($paymentStatus == 'Denied' || $paymentStatus == 'Reversed' || $paymentStatus == 'Failed' || $paymentStatus == 'Voided' || $paymentStatus == 'Pending'){
						//tep_db_query('insert into ' . TABLE_MEMBERSHIP_BILLING_REPORT . ' (customers_id, error, date, status) values ("' . (int) $customerID . '", "Transaction ' . $paymentStatus . '", now(), "D")');
						$membeshipBillingReport = new MembershipBillingReport();
						$membeshipBillingReport->customers_id = $customerID;
						$membeshipBillingReport->error = 'Transaction ' . $paymentStatus;
						$membeshipBillingReport->date = date('Y-m-d H:i:s');
						$membeshipBillingReport->status = 'D';
						$membeshipBillingReport->save();

					}
				}

				$eventID = false;
				if ($planID !== false && ($paymentStatus == 'Completed' || $paymentStatus == 'FreeTrial')){								
				} 
			}
		}
	}else{
		if (isset($_POST['invoice']) && is_numeric($_POST['invoice']) && ($_POST['invoice'] > 0)){
			//$check_query = tep_db_query("select orders_id from " . TABLE_ORDERS . " where orders_id = '" . $_POST['invoice'] . "' and customers_id = '" . (int) $customerTextId . "'");

			$QOrderC = Doctrine_Query::create()
				->from('Orders')
				->where('orders_id = ?', $_POST['invoice'])
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			if (count($QOrderC) > 0){
				$comment_status = $paymentStatus;

				if ($paymentStatus == 'Pending'){
					$comment_status .= '; ' . $_POST['pending_reason'];
				}elseif (($paymentStatus == 'Reversed') || ($paymentStatus == 'Refunded')){
					$comment_status .= '; ' . $_POST['reason_code'];
				}

				/*tep_db_query("update " . TABLE_ORDERS . " set orders_status = '" . ((sysConfig::get('MODULE_PAYMENT_PAYPALIPN_ORDER_STATUS_ID') > 0) ? sysConfig::get('MODULE_PAYMENT_PAYPALIPN_ORDER_STATUS_ID') : sysConfig::get('DEFAULT_ORDERS_STATUS_ID')) . "', last_modified = now() where orders_id = '" . $_POST['invoice'] . "'");

				$sql_data_array = array(
					'orders_id' => $_POST['invoice'],
					'orders_status_id' => (OrderPaymentModules::getModule('paypalipn')->getConfigData('MODULE_PAYMENT_PAYPALIPN_ORDER_STATUS_ID') > 0) ? OrderPaymentModules::getModule('paypalipn')->getConfigData('MODULE_PAYMENT_PAYPALIPN_ORDER_STATUS_ID') : sysConfig::get('DEFAULT_ORDERS_STATUS_ID'),
					'date_added' => 'now()',
					'customer_notified' => '0',
					'comments' => 'PayPal IPN Invalid [' . $comment_status . ']'
				);
				tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);*/

				$QUpdateOrder = Doctrine_Query::create()
					->update('Orders')
					->set('orders_status', '?', ((OrderPaymentModules::getModule('paypalipn')->getConfigData('MODULE_PAYMENT_PAYPALIPN_ORDER_STATUS_ID') > 0) ? OrderPaymentModules::getModule('paypalipn')->getConfigData('MODULE_PAYMENT_PAYPALIPN_ORDER_STATUS_ID') : sysConfig::get('DEFAULT_ORDERS_STATUS_ID')))
					->set('last_modified', '?', date('Y-m-d H:i:s'))
					->where('orders_id = ?', $_POST['invoice'])
					->execute();

				$OrdersStatusHistory = new OrdersStatusHistory();
				$OrdersStatusHistory->orders_id = $_POST['invoice'];
				$OrdersStatusHistory->orders_status_id = (OrderPaymentModules::getModule('paypalipn')->getConfigData('MODULE_PAYMENT_PAYPALIPN_ORDER_STATUS_ID') > 0) ? OrderPaymentModules::getModule('paypalipn')->getConfigData('MODULE_PAYMENT_PAYPALIPN_ORDER_STATUS_ID') : sysConfig::get('DEFAULT_ORDERS_STATUS_ID');
				$OrdersStatusHistory->date_added = date('Y-m-d H:i:s');
				$OrdersStatusHistory->customer_notified = '0';
				$OrdersStatusHistory->comments = 'PayPal IPN Invalid [' . $comment_status . ']';
				$OrdersStatusHistory->save();

			}
		}
	}
    
	require('includes/application_bottom.php');
	?>
