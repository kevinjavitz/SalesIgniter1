<?php
	$fieldListing = htmlBase::newElement('div')->attr('id', 'fieldListing')->css(array(
		'display'  => 'block',
		'width'    => '100%',
		'height'   => '250px',
		'overflow' => 'auto'
	));

	$Qfields = Doctrine_Query::create()
	->from('ProductsCustomFields f')
	->leftJoin('f.ProductsCustomFieldsDescription fd')
	->where('fd.language_id = ?', Session::get('languages_id'))
	->execute();
	if ($Qfields->count() > 0){
		$iconCss = array(
			'float'    => 'right',
			'position' => 'relative',
			'top'      => '-4px',
			'right'    => '-4px'
		);

		foreach($Qfields->toArray(true) as $fInfo){
			$fieldId = $fInfo['field_id'];
			$fieldName = $fInfo['ProductsCustomFieldsDescription'][Session::get('languages_id')]['field_name'];
			$inputType = $fInfo['input_type'];
			$showOnSite = $fInfo['show_on_site'];

			$deleteIcon = htmlBase::newElement('icon')->setType('circleClose')->setTooltip('Click to delete field')
			->setHref(itw_app_link('appExt=customFields&action=removeField&field_id=' . $fieldId))
			->css($iconCss);

			$editIcon = htmlBase::newElement('icon')->setType('wrench')->setTooltip('Click to edit field')
			->setHref(itw_app_link('appExt=customFields&windowAction=edit&action=getFieldWindow&fID=' . $fieldId))
			->css($iconCss);

			$htmlFields = '<b><span class="fieldName" field_id="' . $fieldId . '">' . $fieldName . '</span></b>' . $deleteIcon->draw() . $editIcon->draw() . '<br />' . TEXT_TYPE . '<span class="fieldType">' . $inputType . '</span><br />' . sysLanguage::get('TEXT_SHOWN_ON_SITE') . ($showOnSite == '1' ? 'Yes' : 'No');

			EventManager::notify('ProductCustomFieldsAddOptions', &$htmlFields, $fInfo);

			$newFieldWrapper = htmlBase::newElement('div')->css(array(
				'float'   => 'left',
				'width'   => '150px',
				'height'  => '50px',
				'padding' => '4px',
				'margin'  => '3px'
			))->addClass('ui-widget ui-widget-content ui-corner-all draggableField')
			->html($htmlFields);

			$fieldListing->append($newFieldWrapper);
		}
	}

	$groupListing = htmlBase::newElement('div')->attr('id', 'groupListing')->css(array(
		'display'  => 'block',
		'width'    => '100%',
		'height'   => '315px',
		'overflow' => 'auto'
	));

	$Qgroups = Doctrine_Query::create()
	->select('group_id, group_name')
	->from('ProductsCustomFieldsGroups')
	->orderBy('group_name')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if ($Qgroups){
		$trashBin = new htmlElement('div');
		$trashBin->addClass('trashBin')->html(sysLanguage::get('TEXT_TRASH_BIN') . '<div class="ui-icon ui-icon-trash" style="float:left;"></div>');

		foreach($Qgroups as $gInfo){
			$groupId = $gInfo['group_id'];
			$groupName = $gInfo['group_name'];

			$trashBin->attr('group_id', $groupId);
			$sortableList = htmlBase::newElement('sortable_list');

			$Qfields = Doctrine_Query::create()
			->select('f.field_id, fd.field_name, f2g.sort_order')
			->from('ProductsCustomFields f')
			->leftJoin('f.ProductsCustomFieldsDescription fd')
			->leftJoin('f.ProductsCustomFieldsToGroups f2g')
			->where('fd.language_id = ?', Session::get('languages_id'))
			->andWhere('f2g.group_id = ?', $groupId)
			->orderBy('f2g.sort_order')
			->execute();
			if ($Qfields->count() > 0){
				foreach($Qfields->toArray(true) as $fInfo){
					$liObj = new htmlElement('li');
					$liObj->css(array(
						'font-size' => '.8em',
						'line-height' => '1.2em'
					))
					->attr('id', 'field_' . $fInfo['field_id'])
					->attr('sort_order', $fInfo['ProductsCustomFieldsToGroups'][0]['sort_order'])
					->html($fInfo['ProductsCustomFieldsDescription'][Session::get('languages_id')]['field_name']);
					$sortableList->addItemObj($liObj);
				}
			}

			$deleteIcon = htmlBase::newElement('icon')->setType('circleClose')->setTooltip('Click to delete group')
			->setHref(itw_app_link('appExt=customFields&action=removeGroup&group_id=' . $groupId))
			->css($iconCss);

			$editIcon = htmlBase::newElement('icon')->setType('wrench')->setTooltip('Click to edit group')
			->setHref(itw_app_link('appExt=customFields&action=getGroupWindow&group_id=' . $groupId))
			->css($iconCss);

			$newGroupWrapper = htmlBase::newElement('div')->css(array(
				'float'   => 'left',
				'width'   => '150px',
				'height'  => '200px',
				'padding' => '4px',
				'margin'  => '3px'
			))->attr('group_id', $groupId)
			->addClass('ui-widget ui-widget-content ui-corner-all droppableField')
			->html('<b>' . $groupName . '</b>' . $deleteIcon->draw() . $editIcon->draw() . '<hr />' . $trashBin->draw() . '<hr />' . $sortableList->draw());

			$groupListing->append($newGroupWrapper);
		}
	}
?>

 <div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
 <br />
 <div><?php echo htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_NEW_FIELD'))->setId('newField')->draw();?></div>
 <?php echo $fieldListing->draw();?>
 <div><?php echo htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_NEW_GROUP'))->setId('newGroup')->draw();?></div>
 <?php echo $groupListing->draw();?>

 <div id="newGroupDialog" title="<?php echo sysLanguage::get('WINDOW_TITLE_NEW_GROUP');?>" style="display:none;"><table cellpadding="0" cellspacing="0">
  <tr>
   <td class="main"><?php echo sysLanguage::get('ENTRY_GROUP_NAME');?></td>
   <td class="main"><?php echo tep_draw_input_field('group_name');?></td>
  </tr>
 </table></div>