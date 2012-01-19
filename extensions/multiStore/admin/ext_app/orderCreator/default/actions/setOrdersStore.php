<?php
$Editor->setData('store_id', $_GET['id']);

EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
