<?php
class OrderPaymentHeartland extends StandardPaymentModule {

	public function __construct(){
		/*
		 * Default title and description for modules that are not yet installed
		 */
		$this->setTitle('Credit/Debit Card (via Heartland)');
		$this->setDescription('Credit/Debit Card (via Heartland)');
		
		$this->init('heartland');
		
		if ($this->isEnabled() === true){
			$this->identifier = 'heartland';
			$this->username = $this->getConfigData('MODULE_PAYMENT_HEARTLAND_USERNAME');
			$this->setFormUrl('https://hps.webportal.test.secureexchange.net:443/PaymentMain.aspx');
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
		  $ototal = number_format($order->info['total']*$order->info['currency_value'], 2);

 	      //Session::set('TxnsVFNumb', 'I'.'-'.$userAccount->getCustomerId() . '-'.$order->newOrder['orderID'] .'-'.($onePageCheckout->isMembershipCheckout()?'M':''));

          $process_button_string = tep_draw_hidden_field('ClientSessionID', 'I'.'-'.$userAccount->getCustomerId() . '-'.$order->newOrder['orderID'] .'-'.($onePageCheckout->isMembershipCheckout()?'M':'') ) .
                               tep_draw_hidden_field('ProcessType', 'CreditCard') .
                               tep_draw_hidden_field('TransType', 'Sale') .
	                            tep_draw_hidden_field('TransNum', $order->newOrder['orderID']) .
                               tep_draw_hidden_field('UserName', $this->username) .
	                           tep_draw_hidden_field('Amount', $order->info['total']) .
	                            tep_draw_hidden_field('HasHeader', 'true') .
                               tep_draw_hidden_field('CustomerFirstName',$billingAddress['entry_firstname']) .
                               tep_draw_hidden_field('CustomerLastName', $billingAddress['entry_lastname']) .
                               tep_draw_hidden_field('CustomerStreet2', $billingAddress['entry_street_address']) .
                               tep_draw_hidden_field('CustomerDayPhone', (isset($onePageCheckout->onePage['info']['telephone'])?$onePageCheckout->onePage['info']['telephone']:'')) .
                               tep_draw_hidden_field('CustomerEmail', $onePageCheckout->onePage['info']['email_address']) .
			       			   tep_draw_hidden_field('ReturnURL', tep_href_link('ext/modules/payment/heartland/heartland.php'));

		return $process_button_string;
	}

	function onInstall(){

	}
}
?>