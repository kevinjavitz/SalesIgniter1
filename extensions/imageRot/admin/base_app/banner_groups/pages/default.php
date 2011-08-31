<?php

	$rows = 0;
	$lID = (int)Session::get('languages_id');

	$Qgroups = Doctrine_Query::create()
	->select('g.*')
	->from('BannerManagerGroups g')
	->orderBy('g.banner_group_name');
	if (isset($_GET['search'])) {
		$search = tep_db_prepare_input($_GET['search']);
		$Qgroups->andWhere('g.banner_group_name LIKE ?', '%' . $search . '%');
	}

	$tableGrid = htmlBase::newElement('grid')
	->usePagination(true)
	->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit'] : 25))
	->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 0))
	->setQuery($Qgroups);

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_CATEGORIES')),
			array('text' => sysLanguage::get('TABLE_HEADING_ACTION'))
		)
	));
	$current_group = null;
	$groups = &$tableGrid->getResults();
	if ($groups){
		foreach($groups as $group){
			$groupId = $group['banner_group_id'];
			$rows++;


			
			$folderIcon = htmlBase::newElement('icon')->setType('folderClosed')
			->setHref(itw_app_link(tep_get_all_get_params(array('action', 'gID')) . 'gID='.$groupId, null, null, 'SSL'));

			$arrowIcon = htmlBase::newElement('icon')
			->setHref(itw_app_link(tep_get_all_get_params(array('action', 'gID')) . 'gID=' . $groupId, null, null, 'SSL'));

			if (isset($_GET['gID']) && $groupId == $_GET['gID']) {
				$current_group = $group; 
				$addCls = 'ui-state-default';
				$onclickLink = itw_app_link(tep_get_all_get_params(array('action', 'gID')) . 'gID=' . $groupId, null, null, 'SSL');
				$arrowIcon->setType('circleTriangleEast');
			}else{
				$addCls = '';
				$onclickLink = itw_app_link(tep_get_all_get_params(array('action', 'gID')) . 'gID=' . $groupId, null, null, 'SSL');
				$arrowIcon->setType('info');
			}

			$tableGrid->addBodyRow(array(
				'addCls'  => $addCls,
				'click'   => 'document.location=\'' . $onclickLink . '\'',
				'columns' => array(
					array('text' => $folderIcon->draw() . $group['banner_group_name']),
					array('text' => $arrowIcon->draw(), 'align' => 'right')
				)
			));
		}
	}

	$infoBox = htmlBase::newElement('infobox');

	$editButton = htmlBase::newElement('button')->usePreset('edit');
	$deleteButton = htmlBase::newElement('button')->usePreset('delete');

	if (!empty($action)){
		$cancelButton = htmlBase::newElement('button')->usePreset('cancel')
		->setHref(itw_app_link(tep_get_all_get_params(array('action')), null, null, 'SSL'));
	}

	switch ($action) {
		case 'delete_group':
			$infoBox->setHeader('<b>' . sysLanguage::get('TEXT_INFO_HEADING_DELETE_CATEGORY') . '</b>');
			$infoBox->setForm(array(
				'name' => 'groups',
				'action' => itw_app_link(tep_get_all_get_params(array('action')) . 'action=deleteGroupConfirm', null, null, 'SSL')
			));

			$deleteButton->setType('submit');

			$infoBox->addButton($deleteButton)->addButton($cancelButton);

			$infoBox->addContentRow(sysLanguage::get('TEXT_DELETE_CATEGORY_INTRO') . tep_draw_hidden_field('banner_group_id', $current_group['banner_group_id'] ));
			$infoBox->addContentRow('<b>' . $current_group['banner_group_name'] . '</b>');

			break;
		default:
			$infoBox->setButtonBarLocation('top');
			if (tep_not_null($current_group)) {
				$groupName = $current_group['banner_group_name'];
				$groupId = $current_group['banner_group_id'];

				$infoBox->setHeader('<b>' . $groupName . '</b>');

				$allGetParams = tep_get_all_get_params(array('action'));
				$editButton->setHref(itw_app_link(tep_get_all_get_params(array('action', 'gID')) . 'gID=' . $groupId, null, 'new_group', 'SSL'));
				$deleteButton->setHref(itw_app_link($allGetParams . 'gID=' . $groupId . '&action=delete_group', null, null, 'SSL'));


				$infoBox->addButton($editButton)->addButton($deleteButton);
				
			}else {
				$infoBox->setHeader('<b>' . sysLanguage::get('EMPTY_CATEGORY') . '</b>');
				$infoBox->addContentRow(sysLanguage::get('TEXT_NO_CHILD_CATEGORIES'));
			}
			break;
	}
?>
 <div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
 <br />
 <table border="0" width="100%" cellspacing="0" cellpadding="3">
  <tr>
   <td class="smallText" align="right" colspan="2"><?php
   $searchForm = htmlBase::newElement('form')
   ->attr('name', 'search')
   ->attr('action', itw_app_link(null, null, null, 'SSL'))
   ->attr('method', 'get');

   $searchField = htmlBase::newElement('input')->setName('search')
   ->setLabel(sysLanguage::get('HEADING_TITLE_SEARCH'))->setLabelPosition('before');
   if (isset($_GET['search'])){
   	$searchField->setValue($_GET['search']);
   }

   $searchForm->append($searchField);
   echo $searchForm->draw();
   ?></td>
  </tr>
 </table>
 <table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
  </tr>
 </table>

 <div style="width:75%;float:left;">
  <div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
   <div style="width:99%;margin:5px;">
   <?php echo $tableGrid->draw();?>
   </div>
  </div>
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
   <tr>
    <td align="right" class="smallText"><?php

    if (!isset($_GET['search'])){
    	$newCatButton = htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_NEW_GROUP'))
    	->setHref(itw_app_link(tep_get_all_get_params(array('action', 'gID')), null, 'new_group', 'SSL'));

    	echo $newCatButton->draw();
    }
    ?>&nbsp;</td>
   </tr>
  </table>
 </div>
 <div style="width:25%;float:right;"><?php echo $infoBox->draw();?></div>