<?php
	$AdminGroups = Doctrine_Core::getTable('AdminGroups')->findByAdminGroupsId((int)$_GET['gID']);
	if ($AdminGroups){
		$AdminGroups->delete();
	}

	EventManager::attachActionResponse(itw_app_link(null, null, 'groups'), 'redirect');
?>