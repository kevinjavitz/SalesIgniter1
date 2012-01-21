<?php
	$Qgroups = Doctrine_Query::create()
	->from('AdminGroups')
	->orderBy('admin_groups_name');

	$tableGrid = htmlBase::newElement('grid')
	->usePagination(true)
	->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit']: 25))
	->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 1))
	->setQuery($Qgroups);

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_GROUPS_NAME')),
			array('text' => sysLanguage::get('TABLE_CUSTOMER_LOGIN_ALLOWED')),
			array('text' => sysLanguage::get('TABLE_HEADING_ACTION'))
		)
	));

	$infoBoxes = array();
	$allGetParams = tep_get_all_get_params(array('gID', 'action'));

	$adminGroups = &$tableGrid->getResults();
	if ($adminGroups){
		$defineButton = htmlBase::newElement('button')->setIcon('lockClosed')->setText(sysLanguage::get('TEXT_BUTTON_FILE_PERMISSION'));
		$editButton = htmlBase::newElement('button')->usePreset('edit');
		$deleteButton = htmlBase::newElement('button')->usePreset('delete');

		foreach($adminGroups as $group){
			$groupId = $group['admin_groups_id'];
			$groupName = $group['admin_groups_name'];

			if ((!isset($_GET['gID']) || ($_GET['gID'] == $groupId || $_GET['gID'] == 'groups')) && !isset($gInfo) && !strstr($action, 'new')){
				$gInfo = new objectInfo($group);
			}

			$arrowIcon = htmlBase::newElement('icon')->setType('info')
			->setHref(itw_app_link($allGetParams . 'gID=' . $groupId));

			$tableGrid->addBodyRow(array(
				'rowAttr' => array(
					'infobox_id' => $groupId
				),
				'columns' => array(
					array('text' => $groupName),
					array('text' => ($group['customer_login_allowed'] == '1' ? 'Yes' : 'No')),
					array('text' => $arrowIcon->draw(), 'align' => 'right')
				)
			));

			$infoBox = htmlBase::newElement('infobox');
			$infoBox->setButtonBarLocation('top');

			$defineButton->setHref(itw_app_link('gID=' . $groupId, null, 'permissions'));
			$editButton->setHref(itw_app_link($allGetParams . 'gID=' . $groupId . '&action=edit_group'));
			$deleteButton->setHref(itw_app_link($allGetParams . 'gID=' . $groupId . '&action=del_group'));
			$infoBox->addButton($defineButton)->addButton($editButton)->addButton($deleteButton);

			$infoBox->setHeader('<b>' . sysLanguage::get('TEXT_INFO_HEADING_DEFAULT_GROUPS') . '</b>');
			$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_DEFAULT_GROUPS_INTRO'));

	 		$infoBoxes[$groupId] = $infoBox->draw();
		}
	}

	$infoBox = htmlBase::newElement('infobox');
	$cancelButton = htmlBase::newElement('button')->usePreset('cancel');

	switch($action){
		case 'edit_group':
		case 'new_group':
			$infoBox->setHeader('<b>' . (isset($gInfo) ?  sysLanguage::get('TEXT_INFO_HEADING_EDIT_GROUP') : TEXT_INFO_HEADING_GROUPS) . '</b>');
			$infoBox->setForm(array(
				'name'   => 'group',
				'action' => itw_app_link('action=saveGroup' . (isset($gInfo) ? '&gID=' . $gInfo->admin_groups_id : ''))
			));

			$groupName = '';
			$customerLogin = 0;
			if (isset($gInfo)){
				$groupName = $gInfo->admin_groups_name;
				$customerLogin = $gInfo->customer_login_allowed;
			}

			$saveButton = htmlBase::newElement('button')
			->setType('submit')
			->usePreset((isset($gInfo) ? 'save' : 'next'))
			->setText((isset($gInfo) ? TEXT_BUTTON_SAVE : sysLanguage::get('TEXT_BUTTON_NEXT')));

			$cancelButton->setHref(itw_app_link($allGetParams . (isset($gInfo) ? 'gID=' . $gInfo->admin_groups_id : '')));

			$infoBox->addButton($saveButton)->addButton($cancelButton);

			$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_GROUPS_NAME'));
			$infoBox->addContentRow(array('text' => tep_draw_input_field('admin_groups_name', $groupName), 'align' => 'center'));
			$infoBox->addContentRow(array('text' => tep_draw_checkbox_field('customer_login', 1, ($customerLogin == 1)) . ' Allowed to login as customer', 'align' => 'center'));

			if ($action == 'new_group'){
				$infoBoxes['new'] = $infoBox->draw();
			}else{
				$infoBoxes[$gInfo->admin_groups_id] = $infoBox->draw();
			}
			break;
		case 'del_group':
			$infoBox->setHeader('<b>' . sysLanguage::get('TEXT_INFO_HEADING_DELETE_GROUPS') . '</b>');
			$infoBox->setForm(array(
				'name'   => 'delete_group',
				'action' => itw_app_link('action=deleteGroup&gID=' . $gInfo->admin_groups_id)
			));

			if ($gInfo->admin_groups_id == 1){
				$infoBox->addContentRow(array(
					'align' => 'center',
					'text'  => sprintf(sysLanguage::get('TEXT_INFO_DELETE_GROUPS_INTRO_NOT'), $gInfo->admin_groups_name)
				));

				$backButton = htmlBase::newElement('button')->usePreset('back')
				->setHref(itw_app_link($allGetParams . 'gID=' . $gInfo->admin_groups_id));

				$infoBox->addButton($backButton);
			}else{
				$infoBox->addContentRow(array(
					'align' => 'center',
					'text'  => sprintf(sysLanguage::get('TEXT_INFO_DELETE_GROUPS_INTRO'), $gInfo->admin_groups_name)
				));

				$deleteButton = htmlBase::newElement('button')->setType('submit')->usePreset('delete');

				$cancelButton->setHref(itw_app_link($allGetParams . 'gID=' . $gInfo->admin_groups_id));

				$infoBox->addButton($deleteButton)->addButton($cancelButton);
			}

			$infoBoxes[$gInfo->admin_groups_id] = $infoBox->draw();
			break;
	}
?>
 <div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
 <br />
 <div style="width:75%;float:left;">
  <div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
   <div style="width:99%;margin:5px;">
   <?php echo $tableGrid->draw();?>
   </div>
  </div>
  <?php if (empty($action)){ ?>
  <table width="100%" cellspacing="0" cellpadding="0">
   <tr>
    <td colspan="2" align="right"><?php
    $backButton = htmlBase::newElement('button')->usePreset('back')
    ->setHref(itw_app_link(null, 'admin_members', 'default'));
    $newButton = htmlBase::newElement('button')->usePreset('new')->setText(sysLanguage::get('TEXT_BUTTON_NEW_GROUP'))
    ->setHref(itw_app_link('action=new_group'));

    echo $backButton->draw() . $newButton->draw();
    ?></td>
   </tr>
  </table>
  <?php } ?>
 </div>
 <div style="width:25%;float:right;"><?php
 	if (sizeof($infoBoxes) > 0){
 		foreach($infoBoxes as $infoBoxId => $html){
 			echo '<div class="infoboxContainer" id="infobox_' . $infoBoxId . '" style="display:none;">' . $html . '</div>';
 		}
 	}
 ?></div>