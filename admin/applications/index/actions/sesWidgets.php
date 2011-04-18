<?php

	if (isset($_POST['config'])) {
		$Admin = Doctrine_Core::getTable('Admin')->findOneByAdminId((int)Session::get('login_id'));
		$Admin->config_home = $_POST['config'];
		$Admin->save();
	    $response = 'OK';

	} else {
		$Admin = Doctrine_Core::getTable('Admin')->findOneByAdminId((int)Session::get('login_id'));
		$response = $Admin->config_home;
	}
	$json = array(
			'success' => true,
			'config'  => $response
	);

	EventManager::attachActionResponse($json, 'json');

?>