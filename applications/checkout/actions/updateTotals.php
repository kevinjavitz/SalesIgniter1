<?php
	EventManager::notify('UpdateTotalsCheckout');
	OrderTotalModules::process();
    if (Session::exists('redirectUrl')){
		$redirect = Session::get('redirectUrl');
		Session::remove('redirectUrl');
	}else{
		$redirect = '';
	}
	EventManager::attachActionResponse(array(
		'success' => true,
		'redirectUrl' => $redirect,
		'orderTotalRows' => OrderTotalModules::output()
	), 'json');

?>