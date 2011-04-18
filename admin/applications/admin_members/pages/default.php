<?php
	$Qadmin = Doctrine_Query::create()
	->from('Admin a')
	->leftJoin('a.AdminGroups ag.')
	->orderBy('a.admin_firstname');

	$tableGrid = htmlBase::newElement('grid')
	->usePagination(true)
	->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit']: 25))
	->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 1))
	->setQuery($Qadmin);

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_NAME')),
			array('text' => sysLanguage::get('TABLE_HEADING_EMAIL')),
			array('text' => sysLanguage::get('TABLE_HEADING_GROUPS')),
			array('text' => sysLanguage::get('TABLE_HEADING_LOGNUM')),
			array('text' => sysLanguage::get('TABLE_HEADING_ACTION'))
		)
	));

	$infoBoxes = array();
	$allGetParams = tep_get_all_get_params(array('mID', 'action'));

	$admin = &$tableGrid->getResults();
	if ($admin){
		$deleteButton = htmlBase::newElement('button')->usePreset('delete');
		$editButton = htmlBase::newElement('button')->usePreset('edit');
		foreach($admin as $aInfo){
			$adminId = $aInfo['admin_id'];
			$adminFirstName = $aInfo['admin_firstname'];
			$adminLastName = $aInfo['admin_lastname'];
			$adminEmail = $aInfo['admin_email_address'];
			$adminGroupName = $aInfo['AdminGroups']['admin_groups_name'];
			$adminLogNum = $aInfo['admin_lognum'];
			$adminDateCreated = tep_date_short($aInfo['admin_created']);
			$adminDateModified = tep_date_short($aInfo['admin_modified']);
			$adminDateLastLogin = tep_date_short($aInfo['admin_logdate']);

			if ((!isset($_GET['mID']) || $_GET['mID'] == $adminId) && !isset($mInfo) && !strstr($action, 'new')){
				$mInfo = new objectInfo($aInfo);
			}

			$arrowIcon = htmlBase::newElement('icon')->setType('info')
			->setHref(itw_app_link($allGetParams . 'mID=' . $adminId));

			$tableGrid->addBodyRow(array(
				'rowAttr' => array(
					'infobox_id' => $adminId
				),
				'columns' => array(
					array('text' => $adminFirstName . '&nbsp;' . $adminLastName),
					array('text' => $adminEmail),
					array('text' => $adminGroupName),
					array('text' => $adminLogNum, 'align' => 'center'),
					array('text' => $arrowIcon->draw(), 'align' => 'right')
				)
			));

			$infoBox = htmlBase::newElement('infobox');
			$infoBox->setButtonBarLocation('top');

			$deleteButton->setHref(itw_app_link($allGetParams . 'action=del_member&mID=' . $adminId));
			$editButton->setHref(itw_app_link($allGetParams . 'action=edit_member&mID=' . $adminId));
			$infoBox->addButton($editButton)->addButton($deleteButton);

			$infoBox->setHeader('<b>' . sysLanguage::get('TEXT_INFO_HEADING_DEFAULT') . '</b>');
			$infoBox->addContentRow('<b>' . sysLanguage::get('TEXT_INFO_FULLNAME') . '</b><br />' . $adminFirstName . ' ' . $adminLastName);
	 		$infoBox->addContentRow('<b>' . sysLanguage::get('TEXT_INFO_EMAIL') . '</b><br />' . $adminEmail);
	 		$infoBox->addContentRow('<b>' . sysLanguage::get('TEXT_INFO_GROUP') . '</b><br />' . $adminGroupName);
	 		$infoBox->addContentRow('<b>' . sysLanguage::get('TEXT_INFO_CREATED') . '</b><br />' . $adminDateCreated);
	 		$infoBox->addContentRow('<b>' . sysLanguage::get('TEXT_INFO_MODIFIED') . '</b><br />' . $adminDateModified);
	 		$infoBox->addContentRow('<b>' . sysLanguage::get('TEXT_INFO_LOGDATE') . '</b><br />' . $adminDateLastLogin);
	 		$infoBox->addContentRow('<b>' . sysLanguage::get('TEXT_INFO_LOGNUM') . '</b><br />' . $adminLogNum);

	 		$infoBoxes[$adminId] = $infoBox->draw();
		}
	}

	$infoBox = htmlBase::newElement('infobox');
	$cancelButton = htmlBase::newElement('button')->usePreset('cancel');

	switch($action){
		case 'edit_member':
		case 'new_member':
			$firstNameInput = htmlBase::newElement('input')
			->setName('admin_firstname')
			->setLabel(sysLanguage::get('TEXT_INFO_FIRSTNAME'))
			->setLabelSeparator('<br />')
			->setLabelPosition('before');

			$lastNameInput = htmlBase::newElement('input')
			->setName('admin_lastname')
			->setLabel(sysLanguage::get('TEXT_INFO_LASTNAME'))
			->setLabelSeparator('<br />')
			->setLabelPosition('before');

			$emailInput = htmlBase::newElement('input')
			->setName('admin_email_address')
			->setLabel(sysLanguage::get('TEXT_INFO_EMAIL'))
			->setLabelSeparator('<br />')
			->setLabelPosition('before');

			if (isset($mInfo) && $mInfo->admin_id == 1){
				$groupInput = htmlBase::newElement('input')->setType('hidden')->setName('admin_groups_id');
			}else{
				$groupInput = htmlBase::newElement('selectbox')
				->setName('admin_groups_id')
				->setLabel(sysLanguage::get('TEXT_INFO_GROUP'))
				->setLabelSeparator('<br />')
				->setLabelPosition('before');

				$groupInput->addOption('0', sysLanguage::get('TEXT_NONE'));

				$Qgroups = Doctrine_Query::create()
				->select('admin_groups_id, admin_groups_name')
				->from('AdminGroups')
				->orderBy('admin_groups_name')
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
				if ($Qgroups){
					foreach($Qgroups as $gInfo){
						$groupInput->addOption($gInfo['admin_groups_id'], $gInfo['admin_groups_name']);
					}
				}
			}

			if (isset($mInfo)){
				$firstNameInput->val($mInfo->admin_firstname);
				$lastNameInput->val($mInfo->admin_lastname);
				$emailInput->val($mInfo->admin_email_address);
				$groupInput->selectOptionByValue($mInfo->admin_groups_id);
			}

			$saveButton = htmlBase::newElement('button')
			->setType('submit')
			->usePreset('save')
			->setText((isset($mInfo) ? TEXT_BUTTON_SAVE : sysLanguage::get('TEXT_BUTTON_INSERT')))
			->attr('onclick', 'validateForm();return document.returnValue');

			$cancelButton->setHref(itw_app_link($allGetParams . (isset($mInfo) ? 'mID=' . $mInfo->admin_id : '')));

			$infoBox->setHeader('<b>' . (isset($mInfo) ?  sysLanguage::get('TEXT_INFO_HEADING_EDIT') : TEXT_INFO_HEADING_NEW) . '</b>');
			$infoBox->setForm(array(
				'name'   => 'newmember',
				'action' => itw_app_link($allGetParams . 'action=saveMember' . (isset($mInfo) ? '&mID=' . $mInfo->admin_id : ''))
			));
			$infoBox->addButton($saveButton)->addButton($cancelButton);

			$infoBox->addContentRow($firstNameInput->draw());
			$infoBox->addContentRow($lastNameInput->draw());
			$infoBox->addContentRow($emailInput->draw());
			$infoBox->addContentRow($groupInput->draw());

			if ($action == 'new_member'){
				$infoBoxes['new'] = $infoBox->draw();
			}else{
				$infoBoxes[$mInfo->admin_id] = $infoBox->draw();
			}
			break;
		case 'del_member':
			$infoBox->setHeader('<b>' . sysLanguage::get('TEXT_INFO_HEADING_DELETE') . '</b>');
			if ($mInfo->admin_id == 1 || $mInfo->admin_email_address == STORE_OWNER_EMAIL_ADDRESS){
				$backButton = htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_BACK'))
				->setHref(itw_app_link($allGetParams . 'mID=' . $mInfo->admin_id));

				$infoBox->addButton($backButton);
			}else{
				$infoBox->setForm(array(
					'name'   => 'deletemember',
					'action' => itw_app_link($allGetParams . 'action=deleteMember&mID=' . $mInfo->admin_id)
				));

				$deleteButton = htmlBase::newElement('button')
				->setType('submit')
				->usePreset('delete');

				$cancelButton->setHref(itw_app_link($allGetParams . 'mID=' . $mInfo->admin_id));

				$infoBox->addButton($deleteButton)->addButton($cancelButton);

				$infoBox->addContentRow(sprintf(sysLanguage::get('TEXT_INFO_DELETE_INTRO'), $mInfo->admin_firstname . ' ' . $mInfo->admin_lastname));
			}
			$infoBoxes[$mInfo->admin_id] = $infoBox->draw();
			break;
	}
	require('includes/account_check.js.php');
?>
 <div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
 <br />
 <div style="width:75%;float:left;">
  <div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
   <div style="width:99%;margin:5px;">
   <?php echo $tableGrid->draw();?>
   </div>
  </div>
  <div class="ui-grid-button-bar"><?php
  $groupsButton = htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_GROUPS'))
  ->setHref(itw_app_link(null, 'admin_members', 'groups'));
  $newButton = htmlBase::newElement('button')->usePreset('new')->setText(sysLanguage::get('TEXT_BUTTON_NEW_MEMBER'))
  ->setHref(itw_app_link($allGetParams . 'action=new_member'));

  echo $groupsButton->draw() . $newButton->draw();
  ?></div>
 </div>
 <div style="width:25%;float:right;"><?php
 	if (sizeof($infoBoxes) > 0){
 		foreach($infoBoxes as $aID => $html){
 			echo '<div class="infoboxContainer" id="infobox_' . $aID . '" style="display:none;">' . $html . '</div>';
 		}
 	}
 ?></div>