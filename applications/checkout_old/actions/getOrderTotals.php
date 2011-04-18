<?php
	$totals = '[]';
	if ($orderTotalModules->modulesAreInstalled()){
		$totals = $orderTotalModules->output('json');
	}
	
	EventManager::attachActionResponse(array(
		'totals' => $totals
	), 'json');
?>