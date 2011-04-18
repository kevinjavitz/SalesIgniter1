<?php
	$pointOfSale->addOrdercomments($_GET['comment']);
	
	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>