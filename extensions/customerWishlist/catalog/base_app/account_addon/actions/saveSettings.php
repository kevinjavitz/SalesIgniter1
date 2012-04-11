<?php
//get this users settings
$currentSettings = Doctrine_Query::create()
	->from('CustomerWishlistSettings')
	->where('customers_id=?', $userAccount->getCustomerId())
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

$public = isset( $_POST['wishlistAllowPublic'] ) && $_POST['wishlistAllowPublic'] == 'true' ? 1 : 0;
$search = isset( $_POST['wishlistAllowSearch'] ) && $_POST['wishlistAllowSearch'] == 'true' ? 1 : 0;

if( !isset($currentSettings[0]) ) {
	//new wishlist
	$s = new CustomerWishlistSettings();
	$s->customers_id = $userAccount->getCustomerId();
	$s->wishlist_public = $public;
	$s->wishlist_search = $search;
	$s->save();
} else {
	//update wishlist
	$update = Doctrine_Query::create()
	->update('CustomerWishlistSettings')
	->set('wishlist_public', '?', $public)
	->set('wishlist_search', '?', $search)
	->where('customers_id = ?', $userAccount->getCustomerId())
	->execute();
}

EventManager::attachActionResponse(array(
	'success' => true,
	'redirect' => itw_app_link('','customerWishlist/account_addon','manage_wishlist')
), 'json');
?>