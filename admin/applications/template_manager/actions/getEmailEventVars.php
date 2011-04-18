<?php
	$globalVars = array();
	$standardVars = array();
	$conditionVars = array();
		
	EventManager::attachActionResponse(array(
		'global'      => array(
			'$store_name',
			'$store_owner',
			'$store_owner_email',
			'$today_short',
			'$today_long',
			'$store_url'
		),
		'standard'    => $standardVars,
		'conditional' => $conditionVars
	), 'json');
?>