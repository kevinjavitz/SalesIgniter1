<?php
	$windowAction = $_GET['windowAction'];
	if ($windowAction == 'edit'){
		$Qgroup = Doctrine_Query::create()
		->select('products_options_groups_id, products_options_groups_name')
		->from('ProductsOptionsGroups')
		->where('products_options_groups_id = ?', $_GET['group_id'])
		->fetchOne()->toArray();
	}

	$groupNameInput = htmlBase::newElement('input')->setName('group_name');
	if (isset($Qgroup)){
		$groupNameInput->setValue($Qgroup['products_options_groups_name']);
	}

	$finalTable = htmlBase::newElement('table')->setCellPadding('3')->setCellSpacing('0');
	
	if (isset($_GET['group_id'])){
		$finalTable->attr('group_id', (int)$_GET['group_id']);
	}
			
	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('ENTRY_OPTION_GROUP_NAME') . '</b>')
	)));

	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => $groupNameInput)
	)));

	EventManager::attachActionResponse($finalTable->draw(), 'html');
?>