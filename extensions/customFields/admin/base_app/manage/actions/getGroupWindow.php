<?php
	$groupInput = htmlBase::newElement('input')->setName('group_name');
	if (isset($_GET['group_id'])){
		$groupId = $_GET['group_id'];

		$Qgroup = Doctrine_Query::create()
		->select('group_name')
		->from('ProductsCustomFieldsGroups')
		->where('group_id = ?', $groupId)
		->fetchOne();
		
		$groupInput->setValue($Qgroup['group_name']);
	}else{
		$groupId = null;
	}
			
	EventManager::attachActionResponse(array(
		'success'  => true,
		'html'     => sysLanguage::get('ENTRY_GROUP_NAME') . $groupInput->draw(),
		'group_id' => $groupId
	), 'json');
?>