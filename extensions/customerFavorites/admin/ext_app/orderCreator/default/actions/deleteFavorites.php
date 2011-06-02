<?php
    if(isset($_POST['customerFavoritesSelect'])){
		$QCustomerFavoritesDelete = Doctrine_Query::create()
		->delete('CustomerFavorites')
		->whereIn('customer_favorites_id', $_POST['customerFavoritesSelect'])
		->execute();

		$QCustomerFavoritesDeletePA = Doctrine_Query::create()
		->delete('CustomersFavoritesProductAttributes')
		->whereIn('customer_favorites_id', $_POST['customerFavoritesSelect'])
		->execute();

		$messageStack->addSession('pageStack', sysLanguage::get('EXTENSION_CUSTOMER_FAVORITES_SUCCES_REMOVE'), 'error');
	}else{
	    $messageStack->addSession('pageStack', sysLanguage::get('EXTENSION_CUSTOMER_FAVORITES_ERROR_REMOVE'), 'error');
	}

	EventManager::attachActionResponse(array(
		'success' => true,
		'redirect' => itw_app_link('appExt=customerFavorites','account_addon','manage_favorites')
	), 'json');
?>