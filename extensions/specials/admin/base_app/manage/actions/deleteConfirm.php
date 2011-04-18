<?php
	$Record = Doctrine::getTable('Specials')->findOneBySpecialsId((int)$_GET['sID']);
	if ($Record){
		$Record->delete();
	}

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'sID')), null, 'default'), 'redirect');
?>