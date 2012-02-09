<?php
if (isset($_GET['mID'])){
	$AdminFavorites = Doctrine_Core::getTable('AdminFavorites')->find((int) $_GET['mID']);
	$AdminFavorites->delete();
}

$json = array(
	'success' => true
);

EventManager::attachActionResponse($json, 'json');
?>