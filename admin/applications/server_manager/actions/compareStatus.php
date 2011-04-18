<?php
	$Qprogress = tep_db_query('select message, percentage from progress_bar where name = "fileCompare"');
	$progress = tep_db_fetch_array($Qprogress);
	
	EventManager::attachActionResponse(array(
		'message' => $progress['message'],
		'percent' => $progress['percentage']
	), 'json');
?>