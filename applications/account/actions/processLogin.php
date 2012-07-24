<?php
	$emailAddress = (isset($_POST['email_address']) ? $_POST['email_address'] : '');
	$passWord = (isset($_POST['password']) ? $_POST['password'] : '');
	if ($userAccount->processLogIn($emailAddress, $passWord) === true){




		if(Session::exists('redirectToUrl')){
			$redirectUrl = Session::get('redirectToUrl');
			Session::remove('redirectToUrl');
		}else{
			$redirectUrl = itw_app_link(null, 'account', 'default', 'SSL');
		}
	}
	
	if (!isset($redirectUrl)){
		$redirectUrl = itw_app_link(null, 'account', 'login', 'SSL');
	}
	EventManager::attachActionResponse($redirectUrl, 'redirect');
?>