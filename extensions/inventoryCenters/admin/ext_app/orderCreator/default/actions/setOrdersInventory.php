<?php
$Editor->setData('inventory_center_id', $_GET['id']);

EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
