<?php
	$centerID = $pointOfSale->checkServiceAvailability();
	if ($centerID !== false){
		$inService = 'true';
	}else{
		$inService = 'false';
	}
	EventManager::attachActionResponse(array(
		'success'   => true,
		'inService' => $inService
	), 'json');
?>