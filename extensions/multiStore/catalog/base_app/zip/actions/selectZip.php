<?php
$zip = $_POST['zipClient'];
Session::set('zipClient', ltrim(rtrim(urldecode($zip))));
EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>