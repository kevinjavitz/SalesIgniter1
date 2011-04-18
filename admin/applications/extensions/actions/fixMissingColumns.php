<?php
	if (isset($_GET['extName'])){
		$extension = $appExtension->getExtension($_GET['extName']);
		$extension->fixMissingColumns();
	}elseif (isset($_GET['Model'])){
		$App->addMissingModelColumns($_GET['Model']);
	}
	
	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action'))), 'redirect');
?>