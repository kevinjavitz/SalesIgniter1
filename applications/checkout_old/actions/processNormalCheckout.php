<?php
	//echo '<pre>';print_r($order);exit;
	$order->createOrder();

	$paymentModules->processPayment();

	$order->insertOrderTotals();
	$order->insertStatusHistory();

	// initialized for the email confirmation
	$products_ordered = '';

	foreach($ShoppingCart->getProducts() as $cartProduct) {
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

	$order->sendNewOrderEmail();

	// load the after_process function from the payment modules
	$paymentModules->afterOrderProcess();

	$ShoppingCart->emptyCart(true);

	// #################### Added CCGV ######################
	if (Session::exists('credit_covers') === true){
		Session::remove('credit_covers');
	}
	//$orderTotalModules->clear_posts();//ICW ADDED FOR CREDIT CLASS SYSTEM
	// #################### End Added CCGV ######################

	EventManager::attachActionResponse(itw_app_link(null, 'checkout', 'success', 'SSL'), 'redirect');
?>