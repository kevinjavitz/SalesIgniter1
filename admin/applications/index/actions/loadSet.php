<?php
	$AdminFavorites = Doctrine_Core::getTable('AdminFavorites')->find((int) $_GET['mID']);
	$Admin = Doctrine_Core::getTable('Admin')->findOneByAdminId((int)Session::get('login_id'));
	$Admin->favorites_links = $AdminFavorites->favorites_links;
	$Admin->favorites_names = $AdminFavorites->favorites_names;
	$Admin->save();
	$json = array(
		'success' => true
	);

	EventManager::attachActionResponse($json, 'json');
?>