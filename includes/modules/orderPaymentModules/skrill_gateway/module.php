<?php
class OrderPaymentSkrill_gateway extends StandardPaymentModule
{

	public function __construct() {
		/*
		 * Default title and description for modules that are not yet installed
		 */
		$this->setTitle('MoneyBookers gateway');
		$this->setDescription('MoneyBookers gateway');

		$this->init('skrill_gateway');

		if ($this->isEnabled() === true){
			$this->email_footer = sysLanguage::get('MODULE_PAYMENT_MONEYBOOKERS_GATEWAY_TEXT_EMAIL_FOOTER');
			$this->identifier = 'osCommerce MoneyBookers Gateway';

			if ($this->getConfigData('MODULE_PAYMENT_MONEYBOOKERS_GATEWAY_SERVER') == 'Live'){
				$this->setFormUrl('https://www.moneybookers.com/app/payment.pl');
			}
			else {
				$this->setFormUrl('https://www.moneybookers.com/app/payment.pl');
			}
		}
	}

	function validatePost() {
		global $order, $orderTotalModules, $onePageCheckout, $currencies, $ShoppingCart;
		$userAccount = &Session::getReference('userAccount');
		if (!$onePageCheckout->isMembershipCheckout()){
			$order->createOrder();
			$order->insertOrderTotals();
			$order->insertStatusHistory();
			// initialized for the email confirmation
			$products_ordered = '';

			foreach($ShoppingCart->getProducts() as $cartProduct){
				$order->insertOrderedProduct($cartProduct, &$products_ordered);

				EventManager::notify('CheckoutProcessInsertOrderedProduct', $cartProduct, &$products_ordered);
				// #################### Added CCGV ######################
				//$orderTotalModules->update_credit_account($cartProduct);//ICW ADDED FOR CREDIT CLASS SYSTEM
				// #################### End Added CCGV ######################
			}

			// lets start with the email confirmation
			// #################### Added CCGV ######################
			//$orderTotalModules->apply_credit();//ICW ADDED FOR CREDIT CLASS SYSTEM
			// #################### End Added CCGV ######################

			EventManager::notify('CheckoutProcessPostProcess', &$order);
			//$order->sendNewOrderEmail();
		}
		else {
			$order->info['is_rental'] = '1';
			$order->info['bill_attempts'] = '1';
			$planID = $onePageCheckout->onePage['rentalPlan']['id'];

			$order->createOrder();

			if (isset($onePageCheckout->onePage['info']['account_action']) === true){
				if (isset($onePageCheckout->onePage['info']['payment'])){
					$paymentInfo = $onePageCheckout->onePage['info']['payment'];
					$rentalPlan = $onePageCheckout->onePage['rentalPlan'];

					$membershipMonths = $rentalPlan['months'];
					$membershipDays = $rentalPlan['days'];
					$numberOfRentals = $rentalPlan['no_of_titles'];
					$paymentTerm = 'N'; //not used
					$billPrice = tep_add_tax($rentalPlan['price'], $rentalPlan['tax_rate']);

					$nextBillDate = strtotime('+' . $membershipMonths . ' month +' . $membershipDays . ' day');
					if (isset($paymentTerm)){
						if ($paymentTerm == 'M'){
							$nextBillDate = strtotime('+1 month');
						}
						elseif ($paymentTerm == 'Y') {
							$nextBillDate = strtotime('+12 month');
						}
					}

					if ($rentalPlan['free_trial'] > 0){
						$freeTrialPeriod = $rentalPlan['free_trial'];
						$freeTrialEnds = time();
						if ($rentalPlan['free_trial'] > 0){
							$nextBillDate = strtotime('+' . $freeTrialPeriod . ' day');
							$freeTrialEnds = strtotime('+' . $freeTrialPeriod . ' day');
						}

						if ($freeTrialEnds > time() && $rentalPlan['free_trial_amount'] > 0){
							$billPrice = tep_add_tax($rentalPlan['free_trial_amount'], $rentalPlan['tax_rate']);
						}
					}

					$membership =& $userAccount->plugins['membership'];
					$membership->setPlanId($planID);
					$membership->setMembershipStatus('M');
					$membership->setActivationStatus('N');
					if (isset($freeTrialEnds)){
						$membership->setFreeTrailEnd($freeTrialEnds);
					}
					$membership->setNextBillDate($nextBillDate);
					$membership->setPaymentTerm($paymentTerm);
					$membership->setPaymentMethod($onePageCheckout->onePage['info']['payment']['id']);
					$membership->setRentalAddress($userAccount->plugins['addressBook']->getDefaultAddressId());
					if (!empty($paymentInfo['cardDetails']['cardNumber'])){
						$membership->setCreditCardNumber($paymentInfo['cardDetails']['cardNumber']);
						$membership->setCreditCardExpirationDate($paymentInfo['cardDetails']['cardExpMonth'] . $paymentInfo['cardDetails']['cardExpYear']);
						if (!empty($paymentInfo['cardDetails']['cardCvvNumber'])){
							$membership->setCreditCardCvvNumber($paymentInfo['cardDetails']['cardCvvNumber']);
						}
					}
					$membership->createNewMembership();
				}
			}

			$order->insertOrderTotals();
			$order->insertStatusHistory();

			$products_ordered = '';

			for($i = 0, $n = sizeof($order->products); $i < $n; $i++){
				$order->insertMembershipProduct($order->products[$i], &$products_ordered);
			}

			EventManager::notify('CheckoutProcessPostProcess', &$order);
			//$order->sendNewOrderEmail();
		}

		if (isset($order->newOrder['orderID'])){
			$order_id = $order->newOrder['orderID'];
		}
		else {
			$order_id = '';
		}
		if (Session::exists('cartID')){
			Session::set('cart_MoneyBookers_GATEWAY_ID', Session::get('cartID') . '-' . $order_id);
		}
		else {
			return false;
		}

		return true;
	}

	function process_cancel_button() {
		$alias = $this->getConfigData('MODULE_PAYMENT_MONEYBOOKERSGATEWAY_ID');
		$returnUrl = itw_app_link(null, 'account', 'membership_cancel', 'SSL');

		$process_button_string = htmlBase::newElement('button')
			->usePreset('continue')
			->setHref(' https://www.moneybookers.com/app/payment.pl' . $alias . '&return=' . $returnUrl)
			->draw();
		return $process_button_string;
	}

	function getHiddenFields() {
		global $order, $currencies, $userAccount, $onePageCheckout;

		$AddressBook =& $userAccount->plugins['addressBook'];
		$billingAddress = $AddressBook->getAddress('billing');
		$deliveryAddress = $AddressBook->getAddress('delivery');
		$planID = $onePageCheckout->onePage['rentalPlan']['id'];
		$deliveryCountryInfo = $AddressBook->getCountryInfo($deliveryAddress['entry_country_id']);
		$billingCountryInfo = $AddressBook->getCountryInfo($billingAddress['entry_country_id']);

		if (Session::exists('planid') && Session::get('planid') < 1 && Session::get('plan_id') > 0) {
			Session::set('planid', Session::get('plan_id'));
		}

		if ($this->getConfigData('MODULE_PAYMENT_MONEYBOOKERSGATEWAY_CURRENCY') == 'Selected Currency'){
			$my_currency = Session::get('currency');
		}
		else {
			$my_currency = substr($this->getConfigData('MODULE_PAYMENT_MONEYBOOKERSGATEWAY_CURRENCY'), 5);
		}

		if (!in_array($my_currency, array('AUD', 'CAD', 'CHF', 'CZK', 'DKK', 'EUR', 'GBP', 'HKD', 'HUF', 'JPY', 'NOK', 'NZD', 'PLN', 'SEK', 'SGD', 'USD'))){
			$my_currency = 'USD';
		}

		$parameters = array();
		

		// billing information fix by gravyface
		// for pre-populating the fiels if customer has no MoneyBookers account
		// only works if force shipping address is set to FALSE
		$state_abbr = tep_get_zone_code($deliveryAddress['entry_country_id'], $deliveryAddress['entry_zone_id'], $deliveryAddress['entry_state']);
		$name = $deliveryAddress['entry_firstname'] . ' ' . $deliveryAddress['entry_lastname'];

                //Merchant details
		$parameters['pay_to_email'] = $this->getConfigData('MODULE_PAYMENT_MONEYBOOKERSGATEWAY_ID');
                $parameters['recipient_description'] = sysConfig::get('STORE_NAME');
                $parameters['transaction_id'] = substr(Session::get('cart_MoneyBookers_GATEWAY_ID'), strpos(Session::get('cart_MoneyBookers_GATEWAY_ID'), '-') + 1);
                
                if (sysConfig::get('ENABLE_SSL') == 'true'){
                        //$parameters['return_url'] = sysConfig::get('HTTPS_SERVER') . sysConfig::getDirWsCatalog() . 'ext/modules/payment/skrill_gateway/gateway.php';
			$parameters['status_url'] = sysConfig::get('HTTPS_SERVER') . sysConfig::getDirWsCatalog() . 'ext/modules/payment/skrill_gateway/status.php';
		}
		else {
                        //$parameters['return_url'] = sysConfig::get('HTTP_SERVER') . sysConfig::getDirWsCatalog() . 'ext/modules/payment/skrill_gateway/gateway.php';
			$parameters['status_url'] = sysConfig::get('HTTP_SERVER') . sysConfig::getDirWsCatalog() . 'ext/modules/payment/skrill_gateway/status.php';
		}
                
                $parameters['return_url_text'] = sysLanguage::get('TEXT_RETURN_MERCHANT'). sysConfig::get('STORE_NAME');
                
                if ($this->getConfigData('MODULE_PAYMENT_MONEYBOOKERSGATEWAY_GATEWAY_HIDELOGIN') != 'Live'){
			$parameters['hide_login'] = '1';
		}
                
                $parameters['logo_url'] = sysConfig::get('HTTPS_SERVER') . sysConfig::getDirWsCatalog() .'/templates/newred/images/logo.png';
                $parameters['merchant_fields'] = Session::getSessionId() . ';' . $userAccount->getCustomerId() . ';' . $_SERVER['REMOTE_ADDR'];
		
                
                //Customer details
                $parameters['pay_from_email'] = $onePageCheckout->onePage['info']['email_address'];
                $parameters['firstname'] = $deliveryAddress['entry_firstname'];
		$parameters['lastname'] = $deliveryAddress['entry_lastname'];
                $parameters['address1'] = $deliveryAddress['entry_street_address'];
		$parameters['address2'] = $deliveryAddress['entry_suburb'];
                $parameters['phone_number'] = $onePageCheckout->onePage['info']['telephone'];
                $parameters['postal_code'] = $deliveryAddress['entry_postcode'];
                $parameters['city'] = $deliveryAddress['entry_city'];
                $parameters['state'] = $state_abbr;
                $parameters['country'] = $deliveryCountryInfo['countries_iso_code_3'];
                
                //Payment details
                $parameters['currency'] = $my_currency;
                $parameters['detail1_description'] = "Id:";
                $parameters['detail1_text'] = substr(Session::get('cart_MoneyBookers_GATEWAY_ID'), strpos(Session::get('cart_MoneyBookers_GATEWAY_ID'), '-') + 1);
                
                		
		if ($this->getConfigData('MODULE_PAYMENT_MONEYBOOKERSGATEWAY_GATEWAY_SERVER') != 'Live'){
			$parameters['test_gateway'] = '1';
		}
		
		if ($onePageCheckout->isMembershipCheckout() === false){
			$parameters['return_url'] = itw_app_link(null, 'checkout', 'success', 'SSL');
			$parameters['cancel_url'] = itw_app_link(null, 'checkout', 'default', 'SSL');
		}
		else {
			$parameters['return_url'] = itw_app_link(null, 'checkout', 'success', 'SSL');
			$parameters['cancel_url'] = itw_app_link(null, 'checkout', 'default', 'SSL');
		}
		
		if ($onePageCheckout->isMembershipCheckout() === true){
			$Qcustomer = Doctrine_Query::create()
				->from('Customers c')
				->leftJoin('c.CustomersMembership cm')
				->where('c.customers_id = ?', $userAccount->getCustomerId())
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if (isset($Qcustomer[0])){
				$QplanInfo = Doctrine_Query::create()
					->from('Membership m')
					->leftJoin('m.MembershipPlanDescription md')
					->where('plan_id=?', $Qcustomer[0]['CustomersMembership']['plan_id'])
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			}

			if (isset($QplanInfo[0])){
				$planInfo = $QplanInfo[0];

				$a3Value = $order->info['total'] * $currencies->get_value($my_currency);
				
				
				$packagePrice = number_format($a3Value, $currencies->get_decimal_places($my_currency));

				if ($planInfo['membership_days'] > 0){
					$p3Val = $planInfo['membership_days'];
					$t3Val = 'day';
                                       
				}
				elseif ($planInfo['membership_months'] > 0) {
					$p3Val = $planInfo['membership_months'];
					$t3Val = 'month';
				}

				if($planInfo['reccurring'] == 1){
					$parameters['merchant_fields'] = Session::getSessionId() . ';' . $userAccount->getCustomerId() . ';' . $_SERVER['REMOTE_ADDR']. ';'.'f'.';'.(Session::exists('refid')?Session::get('refid'):'n');
					if (isset($p3Val)){
						$parameters['rec_period'] = $p3Val;
						$parameters['rec_cycle'] = $t3Val;
                                                $parameters['rec_amount'] = $packagePrice;
					}
					if ($planInfo['free_trial'] > 0){
						$parameters['rec_period'] = $planInfo['free_trial'];
						$parameters['rec_cycle'] = 'day';
                                                $parameters['rec_amount'] = $planInfo['free_trial_amount'];
					}										
					
				}else{
					$parameters['merchant_fields'] = Session::getSessionId() . ';' . $userAccount->getCustomerId() . ';' . $_SERVER['REMOTE_ADDR'] .';nonrecurring'.';'.(Session::exists('refid')?Session::get('refid'):'n');
					$parameters['amount'] = $packagePrice;
					
                                }
                                
                                if (sysConfig::get('ENABLE_SSL') == 'true'){
                                        $parameters['rec_status_url'] = sysConfig::get('HTTPS_SERVER') . sysConfig::getDirWsCatalog() . 'ext/modules/payment/skrill_gateway/gateway.php';
                                }
                                else {
                                        $parameters['rec_status_url'] = sysConfig::get('HTTP_SERVER') . sysConfig::getDirWsCatalog() . 'ext/modules/payment/skrill_gateway/gateway.php';
                                }
                        }

                }else{
					
			$parameters['amount'] = number_format(($order->info['total']) * $currencies->get_value($my_currency), $currencies->get_decimal_places($my_currency));
		}
                
                if (isset($onePageCheckout->onePage['info']['account_action']) === true){
			$parameters['merchant_fields'] .= $onePageCheckout->onePage['info']['account_action']; 
                }
                    
		$process_button_string = '';
		while(list($key, $value) = each($parameters)){
			$process_button_string .= tep_draw_hidden_field($key, $value) . "\n";
		}
		
		if ($this->getConfigData('MODULE_PAYMENT_MONEYBOOKERSGATEWAY_DEBUG_EMAIL') != ''){
			$email_body = $process_button_string;
			/*To Debug
			$myFile = sysConfig::getDirFsCatalog(). 'file2.txt';
			$fh = fopen($myFile, 'a') or die("can't open file");
			fwrite($fh, '\n'.$email_body.'\n');
			fclose($fh);
			End Debug*/
			tep_mail(sysConfig::get('STORE_OWNER'), $this->getConfigData('MODULE_PAYMENT_MONEYBOOKERSGATEWAY_DEBUG_EMAIL'), sprintf(sysLanguage::get('EMAIL_SUBJECT'), 'MoneyBookers Debug'), htmlentities($email_body), 'MoneyBookers Debug', sysConfig::get('STORE_OWNER_EMAIL_ADDRESS'));
		}

		//echo $process_button_string;
		//itwExit();
		return $process_button_string;
	}

	function processPayment($orderID = null, $amount = null){
		Session::remove('cart_MoneyBookers_GATEWAY_ID');
		/*global $ShoppingCart;

		$ShoppingCart->emptyCart(true);
		// unregister session variables used during checkout
		Session::remove('sendto');
		Session::remove('billto');
		Session::remove('shipping');
		Session::remove('payment');
		Session::remove('comments');

		Session::remove('cart_MoneyBookers_IPN_ID');
		tep_redirect(itw_app_link(null, 'checkout', 'success', 'SSL'));*/
	}

	function processPaymentCron($orderID) {
		global $order;
		$order_status_id = OrderPaymentModules::getModule('skrill_gateway')->getConfigData('MODULE_PAYMENT_MONEYBOOKERSGATEWAY_COMP_ORDER_STATUS_ID');
		$newStatus = new OrdersPaymentsHistory();
		$newStatus->orders_id = $orderID;
		$newStatus->payment_module = 'skrill_gateway';
		$newStatus->payment_method = 'MoneyBookers';
		$newStatus->gateway_message = 'Successfull payment';
		$newStatus->payment_amount = $order->info['total'];
		$newStatus->card_details = 'NULL';
		$newStatus->save();

		Doctrine_Query::create()
		->update('Orders')
		->set('orders_status', '?', $order_status_id)
		->set('last_modified', '?', date('Y-m-d H:i:s'))
		->where('orders_id = ?', $orderID)
		->execute();

		$newOrdersStatus = new OrdersStatusHistory();
		$newOrdersStatus->orders_id = $orderID;
		$newOrdersStatus->orders_status_id = $order_status_id;
		$newOrdersStatus->date_added = date('Y-m-d H:i:s');
		$newOrdersStatus->customer_notified = '0';
		$newOrdersStatus->comments = 'MoneyBookers GATEWAY (Not Verified Transaction)';
		$newOrdersStatus->save();
		$order->info['payment_method'] = $this->getTitle();

		return true;
	}

	function onInstall() {

		$Qstatus = Doctrine_Query::create()
			->select('s.orders_status_id, sd.orders_status_name')
			->from('OrdersStatus s')
			->leftJoin('s.OrdersStatusDescription sd')
			->where('sd.language_id = ?', (int)Session::get('languages_id'))
			->andWhere('sd.orders_status_name=?', 'Preparing [MoneyBookers GATEWAY]')
			->orderBy('s.orders_status_id')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		if (count($Qstatus) > 0){
			$status_id = $Qstatus[0]['order_status_id'];
		}
		else {
			$OrdersStatus = Doctrine_Core::getTable('OrdersStatus');
			$Status = $OrdersStatus->getRecord();
			$Description = $Status->OrdersStatusDescription;
			foreach(sysLanguage::getLanguages() as $lInfo){
				$Description[$lInfo['id']]->language_id = $lInfo['id'];
				$Description[$lInfo['id']]->orders_status_name = 'Preparing [MoneyBookers GATEWAY]';
			}

			$Status->save();
			$status_id = $Status->orders_status_id;
		}

		$Qupdate = Doctrine_Query::create()
			->update('ModulesConfiguration')
			->set('configuration_value', '?', (empty($status_id) ? '1' : $status_id))
			->where('configuration_key = ?', 'MODULE_PAYMENT_MONEYBOOKERSGATEWAY_PREPARE_ORDER_STATUS_ID')
			->execute();
	}
}

?>