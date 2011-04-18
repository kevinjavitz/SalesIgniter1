<?php
class OrderPaymentDotpay extends StandardPaymentModule {

	public function __construct(){
		/*
		 * Default title and description for modules that are not yet installed
		 */
		$this->setTitle('Credit/Debit Card (via Dotpay)');
		$this->setDescription('Credit/Debit Card (via Dotpay)');
		
		$this->init('dotpay');
		
		if ($this->isEnabled() === true){
			$this->identifier = 'Dotpay';
			$this->setFormUrl('https://ssl.dotpay.eu/');
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
		}
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
							$paymentTerm = $rentalPlan['pay_term'];
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
			Session::set('cart_DOTPAY_ID', Session::get('cartID') . '-' . $order_id);
		}else{
			return false;
		}

		return true;
	}

	function getHiddenFields(){
		global $order, $currencies, $userAccount, $onePageCheckout;
		//$userAccount = &Session::getReference('userAccount');
		$AddressBook =& $userAccount->plugins['addressBook'];
		$billingAddress = $AddressBook->getAddress('billing');
		$deliveryAddress = $AddressBook->getAddress('delivery');
		  $my_order = sysConfig::get('STORE_NAME') . " - " . date('Ymdhis') ."\n";
		  if (is_array($order->products)) {
			 // echo 'ff'.print_r($order->products);
			 foreach ($order->products as $pr => $ar) {
			    if (is_array($ar)) { $my_order .= $ar['quantity']."x - ".$ar['name']." => ".$ar['model'].": ".$ar['final_price']." ".$order->info['currency']."\n"; }
			 }
			 if(isset($order->info['shipping_method']) && !empty($order->info['shipping_method'])){
		     	$my_order .= "+".$order->info['shipping_method'].": ".$order->info['shipping_cost']." ".$order->info['currency']."\n";
			 }
		  }
		  $kwota = number_format($order->info['total']*$order->info['currency_value'], 2);
		  $control = $kwota;

		  $my_lang = tep_db_fetch_array(tep_db_query("select code from " . TABLE_LANGUAGES . " where languages_id = '" . Session::get('languages_id') . "'"));
		  if ($my_lang['code'] == "pl"){
			$mytitle = "Zamowienie";
		  } else {
			$mytitle = "Order";
		  }
      //echo "saas". print_r($order);
      //echo '<br>'. $billingAddress['country']['title'];
      //echo
     // echo '<br/>'. print_r($onePageCheckout);

      //itwExit();
      $process_button_string = tep_draw_hidden_field('session_id', 'I'.'-'.$userAccount->getCustomerId() . '-'.$order->newOrder['orderID'] .'-'.($onePageCheckout->isMembershipCheckout()?'M':'') ) .
                               tep_draw_hidden_field('lang', strtolower($my_lang['code'])) .
                               tep_draw_hidden_field('pay', 'yes') .
                               tep_draw_hidden_field('waluta', $order->info['currency']) .
                               tep_draw_hidden_field('osC', '1') .
                               tep_draw_hidden_field('id', $this->getConfigData('MODULE_PAYMENT_DOTPAY_ID')) .
                               tep_draw_hidden_field('kanal', '0') .
                               tep_draw_hidden_field('kwota', $kwota) .
                               tep_draw_hidden_field('opis', sysConfig::get('STORE_NAME') . ' - ' . $mytitle . '-' . date('Ymdhis') ) .
                               tep_draw_hidden_field('forename',$billingAddress['entry_firstname']) .
                               tep_draw_hidden_field('surname', $billingAddress['entry_lastname']) .
                               tep_draw_hidden_field('oscdesc', $my_order) .
                               tep_draw_hidden_field('oscname', $deliveryAddress['entry_firstname'] . ' ' .$deliveryAddress['entry_lastname']) .
                               tep_draw_hidden_field('deladdr', $AddressBook->formatAddress('delivery')) .
                               tep_draw_hidden_field('street', $billingAddress['entry_street_address']) .
                               tep_draw_hidden_field('street_n1', $billingAddress['entry_suburb']) .
                               tep_draw_hidden_field('city', $billingAddress['entry_city']) .
                               tep_draw_hidden_field('bill_state', $billingAddress['entry_state']) .
                               tep_draw_hidden_field('postcode', $billingAddress['entry_postcode']) .
                               tep_draw_hidden_field('country', tep_get_country_name($billingAddress['entry_country_id'])) .
                               tep_draw_hidden_field('comments', $order->info['comments']) .
		  					   tep_draw_hidden_field('p_info', sysConfig::get('STORE_NAME')) .
                               tep_draw_hidden_field('phone', $onePageCheckout->onePage['info']['telephone']) .
                               tep_draw_hidden_field('email', $onePageCheckout->onePage['info']['email_address']) .
                               tep_draw_hidden_field('control', $control) .
                               tep_draw_hidden_field('vmodule', '6.0.3') .
			       				tep_draw_hidden_field('URLC', tep_href_link('ext/modules/payment/dotpay/dotpay.php','','SSL')) .
                               tep_draw_hidden_field('return_url', itw_app_link('action=sessionClean&order_id='.$order->newOrder['orderID'], 'account', 'default', 'SSL')) .
							   tep_draw_hidden_field('cancel_return_url', itw_app_link(null,'checkout','default','SSL'));

		return $process_button_string;
	}

	function onInstall(){
		/*$check_query = tep_db_query("select orders_status_id from " . TABLE_ORDERS_STATUS . " where orders_status_name = 'Preparing [Dotpay]' limit 1");
		if (tep_db_num_rows($check_query) < 1){
			$status_query = tep_db_query("select max(orders_status_id) as status_id from " . TABLE_ORDERS_STATUS);
			$status = tep_db_fetch_array($status_query);
			$status_id = $status['status_id'] + 1;

			$languages = tep_get_languages();
			foreach ($languages as $lang){
				tep_db_query("insert into " . TABLE_ORDERS_STATUS . " (orders_status_id, language_id, orders_status_name) values ('" . $status_id . "', '" . $lang['id'] . "', 'Preparing [Dotpay]')");
			}
		}else{
			$check = tep_db_fetch_array($check_query);
			$status_id = $check['orders_status_id'];
		}
		
		$Qupdate = Doctrine_Query::create()
		->update('ModulesConfiguration')
		->set('configuration_value', '?', $status_id)
		->where('configuration_key = ?', 'MODULE_PAYMENT_DOTPAY_PREPARE_ORDER_STATUS_ID')
		->execute();*/
	}
}
?>