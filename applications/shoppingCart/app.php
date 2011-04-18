<?php
	$appContent = $App->getAppContentFile();
	$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.datepicker.js');

	if (Session::exists('payment_rental') === true){
		$payment_rental = false;
		Session::remove('payment_rental');
	}
	
	$breadcrumb->add(sysLanguage::get('NAVBAR_TITLE'), itw_app_link(null, 'shoppingCart', 'default'));
?>