<?php
	$Qprogress = tep_db_query('select message from progress_bar where name = "newLanguage"');
	$progress = tep_db_fetch_array($Qprogress);
	
	EventManager::attachActionResponse($progress['message'], 'html');
?>