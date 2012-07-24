<?php
EventManager::notify('OrderCreatorLoadExtraFields');
EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>