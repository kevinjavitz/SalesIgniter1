<?php
	$Groups = Doctrine_Core::getTable('FeaturedManagerGroups')->findOneByFeaturedGroupId((int)$_POST['featured_group_id']);
	if ($Groups){
		$Groups->delete();
	}


	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('gID', 'action'))), 'redirect');
?>