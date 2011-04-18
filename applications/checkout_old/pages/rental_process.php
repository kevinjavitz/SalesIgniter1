<?php
/* One Page Checkout - BEGIN */
require('includes/classes/onepage_checkout.php');
$onePageCheckout = new osC_onePageCheckout();
$onePageCheckout->setMode('membership');
/* One Page Checkout - END */

// load selected payment module
require(DIR_WS_CLASSES . 'payment.php');
// #################### Added CCGV ######################
if (Session::exists('credit_covers') === true && Session::get('credit_covers') === true) $onePageCheckout->onePage['info']['payment']['id'] = '';
// #################### End Added CGV ######################
$paymentModules = new payment($onePageCheckout->onePage['info']['payment']['id']);

require(DIR_WS_CLASSES . 'order.php');
$order = new order;
// load the before_process function from the payment modules

$planID = $onePageCheckout->onePage['rentalPlan']['id'];
/* One Page Checkout - BEGIN */
$onePageCheckout->loadOrdersVars();
$onePageCheckout->fixTaxes();
/* One Page Checkout - END */

require(DIR_WS_CLASSES . 'order_total.php');
$orderTotalModules = new order_total;

$order->newOrder['orderTotals'] = $orderTotalModules->process();
$order->info['is_rental'] = '1';
$order->info['bill_attempts'] = '1';

/*  echo '<pre>POST::';print_r($_POST);echo '</pre>';
echo '<pre>ONEPAGECHECKOUT::';print_r($onePageCheckout);echo '</pre>';
echo '<pre>ORDER::';print_r($order);echo '</pre>';
echo '<pre>SESSION::';print_r($_SESSION);echo '</pre>';
exit;*/

$order->createOrder();
/*
if (Session::exists('cart_PayPal_IPN_ID') === false){
	$paymentModules->before_process();
}
*/

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

		$paymentModule = $paymentModules->getModule($paymentInfo['id']);
		if ($billPrice > 0){
			$gatewayVars = array(
				'orderID'        => $order->newOrder['orderID'],
				'amount'         => $billPrice,
				'cardOwner'      => $paymentInfo['cardDetails']['cardOwner'],
				'cardOwnerEmail' => $onePageCheckout->onePage['info']['email_address'],
				'cardNumber'     => $paymentInfo['cardDetails']['cardNumber'],
				'cardExpMonth'   => $paymentInfo['cardDetails']['cardExpMonth'],
				'cardExpYear'    => $paymentInfo['cardDetails']['cardExpYear']
			);

			if (isset($paymentInfo['cardDetails']['cardCvvNumber'])){
				$gatewayVars['cardCvvNumber'] = $paymentInfo['cardDetails']['cardCvvNumber'];
			}

			$paymentModule->paymentGatewayProcess($gatewayVars);
		}
		
		$onePageCheckout->createCustomerAccount();

		$membership =& $userAccount->plugins['membership'];
		$membership->setPlanId($planID);
		$membership->setMembershipStatus('M');
		$membership->setActivationStatus('Y');
		$membership->setFreeTrailEnd($freeTrialEnds);
		$membership->setNextBillDate($nextBillDate);
		$membership->setPaymentTerm($paymentTerm);
		$membership->setPaymentMethod($paymentModule->code);
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

if (Session::exists('cart_PayPal_IPN_ID') === false){
	$order->insertOrderTotals();
	$order->insertStatusHistory();

	// initialized for the email confirmation

	$products_ordered = '';
	$subtotal = 0;
	$total_tax = 0;
	for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
		$order->insertMembershipProduct($order->products[$i]);
	}

	EventManager::notify('CheckoutProcessPostProcess', &$order);
}

// load the after_process function from the payment modules
$paymentModules->after_process();

$userAccount->processLogIn(
	$onePageCheckout->onePage['info']['email_address'],
	$onePageCheckout->onePage['info']['password']
);

// unregister session variables used during checkout
Session::remove('sendto');
Session::remove('billto');
Session::remove('shipping');
Session::remove('payment');
Session::remove('comments');

// #################### Added CCGV ######################
if(Session::exists('credit_covers') === true) Session::remove('credit_covers');
$orderTotalModules->clear_posts();//ICW ADDED FOR CREDIT CLASS SYSTEM
// #################### End Added CCGV ######################

//tep_session_unregister('planid');
Session::remove('payment_recurring');
Session::remove('cancel_request');
//  tep_session_unregister('arr_payment_info');
//  tep_session_unregister('payment_rental');

Session::remove('onepage');

tep_redirect(itw_app_link(null, 'checkout', 'success'));
?>