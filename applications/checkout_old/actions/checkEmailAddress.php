<?php
	$success = true;
	$errMsg = '';
	$emailAddress = $_POST['emailAddress'];

	if ($userAccount->emailExists($emailAddress)){
		$success = false;
		$errMsg = 'Your email address already exists, please log into your account or use a different email address.';
	}elseif ($userAccount->validateEmailAddress($emailAddress) === false){
		$success = false;
		$errMsg = 'The email address provided is invalid.';
	}

	if ($success === true){
		$onePageCheckout->onePage['info']['email_address'] = $emailAddress;
	}
	
	EventManager::attachActionResponse(array(
		'success' => $success,
		'errMsg' => $errMsg
	), 'json');
?>