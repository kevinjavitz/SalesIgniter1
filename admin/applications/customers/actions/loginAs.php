<?php
	if (Session::exists('customer_login_allowed') === true && Session::get('customer_login_allowed') === true){

		$sessionVar = Session::getSessionName() . '=' . Session::getSessionId();
		$adminVar = '&adminCustomerId=' . $_GET['cID'];
		EventManager::attachActionResponse(itw_catalog_app_link('action=processLogin&' . $sessionVar . $adminVar,
		                                  'account',
		                                  'login',
		                                  'SSL'
										), 'redirect');
	}
?>