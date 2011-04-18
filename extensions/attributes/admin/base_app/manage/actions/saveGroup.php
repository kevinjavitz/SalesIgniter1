<?php
	$ProductsOptionsGroups = Doctrine::getTable('ProductsOptionsGroups');
	if (isset($_GET['gID'])){
		$Group = $ProductsOptionsGroups->findOneByProductsOptionsGroupsId($_GET['gID']);
	}else{
		$Group = $ProductsOptionsGroups->create();
	}
	
	$Group->products_options_groups_name = $_POST['group_name'];
	$Group->save();

	if (isset($_GET['gID'])){
		EventManager::attachActionResponse(array(
			'success'    => true,
			'group_name' => $Group->products_options_groups_name
		), 'json');
	}else{
		$groupId = $Group->products_options_groups_id;
		$trashBin = htmlBase::newElement('div')->addClass('trashBin')
		->html('Drop Here To Trash<div class="ui-icon ui-icon-trash" style="float:left;"></div>')
		->attr('group_id', $groupId);

		$sortableList = htmlBase::newElement('sortable_list');

		$iconCss = array(
 			'float'    => 'right',
 			'position' => 'relative',
 			'top'      => '-4px',
 			'right'    => '-4px'
		);
		
 		$deleteIcon = htmlBase::newElement('icon')->setType('circleClose')->setTooltip('Click to delete group')
 		->setHref(itw_app_link('action=removeGroup&group_id=' . $groupId))
 		->css($iconCss);

 		$editIcon = htmlBase::newElement('icon')->setType('wrench')->setTooltip('Click to edit group')
 		->setHref(itw_app_link('action=getGroupWindow&group_id=' . $groupId))
 		->css($iconCss);

		$newGroupWrapper = htmlBase::newElement('div')->css(array(
			'float'   => 'left',
			'width'   => '150px',
			'height'  => '165px',
			'padding' => '4px',
			'margin'  => '3px'
		))->attr('group_id', $groupId)
		->addClass('ui-widget ui-widget-content ui-corner-all droppableField')
		->html('<b>' . $Group->products_options_groups_name . '</b>' . $deleteIcon->draw() . $editIcon->draw() . '<hr>' . $trashBin->draw() . '<hr />' . $sortableList->draw());
		
		EventManager::attachActionResponse($newGroupWrapper->draw(), 'html');
	}
?>