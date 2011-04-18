<?php
	$App->addMissingModelTable($_GET['Model'], (isset($_GET['extName']) ? $_GET['extName'] : null));
	
	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('Model', 'extName', 'action'))), 'redirect');
?>