<?php

	chdir('../../../../');
	require('includes/application_top.php');

	function storeAddress(){
		// Validation
		if(!$_GET['email']){ return "No email address provided"; }

		if(!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*$/i", $_GET['email'])) {
			return "Email address is invalid";
		}

		require_once('MCAPI.class.php');
		// grab an API Key from http://admin.mailchimp.com/account/api/
		$api = new MCAPI((isset($_GET['api'])?$_GET['api']:''));

		// grab your List's Unique Id by going to http://admin.mailchimp.com/lists/
		// Click the "settings" link for the list - the Unique Id is at the bottom of that page.
		$list_id = (isset($_GET['list'])?$_GET['list']:'');

		if($api->listSubscribe($list_id, $_GET['email'], '') === true) {
			// It worked!
			return 'Success. Check your inbox for confirmation';//sysLanguage::get('INFOBOX_MAILCHIMP_SUCCESS')
		}else{
			// An error ocurred, return error message
			return  'Error:'. $api->errorMessage;//sysLanguage::get('INFOBOX_MAILCHIMP_FAIL')
		}
	}

	if($_GET['ajax']){
		echo storeAddress();
	}
	require('includes/application_bottom.php');
?>
