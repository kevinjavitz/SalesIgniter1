<?php
	$Group = Doctrine_Core::getTable('CustomerCustomFieldsGroups')->findOneByGroupId((int)$_GET['group_id']);
	if ($Group){
		$Group->delete();
		$success = true;
	}else{
		$success = false;
	}
	EventManager::attachActionResponse(array(
		'success' => $success
	), 'json');
?>