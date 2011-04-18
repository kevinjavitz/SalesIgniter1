<?php
	$error = false;
	if ($userAccount->processLogIn($_POST['email'], $_POST['pass']) === false){
		$error = true;
	}

	if ($error === false){
		$onePageCheckout->setupLoggedInCustomer();
		$json = array(
			'success' => true,
			'msg' => 'Loading your account info'
		);
	}else{
		$json = array(
			'success' => false,
			'msg' => 'Authorization Failed'
		);
	}
	EventManager::attachActionResponse($json, 'json');
?>