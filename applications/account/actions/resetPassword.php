<?php
	$link = itw_app_link(null, 'account', 'password_forgottem', 'SSL');
	if ($userAccount->processPasswordForgotten($_POST['email_address']) === true){
		$link = itw_app_link(null, 'account', 'login', 'SSL');
	}
	EventManager::attachActionResponse($link, 'redirect');
?>