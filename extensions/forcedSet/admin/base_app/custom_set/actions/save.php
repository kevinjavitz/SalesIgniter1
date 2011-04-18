<?php

	$customField1 = $_POST['forced_set_custom_field_one'];
	$customField2 = $_POST['forced_set_custom_field_two'];

	$forcedRelation = Doctrine_Core::getTable('ForcedSetRelations');
	if (isset($_GET['fID'])){
		$forcedRelation = $forcedRelation->find((int)$_GET['fID']);
	}else{
		$forcedRelation = new ForcedSetRelations();
	}
	

	$forcedRelation->forced_set_custom_field_one = $customField1;
	$forcedRelation->forced_set_custom_field_two = $customField2;
	$forcedRelation->save();

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'fID')) . 'fID=' . $forcedRelation->forced_set_id, null, 'default'), 'redirect');
?>