<?php
	$App->addMissingModelTable($_GET['Model']);

$ModelCheck = checkModel($_GET['Model']);
EventManager::attachActionResponse(array(
	'success' => $ModelCheck['isOk'],
	'resUrl' => $ModelCheck['resolution']
), 'json');
?>