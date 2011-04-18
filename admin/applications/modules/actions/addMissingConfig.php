<?php
	require(DIR_FS_CATALOG . 'includes/classes/' . $set . '.php');
	switch ($set){
		case 'payment':
			payment::addMissingConfig($_GET['module']);
			break;
		case 'shipping':
			shipping::addMissingConfig($_GET['module']);
			break;
		case 'order_total':
			order_total::addMissingConfig($_GET['module']);
			break;
	}
	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action'))), 'redirect');
?>