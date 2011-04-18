<?php
	$Groups = Doctrine_Core::getTable('FeaturedManagerGroups');
	if (isset($_GET['gID'])){
		$Group = $Groups->findOneByFeaturedGroupId((int)$_GET['gID']);
	}else{
		$Group = $Groups->create();
	}

	$Group->featured_group_name = $_POST['featured_group_name'];
	$Group->featured_group_number_of_products = $_POST['featured_group_number_of_products'];
	$Group->save();


	EventManager::attachActionResponse(
		itw_app_link(tep_get_all_get_params(array('action', 'gID')) . 'gID=' . $Group->featured_group_id, null, 'default'),
		'redirect'
	);
?>