<?php
	$ProductsCustomFieldsGroups = Doctrine_Core::getTable('ProductsCustomFieldsGroups');
	if (isset($_GET['gID'])){
		$Group = $ProductsCustomFieldsGroups->findOneByGroupId((int)$_GET['gID']);
	}else{
		$Group = $ProductsCustomFieldsGroups->create();
	}
	
	$Group->group_name = $_POST['group_name'];
	$Group->save();

	if (isset($_GET['gID'])){
		EventManager::attachActionResponse(array(
			'success'    => true,
			'group_name' => $Group->group_name
		), 'json');
	}else{
		$trashBin = htmlBase::newElement('div')->addClass('trashBin')
		->html('Drop Here To Trash<div class="ui-icon ui-icon-trash" style="float:left;"></div>')
		->attr('group_id', $Group->group_id);

		$sortableList = htmlBase::newElement('sortable_list');

		$iconCss = array(
 			'float'    => 'right',
 			'position' => 'relative',
 			'top'      => '-4px',
 			'right'    => '-4px'
		);
		
 		$deleteIcon = htmlBase::newElement('icon')->setType('circleClose')->setTooltip('Click to delete group')
 		->setHref(itw_app_link('appExt=customFields&action=removeGroup&group_id=' . $Group->group_id))
 		->css($iconCss);

 		$editIcon = htmlBase::newElement('icon')->setType('wrench')->setTooltip('Click to edit group')
 		->setHref(itw_app_link('appExt=customFields&action=getGroupWindow&group_id=' . $Group->group_id))
 		->css($iconCss);

		$newGroupWrapper = htmlBase::newElement('div')->css(array(
			'float'   => 'left',
			'width'   => '150px',
			'height'  => '200px',
			'padding' => '4px',
			'margin'  => '3px'
		))->attr('group_id', $Group->group_id)
		->addClass('ui-widget ui-widget-content ui-corner-all droppableField')
		->html('<b>' . $Group->group_name . '</b>' . $deleteIcon->draw() . $editIcon->draw() . '<hr>' . $trashBin->draw() . '<hr />' . $sortableList->draw());
		EventManager::attachActionResponse($newGroupWrapper->draw(), 'html');
	}
?>