<?php
	$Record = Doctrine::getTable('Specials')->find((int)$_GET['sID']);
	if ($Record){
		$Record->status = (int)$_GET['flag'];
		$Record->save();
	}

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'flag'))), 'redirect');
?>