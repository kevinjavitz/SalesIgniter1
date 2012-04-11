<?php
    if(isset($_POST['customerWishlistSelect'])){
		$QCustomerWishlistDelete = Doctrine_Query::create()
		->delete('CustomerWishlist')
		->whereIn('customer_wishlist_id', $_POST['customerWishlistSelect'])
		->execute();

		$QCustomerWishlistDeletePA = Doctrine_Query::create()
		->delete('CustomersWishlistProductAttributes')
		->whereIn('customer_wishlist_id', $_POST['customerWishlistSelect'])
		->execute();

		$messageStack->addSession('pageStack', sysLanguage::get('EXTENSION_CUSTOMER_WISHLIST_SUCCES_REMOVE'), 'error');
	}else{
	    $messageStack->addSession('pageStack', sysLanguage::get('EXTENSION_CUSTOMER_WISHLIST_ERROR_REMOVE'), 'error');
	}

	EventManager::attachActionResponse(array(
		'success' => true,
		'redirect' => itw_app_link('appExt=customerWishlist','account_addon','manage_wishlist')
	), 'json');
?>