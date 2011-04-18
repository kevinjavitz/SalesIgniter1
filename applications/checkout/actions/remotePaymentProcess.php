<?php
	// load the after_process function from the payment modules
	$PaymentModule->processPayment();
	$PaymentModule->afterOrderProcess();

	$ShoppingCart->emptyCart(true);
	Session::remove('sendto');
	Session::remove('billto');
	Session::remove('shipping');
	Session::remove('payment');
	Session::remove('comments');

	// #################### Added CCGV ######################
	if (Session::exists('credit_covers') === true){
		Session::remove('credit_covers');
	}
	//$orderTotalModules->clear_posts();//ICW ADDED FOR CREDIT CLASS SYSTEM
	// #################### End Added CCGV ######################

	EventManager::attachActionResponse(itw_app_link(null, 'checkout', 'success', 'SSL'), 'redirect');
?>