<?php
	$Editor->ProductManager->remove((int) $_GET['id']);

	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>
