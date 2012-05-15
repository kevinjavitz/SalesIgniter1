<?php

$_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en';
$textArr = array();
$textArr = explode(';', $_POST['merchant_fields']);
$_GET['osCID'] = $textArr[0];
$_REQUEST['osCID'] = $textArr[0];
$_POST['osCID'] = $textArr[0];
$customerTextId = $textArr[1];
if(isset($textArr[2])){
        $customerIp = $textArr[2];
}
$nonrecurring = false;
if(isset($textArr[3]) && $textArr[3] == 'nonrecurring'){
        $nonrecurring = true;
}

if(isset($textArr[4])){
        $refid = $textArr[4];
}else{
        $refid = 'n';
}
if(isset($textArr[4])){
        $account_action = $textArr[4];
}
        
chdir('../../../../');
require('includes/application_top.php');
require('includes/classes/order.php');

	// Validate the Moneybookers signature
$concatFields = $_POST['merchant_id']
    .$_POST['transaction_id']
    .strtoupper(md5(OrderPaymentModules::getModule('skrill_gateway')->getConfigData('MODULE_PAYMENT_MONEYBOOKERSGATEWAY_WORD')))
    .$_POST['mb_amount']
    .$_POST['mb_currency']
    .$_POST['status'];

$MBEmail = OrderPaymentModules::getModule('skrill_gateway')->getConfigData('MODULE_PAYMENT_MONEYBOOKERSGATEWAY_ID');

if(isset($_POST['status'])){
		$paymentStatus = $_POST['status'];
}

if(isset($_POST['transaction_id'])){
		$orderID = (int)$_POST['transaction_id'];
}

$customerID = (int)$customerTextId;

$planID = false;

// Ensure the signature is valid, the status code == 2,
// and that the money is going to you
if (strtoupper(md5($concatFields)) == $_POST['md5sig']
    && $_POST['pay_to_email'] == $MBEmail)
{
        // Valid transaction.
   if ($paymentStatus){
            
            if($nonrecurring){
		$account_action = 'payment';
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
		if ($account_action == 'upgrade' || $account_action == 'cancel'){

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

			if ((isset($_POST['amount']) && (int)$_POST['amount'] == (int) $new_amount) || (isset($_POST['rec_amount']) && (int)$_POST['rec_amount'] == (int) $new_amount)){
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
            
            if (is_numeric($orderID) && $orderID > 0){
			
			//$order_query = tep_db_query("select currency, currency from " . TABLE_ORDERS . " where orders_id = '" . $orderID . "' and customers_id = '" . $customerID . "'");
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
				$comment_status = $paymentStatus . ' (' . ucfirst($_POST['status']) . '; ' . $currencies->format($_POST['amount'], false, $_POST['currency']) . ')';

                                if (($paymentStatus == '2') && (OrderPaymentModules::getModule('skrill_gateway')->getConfigData('MODULE_PAYMENT_MONEYBOOKERS_SHIPPING') == 'True')){
					$comment_status .= ", \n" . OrderPaymentModules::getModule('skrill_gateway')->getConfigData('MODULE_PAYMENT_MONEYBOOKERS_ID') . ": " . $_POST['payment_type'] ;
				}
				$order_status_id = sysConfig::get('DEFAULT_ORDERS_STATUS_ID');

				$pricing = ((number_format($QTotals->value * $QOrder[0]['currency_value'], $currencies->get_decimal_places($QOrder[0]['currency']))) - $_POST['amount']);

				if ($pricing <= 0.05 && $pricing >= -0.05){
					if (((int)OrderPaymentModules::getModule('skrill_gateway')->getConfigData('MODULE_PAYMENT_MONEYBOOKERS_COMP_ORDER_STATUS_ID') > 0) && ($paymentStatus == '2')){
						if(!empty($account_action)){
							$QUpdateCustomerMemberShip = Doctrine_Query::create()
							->update('CustomersMembership')
							->set('activate', '?', 'Y')
							->where('customers_id=?',$customerID)
							->execute();

						}
						
						EventManager::notify('CheckoutSuccessRemoteFinish',$orderID, $_POST['amount'], $customerIp, $refid, $customerID, $planID);
						$order_status_id = OrderPaymentModules::getModule('skrill_gateway')->getConfigData('MODULE_PAYMENT_MONEYBOOKERS_COMP_ORDER_STATUS_ID');
						$newStatus = new OrdersPaymentsHistory();
						$newStatus->orders_id = $orderID;
						$newStatus->payment_module = 'skrill_gateway';
						$newStatus->payment_method = 'MoneyBookers';
						$newStatus->gateway_message = 'Successfull payment';
						$newStatus->payment_amount = $_POST['amount'];
						$newStatus->card_details = 'NULL';
						$newStatus->save();
						$order->info['payment_module'] = 'MoneyBookers';
						$order->sendNewOrderEmail();
					}elseif (OrderPaymentModules::getModule('skrill_gateway')->getConfigData('MODULE_PAYMENT_MONEYBOOKERS_ORDER_STATUS_ID') > 0){
						$order_status_id = OrderPaymentModules::getModule('skrill_gateway')->getConfigData('MODULE_PAYMENT_MONEYBOOKERS_ORDER_STATUS_ID');
					}
				}				
				$customer_notified = '0';
				if (($paymentStatus == '0') || ($paymentStatus == '2')){
					$customer_notified = '1';
				}

				$QUpdateOrder = Doctrine_Query::create()
					->update('Orders')
					->set('orders_status', '?', $order_status_id)
					->set('last_modified', '?', date('Y-m-d H:i:s'))
					->where('orders_id=?',$orderID)
					->execute();

				$OrdersStatusHistory = new OrdersStatusHistory();
				$OrdersStatusHistory->orders_id = $orderID;
				$OrdersStatusHistory->orders_status_id = $order_status_id;
				$OrdersStatusHistory->date_added = date('Y-m-d H:i:s');
				$OrdersStatusHistory->customer_notified = $customer_notified;
				$OrdersStatusHistory->comments = 'MoneyBookers GATEWAY Verified [' . $comment_status . ']';
				$OrdersStatusHistory->save();



				//insert payment History				
				if ($planID !== false && $account_action = 'free_trial' && isset($_POST['rec_cycle'])){
					$periodType = $_POST['rec_cycle'];
                                        $periodTime = $_POST['rec_period'];
                                   
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
					if ($paymentStatus == '-2' || $paymentStatus == '-3' || $paymentStatus == '0'){
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
        
        }
        if($paymentStatus == '-2'){
	
            $QOrderC = Doctrine_Query::create()
                    ->from('Orders')
                    ->where('orders_id = ?', $_POST['transaction_id'])
                    ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

            if (count($QOrderC) > 0){
                    $comment_status = $paymentStatus;

            if ($paymentStatus == '-2'){
                    $comment_status .= '; ' . $_POST['failed_reason_code'];
            }


            $QUpdateOrder = Doctrine_Query::create()
                    ->update('Orders')
                    ->set('orders_status', '?', ((OrderPaymentModules::getModule('skrill_gateway')->getConfigData('MODULE_PAYMENT_MONEYBOOKERS_ORDER_STATUS_ID') > 0) ? OrderPaymentModules::getModule('skrill_gateway')->getConfigData('MODULE_PAYMENT_MONEYBOOKERS_ORDER_STATUS_ID') : sysConfig::get('DEFAULT_ORDERS_STATUS_ID')))
                    ->set('last_modified', '?', date('Y-m-d H:i:s'))
                    ->where('orders_id = ?', $_POST['invoice'])
                    ->execute();

            $OrdersStatusHistory = new OrdersStatusHistory();
            $OrdersStatusHistory->orders_id = $_POST['transaction_id'];
            $OrdersStatusHistory->orders_status_id = (OrderPaymentModules::getModule('skrill_gateway')->getConfigData('MODULE_PAYMENT_MONEYBOOKERS_ORDER_STATUS_ID') > 0) ? OrderPaymentModules::getModule('skrill_gateway')->getConfigData('MODULE_PAYMENT_MONEYBOOKERS_ORDER_STATUS_ID') : sysConfig::get('DEFAULT_ORDERS_STATUS_ID');
            $OrdersStatusHistory->date_added = date('Y-m-d H:i:s');
            $OrdersStatusHistory->customer_notified = '0';
            $OrdersStatusHistory->comments = 'MoneyBookers Invalid [' . $comment_status . ']';
            $OrdersStatusHistory->save();

	}
    }
}
else
{
       
    exit;
}
    
	
?>
