<?php
class OrderPaymentPaypalipn extends StandardPaymentModule {

	public function __construct(){
		/*
		 * Default title and description for modules that are not yet installed
		 */
		$this->setTitle('Credit/Debit Card (via PayPal)');
		$this->setDescription('Credit/Debit Card (via PayPal)');
		
		$this->init('paypalipn');
		
		if ($this->isEnabled() === true){
			$this->email_footer = sysLanguage::get('MODULE_PAYMENT_PAYPALIPN_TEXT_EMAIL_FOOTER');
			$this->identifier = 'osCommerce PayPal IPN v2.1';

			if ($this->getConfigData('MODULE_PAYMENT_PAYPALIPN_GATEWAY_SERVER') == 'Live'){
				$this->setFormUrl('https://www.paypal.com/cgi-bin/webscr');
			}else{
				$this->setFormUrl('https://www.sandbox.paypal.com/cgi-bin/webscr');
			}
		}
	}

	function validatePost(){
		global $order, $orderTotalModules, $onePageCheckout, $currencies, $ShoppingCart;
		$userAccount = &Session::getReference('userAccount');
		if (!$onePageCheckout->isMembershipCheckout()){
		$order->createOrder();
		$order->insertOrderTotals();
		$order->insertStatusHistory();
		// initialized for the email confirmation
		$products_ordered = '';

		foreach ($ShoppingCart->getProducts() as $cartProduct) {
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
		}else{
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
							$paymentTerm = 'N';//not used
							$billPrice = tep_add_tax($rentalPlan['price'], $rentalPlan['tax_rate']);

							$nextBillDate = strtotime('+' . $membershipMonths . ' month +' . $membershipDays . ' day');
							if (isset($paymentTerm)){
								if ($paymentTerm == 'M'){
									$nextBillDate = strtotime('+1 month');
								}elseif ($paymentTerm == 'Y'){
									$nextBillDate = strtotime('+12 month');
								}
							}

							if ($rentalPlan['free_trial'] > 0){
								$freeTrialPeriod = $rentalPlan['free_trial'];
								$freeTrialEnds = time();
								if ($rentalPlan['free_trial'] > 0){
									$nextBillDate = strtotime('+'.$freeTrialPeriod.' day');
									$freeTrialEnds = strtotime('+'.$freeTrialPeriod.' day');
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

					for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
						$order->insertMembershipProduct($order->products[$i], &$products_ordered);
					}

					EventManager::notify('CheckoutProcessPostProcess', &$order);

					//$order->sendNewOrderEmail();
		}
		
		if (isset($order->newOrder['orderID'])){
				$order_id = $order->newOrder['orderID'];
		}else{
				$order_id = '';
		}
		if (Session::exists('cartID')){
			Session::set('cart_PayPal_IPN_ID', Session::get('cartID') . '-' . $order_id);
		}else{
			return false;
		}

		return true;
	}

	function process_cancel_button(){
		$alias = $this->getConfigData('MODULE_PAYMENT_PAYPALIPN_ID');
		$returnUrl = itw_app_link(null, 'account', 'membership_cancel', 'SSL');
		
		$process_button_string = htmlBase::newElement('button')
		->usePreset('continue')
		->setHref('https://www.paypal.com/cgi-bin/webscr?cmd=_subscr-find&alias=' . $alias . '&return=' . $returnUrl)
		->draw();
		return $process_button_string;
	}

	function getHiddenFields(){
		global $order, $currencies, $userAccount, $onePageCheckout;

		$AddressBook =& $userAccount->plugins['addressBook'];
		$billingAddress = $AddressBook->getAddress('billing');
		$deliveryAddress = $AddressBook->getAddress('delivery');
		$planID = $onePageCheckout->onePage['rentalPlan']['id'];
		$deliveryCountryInfo = $AddressBook->getCountryInfo($deliveryAddress['entry_country_id']);
		$billingCountryInfo = $AddressBook->getCountryInfo($billingAddress['entry_country_id']);

		if (Session::exists('planid') && Session::get('planid') < 1 && Session::get('plan_id') > 0) Session::set('planid', Session::get('plan_id'));

		if ($this->getConfigData('MODULE_PAYMENT_PAYPALIPN_CURRENCY') == 'Selected Currency'){
			$my_currency = Session::get('currency');
		}else{
			$my_currency = substr($this->getConfigData('MODULE_PAYMENT_PAYPALIPN_CURRENCY'), 5);
		}

		if (!in_array($my_currency, array('AUD', 'CAD', 'CHF', 'CZK', 'DKK', 'EUR', 'GBP', 'HKD', 'HUF', 'JPY', 'NOK', 'NZD', 'PLN', 'SEK', 'SGD', 'USD'))){
			$my_currency = 'USD';
		}

		$parameters = array();
		if (($this->getConfigData('MODULE_PAYMENT_PAYPALIPN_TRANSACTION_TYPE') == 'Per Item') && ($this->getConfigData('MODULE_PAYMENT_PAYPALIPN_EWP_STATUS') == 'False') && Session::exists('payment_rental') === false){
			$parameters['cmd'] = '_cart';
			$parameters['upload'] = '1';
			$shipping = Session::get('shipping');
			for ($i = 0, $n = sizeof($order->products); $i < $n; $i++){
				$item = $i + 1;
				$tax_value = ($order->products[$i]['tax'] / 100) * $order->products[$i]['final_price'];

				$parameters['item_name_' . $item] = $order->products[$i]['name'];
				$parameters['amount_' . $item] = number_format($order->products[$i]['final_price'] * $currencies->get_value($my_currency), $currencies->get_decimal_places($my_currency));
				$parameters['tax_' . $item] = number_format($tax_value * $currencies->get_value($my_currency), $currencies->get_decimal_places($my_currency));
				$parameters['quantity_' . $item] = $order->products[$i]['qty'];

				if ($i == 0){
					if (sysConfig::get('DISPLAY_PRICE_WITH_TAX') == 'true'){
						$shipping_cost = $order->info['shipping_cost'];
					}else{
						$module = substr($shipping['id'], 0, strpos($shipping['id'], '_'));
						$shipping_tax = tep_get_tax_rate($GLOBALS[$module]->tax_class, $deliveryAddress['entry_country_id'], $order->delivery['zone_id']);
						$shipping_cost = $order->info['shipping_cost'] + tep_calculate_tax($order->info['shipping_cost'], $shipping_tax);
					}

					$parameters['shipping_' . $item] = number_format($shipping_cost * $currencies->get_value($my_currency), $currencies->get_decimal_places($my_currency));
				}

				if (isset($order->products[$i]['attributes'])){
					for ($j = 0, $n2 = sizeof($order->products[$i]['attributes']); $j < $n2; $j++){
						if (sysConfig::get('DOWNLOAD_ENABLED') == 'true'){
							$attributes_query = "select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix, pad.products_attributes_maxdays, pad.products_attributes_maxcount , pad.products_attributes_filename
                                                   from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa
                                                   left join " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad
                                                   on pa.products_attributes_id=pad.products_attributes_id
                                                   where pa.products_id = '" . $order->products[$i]['id'] . "'
                                                   and pa.options_id = '" . $order->products[$i]['attributes'][$j]['option_id'] . "'
                                                   and pa.options_id = popt.products_options_id
                                                   and pa.options_values_id = '" . $order->products[$i]['attributes'][$j]['value_id'] . "'
                                                   and pa.options_values_id = poval.products_options_values_id
                                                   and popt.language_id = '" . Session::get('languages_id') . "'
                                                   and poval.language_id = '" . Session::get('languages_id') . "'";
							$attributes = tep_db_query($attributes_query);
						}else{
							$attributes = tep_db_query("select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa where pa.products_id = '" . $order->products[$i]['id'] . "' and pa.options_id = '" . $order->products[$i]['attributes'][$j]['option_id'] . "' and pa.options_id = popt.products_options_id and pa.options_values_id = '" . $order->products[$i]['attributes'][$j]['value_id'] . "' and pa.options_values_id = poval.products_options_values_id and popt.language_id = '" . Session::get('languages_id') . "' and poval.language_id = '" . Session::get('languages_id') . "'");
						}
						$attributes_values = tep_db_fetch_array($attributes);

						// Unfortunately PayPal only accepts two attributes per product, so the
						// third attribute onwards will not be shown at PayPal
						$parameters['on' . $j . '_' . $item] = $attributes_values['products_options_name'];
						$parameters['os' . $j . '_' . $item] = $attributes_values['products_options_values_name'];
					}
				}
			}

			$parameters['num_cart_items'] = $item;
			$parameters['cmd'] = '_xclick';
			if ($this->getConfigData('MOVE_TAX_TO_TOTAL_AMOUNT') == 'True'){
				// PandA.nl move tax to total amount
				$parameters['amount'] = number_format(($order->info['total'] - $order->info['shipping_cost']) * $currencies->get_value($my_currency), $currencies->get_decimal_places($my_currency));
			}else{
				// default
				$parameters['amount'] = number_format(($order->info['total'] - $order->info['shipping_cost'] - $order->info['tax']) * $currencies->get_value($my_currency), $currencies->get_decimal_places($my_currency));
			}
		}else{
			if ($onePageCheckout->isMembershipCheckout() === false){
				$parameters['cmd'] = '_xclick';
				$parameters['redirect_cmd'] = '_xclick';
				$parameters['item_name'] = sysConfig::get('STORE_NAME');
			}
			$parameters['shipping'] = '0';

			if ($this->getConfigData('MOVE_TAX_TO_TOTAL_AMOUNT') == 'True'){
				// PandA.nl move tax to total amount
				$parameters['amount'] = number_format($order->info['total'] * $currencies->get_value($my_currency), $currencies->get_decimal_places($my_currency));
			}else{
				// default
				$parameters['amount'] = number_format(($order->info['total'] - $order->info['tax']) * $currencies->get_value($my_currency), $currencies->get_decimal_places($my_currency));
			}
		}

		// billing information fix by gravyface
		// for pre-populating the fiels if customer has no PayPal account
		// only works if force shipping address is set to FALSE
		$state_abbr = tep_get_zone_code($deliveryAddress['entry_country_id'], $deliveryAddress['entry_zone_id'], $deliveryAddress['entry_state']);
		$name = $deliveryAddress['entry_firstname'] . ' ' . $deliveryAddress['entry_lastname'];

		$parameters['business'] = $this->getConfigData('MODULE_PAYMENT_PAYPALIPN_ID');
		// let's check what has been defined in the shop admin for the shipping address
		if ($this->getConfigData('MODULE_PAYMENT_PAYPALIPN_SHIPPING') == 'True'){
			// all that matters is that we send the variables
			// what they contain is irrelevant as PayPal overwrites it with the customer's confirmed PayPal address
			// so what we send is probably not what we'll get back
			$parameters['no_shipping'] = '2';
			$parameters['address_name'] = $name;
			$parameters['address_street'] = $deliveryAddress['entry_street_address'];
			$parameters['address_city'] = $deliveryAddress['entry_city'];
			$parameters['address_zip'] = $deliveryAddress['entry_postcode'];
			$parameters['address_state'] = $state_abbr;
			$parameters['address_country_code'] = $deliveryCountryInfo['countries_iso_code_2'];
			$parameters['address_country'] = $deliveryAddress['countries']['countries_name'];
			$parameters['payer_email'] = $onePageCheckout->onePage['info']['email_address'];
		}else{
			$parameters['no_shipping'] = '1';
			$parameters['H_PhoneNumber'] = $onePageCheckout->onePage['info']['telephone'];
			$parameters['first_name'] = $deliveryAddress['entry_firstname'];
			$parameters['last_name'] = $deliveryAddress['entry_lastname'];
			$parameters['address1'] = $deliveryAddress['entry_street_address'];
			$parameters['address2'] = $deliveryAddress['entry_suburb'];
			$parameters['city'] = $deliveryAddress['entry_city'];
			$parameters['zip'] = $deliveryAddress['entry_postcode'];
			$parameters['state'] = $state_abbr;
			$parameters['country'] = $deliveryCountryInfo['countries_iso_code_2'];
			$parameters['email'] = $onePageCheckout->onePage['info']['email_address'];
		}

		$parameters['currency_code'] = $my_currency;
		$parameters['invoice'] = substr(Session::get('cart_PayPal_IPN_ID'), strpos(Session::get('cart_PayPal_IPN_ID'), '-') + 1);
		$parameters['custom'] = Session::getSessionId().';'.$userAccount->getCustomerId();
		//$parameters['custom'] = $userAccount->getCustomerId();
		$parameters['no_note'] = '1';
		//$parameters['notify_url'] = sysConfig::get('HTTP_SERVER') . sysConfig::getDirWsCatalog(). 'gateway_response.php';
		if(sysConfig::get('ENABLE_SSL') == 'true'){
			$parameters['notify_url'] = sysConfig::get('HTTPS_SERVER') . sysConfig::getDirWsCatalog(). 'ext/modules/payment/paypal_ipn/ipn.php';
		}else{
			$parameters['notify_url'] = sysConfig::get('HTTP_SERVER') . sysConfig::getDirWsCatalog(). 'ext/modules/payment/paypal_ipn/ipn.php';
		}
		if ($this->getConfigData('MODULE_PAYMENT_PAYPALIPN_GATEWAY_SERVER') != 'Live'){
			$parameters['test_ipn'] = '1';
		}
		$parameters['cbt'] = '';
		$parameters['rm'] = '2';
		if ($onePageCheckout->isMembershipCheckout() === false){
			$parameters['return'] = itw_app_link('action=sessionClean&order_id='.$parameters['invoice'], 'account', 'default', 'SSL');
			$parameters['cancel_return'] = itw_app_link(null, 'checkout', 'default', 'SSL');
		}else{
			$parameters['return'] = itw_app_link('action=sessionClean&order_id='.$parameters['invoice'], 'account', 'default', 'SSL');
			$parameters['cancel_return'] = itw_app_link(null, 'checkout', 'default', 'SSL');
		}
		$parameters['bn'] = $this->identifier;
		$parameters['lc'] = $billingCountryInfo['countries_iso_code_2'];

		if (tep_not_null($this->getConfigData('MODULE_PAYMENT_PAYPALIPN_PAGE_STYLE'))){
			$parameters['page_style'] = $this->getConfigData('MODULE_PAYMENT_PAYPALIPN_PAGE_STYLE');
		}

		if ($onePageCheckout->isMembershipCheckout() === true){			
			    $Qcustomer = Doctrine_Query::create()
				->from('Customers c')
				->leftJoin('c.CustomersMembership cm')
				->where('c.customers_id = ?', $userAccount->getCustomerId())
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			    if(isset($Qcustomer[0])){
					$QplanInfo = Doctrine_Query::create()
					->from('Membership m')
					->leftJoin('m.MembershipPlanDescription md')
					->where('plan_id=?', $Qcustomer[0]['CustomersMembership']['plan_id'])
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			    }


				if (isset($QplanInfo[0])){
					$planInfo = $QplanInfo[0];

					if ($this->getConfigData('MOVE_TAX_TO_TOTAL_AMOUNT') == 'True'){
						$a3Value = $order->info['total'] * $currencies->get_value($my_currency);
					}else{
						$a3Value = ($order->info['total'] - $order->info['tax']) * $currencies->get_value($my_currency);
					}
					$packagePrice = number_format($a3Value, $currencies->get_decimal_places($my_currency));

					if ($planInfo['free_trial'] > 0){
						$parameters['a1'] = $planInfo['free_trial_amount'];
						$parameters['p1'] = $planInfo['free_trial'];
						$parameters['t1'] = 'D';
					}

					if ($planInfo['membership_days'] > 0){
						$p3Val = $planInfo['membership_days'];
						$t3Val = 'D';
					} elseif ($planInfo['membership_months'] > 0){
						$p3Val = $planInfo['membership_months'];
						$t3Val = 'M';
					}
					
					if (isset($p3Val)){
						$parameters['p3'] = $p3Val;
						$parameters['t3'] = $t3Val;
					}

					$parameters['cmd'] = '_xclick-subscriptions';
					$parameters['custom'] = Session::getSessionId().';'.$userAccount->getCustomerId();
					$parameters['item_name'] = $planInfo['MembershipPlanDescription'][0]['name'];
					$parameters['no_note'] = '1';
					$parameters['a3'] = $packagePrice;
					//$parameters['modify'] = '1';
					$parameters['src'] = '1';
					$parameters['sra'] = '1';
				}
			}

		if ($this->getConfigData('MODULE_PAYMENT_PAYPALIPN_EWP_STATUS') == 'True'){
			$process_button_string = tep_draw_hidden_field('cmd', '_s-xclick') . "\n" .
			tep_draw_hidden_field('encrypted', $this->buildEwpString($parameters)) . "\n";
		}else{
			$process_button_string = '';
			while (list($key, $value) = each($parameters)){
				$process_button_string .= tep_draw_hidden_field($key, $value) . "\n";
			}
		}
		if ($this->getConfigData('MODULE_PAYMENT_PAYPALIPN_DEBUG_EMAIL') != '') {
			$email_body = $process_button_string;
			/*To Debug
			$myFile = sysConfig::getDirFsCatalog(). 'file2.txt';
			$fh = fopen($myFile, 'a') or die("can't open file");
			fwrite($fh, '\n'.$email_body.'\n');
			fclose($fh);
			End Debug*/
			tep_mail(sysConfig::get('STORE_OWNER'), $this->getConfigData('MODULE_PAYMENT_PAYPALIPN_DEBUG_EMAIL'), sprintf(sysLanguage::get('EMAIL_SUBJECT'),'PayPal Debug'), htmlentities($email_body), 'Paypal Debug', sysConfig::get('STORE_OWNER_EMAIL_ADDRESS'));
		}

		//echo $process_button_string;
		//itwExit();
		return $process_button_string;
	}
	
	private function buildEwpString(&$parameters){
		global $userAccount;
		$parameters['cert_id'] = $this->getConfigData('MODULE_PAYMENT_PAYPALIPN_EWP_CERT_ID');
		$random_string = rand(100000, 999999) . '-' . $userAccount->getCustomerId() . '-';
		$data = '';
		while (list($key, $value) = each($parameters)){
			$data .= $key . '=' . $value . "\n";
		}
		
		$openSslPath = $this->getConfigData('MODULE_PAYMENT_PAYPALIPN_EWP_OPENSSL');
		$ipnId = $this->getConfigData('MODULE_PAYMENT_PAYPALIPN_ID');
		$ewpWorkingDir = $this->getConfigData('MODULE_PAYMENT_PAYPALIPN_EWP_WORKING_DIRECTORY') . '/';
		$payPalKeyFile = $this->getConfigData('MODULE_PAYMENT_PAYPALIPN_EWP_PUBLIC_KEY');
		$publicKeyFile = $this->getConfigData('MODULE_PAYMENT_PAYPALIPN_EWP_PUBLIC_KEY');
		$privateKeyFile = $this->getConfigData('MODULE_PAYMENT_PAYPALIPN_EWP_PRIVATE_KEY');
		$payPalKey = file_get_contents($payPalKeyFile);
		$publicKey = file_get_contents($publicKeyFile);
		$privateKey = file_get_contents($privateKeyFile);
		$dataFile = $ewpWorkingDir . $random_string . 'data.txt';
		$signedFile = $ewpWorkingDir . $random_string . 'signed.txt';
		$encryptedFile = $ewpWorkingDir . $random_string . 'encrypted.txt';

		$fp = fopen($dataFile, 'w');
		fwrite($fp, $data);
		fclose($fp);
		unset($data);

		if (function_exists('openssl_pkcs7_sign') && function_exists('openssl_pkcs7_encrypt')){
			openssl_pkcs7_sign($dataFile, $signedFile, $publicKey, $privateKey, array('From' => $ipnId), PKCS7_BINARY);
			unlink($dataFile);
			// remove headers from the signature
			$signed = file_get_contents($signedFile);
			$signed = explode("\n\n", $signed);
			$signed = base64_decode($signed[1]);

			$fp = fopen($signedFile, 'w');
			fwrite($fp, $signed);
			fclose($fp);
			unset($signed);

			openssl_pkcs7_encrypt($signedFile, $encryptedFile, $payPalKey, array('From' => $ipnId), PKCS7_BINARY);
			unlink($signedFile);

			// remove headers from the encrypted result
			$data = file_get_contents($encryptedFile);
			$data = explode("\n\n", $data);
			$data = '-----BEGIN PKCS7-----' . "\n" . $data[1] . "\n" . '-----END PKCS7-----';

			unlink($encryptedFile);
		}else{
			exec($openSslPath . ' smime -sign -in ' . $dataFile . ' -signer ' . $publicKeyFile . ' -inkey ' . $privateKeyFile . ' -outform der -nodetach -binary > ' . $signedFile);
			unlink($dataFile);

			exec($openSslPath . ' smime -encrypt -des3 -binary -outform pem ' . $payPalKeyFile . ' < ' . $signedFile . ' > ' . $encryptedFile);
			unlink($signedFile);

			$fh = fopen($encryptedFile, 'rb');
			$data = fread($fh, filesize($encryptedFile));
			fclose($fh);

			unlink($encryptedFile);
		}
		return $data;
	}

	function processPayment(){
		Session::remove('cart_PayPal_IPN_ID');
		/*global $ShoppingCart;

		$ShoppingCart->emptyCart(true);
		// unregister session variables used during checkout
		Session::remove('sendto');
		Session::remove('billto');
		Session::remove('shipping');
		Session::remove('payment');
		Session::remove('comments');

		Session::remove('cart_PayPal_IPN_ID');
		tep_redirect(itw_app_link(null, 'checkout', 'success', 'SSL'));*/
	}

	function processPaymentCron($orderID){
		global $order;
		$order->info['payment_method'] = $this->getTitle();
		
		//$this->processPayment();
		return true;
	}

	function onInstall(){

		$Qstatus = Doctrine_Query::create()
		->select('s.orders_status_id, sd.orders_status_name')
		->from('OrdersStatus s')
		->leftJoin('s.OrdersStatusDescription sd')
		->where('sd.language_id = ?', (int) Session::get('languages_id'))
		->andWhere('sd.orders_status_name=?', 'Preparing [PayPal IPN]')
		->orderBy('s.orders_status_id')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		if(count($Qstatus) > 0){
			$status_id = $Qstatus[0]['order_status_id'];
		}else{
			$OrdersStatus = Doctrine_Core::getTable('OrdersStatus');
			$Status = $OrdersStatus->getRecord();
			$Description = $Status->OrdersStatusDescription;
			foreach(sysLanguage::getLanguages() as $lInfo){
				$Description[$lInfo['id']]->language_id = $lInfo['id'];
				$Description[$lInfo['id']]->orders_status_name =  'Preparing [PayPal IPN]';
			}

			$Status->save();
			$status_id = $Status->orders_status_id;
		}
		
		$Qupdate = Doctrine_Query::create()
		->update('ModulesConfiguration')
		->set('configuration_value', '?', (empty($status_id) ? '1' : $status_id))
		->where('configuration_key = ?', 'MODULE_PAYMENT_PAYPALIPN_PREPARE_ORDER_STATUS_ID')
		->execute();
	}
}
?>