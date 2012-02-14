<?php
	EventManager::notify('UpdateTotalsCheckout');
    if (Session::exists('redirectUrl')){
		$redirect = Session::get('redirectUrl');
		Session::remove('redirectUrl');
	}else{
		$redirect = '';
	}
	OrderTotalModules::process();
	EventManager::attachActionResponse(array(
		'success' => true,
		'redirectUrl' => $redirect,
		'orderTotalRows' => OrderTotalModules::output()
	), 'json');

?>