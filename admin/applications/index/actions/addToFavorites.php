<?php
if (isset($_POST['settings']) && isset($_POST['settings']['name']) && !empty($_POST['settings']['name'])){
	$Admin = Doctrine_Core::getTable('Admin')
		->find((Session::get('login_id') == 'master' ? 0 : (int)Session::get('login_id')));

	$Settings = $_POST['settings'];
	$url = '';
	if (isset($Settings['appExt']) && !empty($Settings['appExt'])){
		$url .= $Settings['appExt'] . '/';
	}
	$url .= $Settings['app'] . '/';
	$url .= $Settings['appPage'] . '.php';
	if (isset($Settings['get']) && !empty($Settings['get'])){
		$url .= '?' . $Settings['get'];
	}

	$Admin->favorites_links .= ';' . $url;
	$Admin->favorites_names .= ';' . $Settings['name'];
	$Admin->save();
}

$json = array(
	'success' => true
);

EventManager::attachActionResponse($json, 'json');
?>