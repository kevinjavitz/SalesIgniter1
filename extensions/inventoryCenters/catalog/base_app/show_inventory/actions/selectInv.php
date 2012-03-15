<?php

Session::set('isppr_inventory_pickup', $_GET['inv_id']);

if(Session::exists('redirectLinkBefore')){
	$redirectLink = Session::get('redirectLinkBefore');
}else{
	$redirectLink = itw_app_link(null, 'products', 'all');
}

Session::remove('redirectLinkBefore');
Session::remove('redirectCategoryBefore');

EventManager::attachActionResponse($redirectLink, 'redirect');
?>