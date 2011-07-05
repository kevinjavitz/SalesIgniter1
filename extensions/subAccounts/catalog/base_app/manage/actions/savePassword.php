<?php
	$error = false;
	$cID = $_GET['cID'];
	if(isset($_POST['password']) && !empty($_POST['password'])){
		$password = $_POST['password'];
	}else{
		$password = '';
	}



	$newUser = Doctrine::getTable('Customers')->find($cID);

	if($password != ''){
		$newUser->customers_password = $userAccount->encryptPassword($password);
	}

	$newUser->save();
	EventManager::attachActionResponse(itw_app_link(null, 'account', 'default', 'SSL'), 'redirect');

?>