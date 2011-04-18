<?php
	$fieldId = (int)$_GET['field_id'];
	$groupId = (int)$_GET['group_id'];
	
	$Qcheck = Doctrine_Query::create()
	->select('group_id')
	->from('ProductsCustomFieldsToGroups')
	->where('field_id = ?', $fieldId)
	->andWhere('group_id = ?', $groupId)
	->execute();
	if ($Qcheck){
		$QnextSort = Doctrine_Query::create()
		->select('max(sort_order)+1 as sort_order')
		->from('ProductsCustomFieldsToGroups')
		->where('group_id = ?', $groupId)
		->fetchOne();

		$newFieldToGroup = new ProductsCustomFieldsToGroups();
		$newFieldToGroup->group_id = $groupId;
		$newFieldToGroup->field_id = $fieldId;
		$newFieldToGroup->sort_order = $QnextSort['sort_order'];
		$newFieldToGroup->save();

		$success = true;
	}else{
		$success = false;
	}
	EventManager::attachActionResponse(array(
		'success' => $success
	), 'json');
?>