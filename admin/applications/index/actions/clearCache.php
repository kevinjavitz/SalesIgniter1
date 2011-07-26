<?php
	apc_clear_cache('opcode');
	$json = array(
			'success' => true
	);

	EventManager::attachActionResponse($json, 'json');
?>