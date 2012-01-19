<?php

	if(isset($_GET['url']) && !empty($_GET['url'])){
		$Admin = Doctrine_Core::getTable('AdminFavorites')->find($_POST['aID']);
		$favorites_links = explode(';', $Admin->favorites_links);
		$favorites_names = explode(';', $Admin->favorites_names);
		$removeVal = array_search($_GET['url'], $favorites_links);
		unset($favorites_links[$removeVal]);
		unset($favorites_names[$removeVal]);
		$Admin->favorites_links = implode(';', $favorites_links);
		$Admin->favorites_names = implode(';', $favorites_names);
		$Admin->save();
	}
$json = array(
	'success' => true
);

EventManager::attachActionResponse($json, 'json');
?>