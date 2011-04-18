<?php
	$Groups = Doctrine_Core::getTable('BannerManagerGroups')->findOneByBannerGroupId((int)$_POST['banner_group_id']);
	if ($Groups){
		$Groups->delete();
	}


	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('gID', 'action'))), 'redirect');
?>