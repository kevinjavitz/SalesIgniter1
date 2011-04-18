<?php
	$QglobalProgress = mysql_query('select message, percentage from progress_bar where name = "upgradeCheckGlobal"');
	$globalProgress = mysql_fetch_assoc($QglobalProgress);
	
	$QprocessProgress = mysql_query('select message, percentage from progress_bar where name = "upgradeCheckProcess"');
	$processProgress = mysql_fetch_assoc($QprocessProgress);
	
	EventManager::attachActionResponse(array(
		'globalMessage' => $globalProgress['message'],
		'processMessage' => $processProgress['message'],
		'globalPercent' => $globalProgress['percentage'],
		'processPercent' => $processProgress['percentage']
	), 'json');
?>