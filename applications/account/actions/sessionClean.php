<?php
	Session::remove('sendto');
	Session::remove('billto');
	Session::remove('shipping');
	Session::remove('payment');
	Session::remove('comments');

	if (Session::exists('credit_covers') === true){
		Session::remove('credit_covers');
	}

	Session::remove('cc_id');
	Session::remove('payment_recurring');
	Session::remove('cancel_request');
	Session::remove('onepage');
 	require('includes/classes/onepage_checkout.php');
	$onePageCheckout = new osC_onePageCheckout();
	$onePageCheckout->setMode('');
 	$ShoppingCart->emptyCart(true);
	$membership =& $userAccount->plugins['membership'];
	$membership->loadMembershipInfo();
	$membership->loadPlanInfo();
	if(Session::exists('add_to_queue_product_id')){
		$pID = Session::get('add_to_queue_product_id');
		$attribs = Session::get('add_to_queue_product_attrib');
		Session::remove('add_to_queue_product_id');
		Session::remove('add_to_queue_product_attrib');
		$rentalQueue->addToQueue($pID, $attribs);
		$messageStack->addSession('pageStack',sysLanguage::get('TEXT_ACTIVATED_MINUTES'));
		tep_redirect( itw_app_link(null,'rentals','queue'));
	}else{

  	    tep_redirect(itw_app_link('order_id='.$_GET['order_id'], 'account', 'history_info', 'SSL'));
	}

?>