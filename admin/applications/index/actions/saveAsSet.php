<?php
if (isset($_GET['aID'])){
	$AdminFavorites = Doctrine_Core::getTable('AdminFavorites')->find((int) $_GET['aID']);
}
$AdminFavorites->favorites_links = implode(';',$_POST['fav_links']);
$AdminFavorites->favorites_names = implode(';',$_POST['fav_names']);
$AdminFavorites->save();
$json = array(
	'success' => true
);

EventManager::attachActionResponse($json, 'json');
?>