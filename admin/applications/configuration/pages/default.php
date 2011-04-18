<?php
	$tableGrid = htmlBase::newElement('grid');

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_CONFIGURATION_TITLE')),
			array('text' => sysLanguage::get('TABLE_HEADING_CONFIGURATION_VALUE')),
			array('text' => sysLanguage::get('TABLE_HEADING_ACTION'))
		)
	));

	$infoBoxes = array();
	
	$Qconfiguration = Doctrine_Query::create()
	->from('Configuration')
	->where('configuration_group_id = ?', (int)$gID)
	->orderBy('sort_order')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	foreach($Qconfiguration as $Config){
		$configurationId = (int)$Config['configuration_id'];
		$configurationValue = $Config['configuration_value'];
		$useFunction = $Config['use_function'];

		if (!empty($useFunction) && !is_null($useFunction)){
			if (ereg('->', $useFunction)){
				$class_method = explode('->', $useFunction);
				if (!is_object(${$class_method[0]})){
					include(DIR_WS_CLASSES . $class_method[0] . '.php');
					${$class_method[0]} = new $class_method[0]();
				}
				$cfgValue = tep_call_function($class_method[1], $configurationValue, ${$class_method[0]});
			}else{
				$cfgValue = tep_call_function($useFunction, $configurationValue);
			}
		}else{
			$cfgValue = $configurationValue;
		}

		if ((!isset($_GET['cID']) || (isset($_GET['cID']) && ($_GET['cID'] == $configurationId))) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) {
			$cInfo = new objectInfo($Config);
		}

		$arrowIcon = htmlBase::newElement('icon')->setType('info')
		->setHref(itw_app_link('gID=' . $_GET['gID'] . '&cID=' . $configurationId));

		$tableGrid->addBodyRow(array(
			'rowAttr' => array(
				'infobox_id' => $configurationId
			),
			'columns' => array(
				array('text' => $Config['configuration_title']),
				array('text' => strip_tags($cfgValue)),
				array('text' => $arrowIcon->draw(), 'align' => 'right')
			)
		));
		
		$infoBox = htmlBase::newElement('infobox');
		$infoBox->setHeader('<b>' . $Config['configuration_title'] . '</b>');
		$infoBox->setButtonBarLocation('top');

		$editButton = htmlBase::newElement('button')->usePreset('edit')
		->setHref(itw_app_link('gID=' . $_GET['gID'] . '&cID=' . $configurationId . '&action=edit'));

		$infoBox->addButton($editButton);

		$infoBox->addContentRow($Config['configuration_description']);
		$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_DATE_ADDED') . ' ' . tep_date_short($Config['date_added']));

		if (tep_not_null($Config['last_modified'])){
			$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_LAST_MODIFIED') . ' ' . tep_date_short($Config['last_modified']));
		}
		
		$infoBoxes[$configurationId] = $infoBox->draw();
	}

	switch ($action) {
		case 'edit':
			$infoBox = htmlBase::newElement('infobox');
			$infoBox->setHeader('<b>' . $cInfo->configuration_title . '</b>');
			$infoBox->setForm(array(
				'name'   => 'configuration',
				'action' => itw_app_link('gID=' . $_GET['gID'] . '&cID=' . $cInfo->configuration_id . '&action=save'),
				'attr'   => array('enctype' => 'multipart/form-data')
			));

			$saveButton = htmlBase::newElement('button')->setType('submit')->usePreset('save')->setText(sysLanguage::get('IMAGE_UPDATE'));
			$cancelButton = htmlBase::newElement('button')->usePreset('cancel')->setText(sysLanguage::get('IMAGE_CANCEL'))
			->setHref(itw_app_link('gID=' . $_GET['gID'] . '&cID=' . $cInfo->configuration_id));

			$infoBox->addButton($saveButton)->addButton($cancelButton);

			if ($cInfo->set_function) {
				eval('$value_field = ' . $cInfo->set_function . '"' . htmlspecialchars($cInfo->configuration_value) . '");');
			} else {
				$value_field = tep_draw_input_field('configuration_value', $cInfo->configuration_value);
			}
			
			/* One Page Checkout - BEGIN */
			if ($cInfo->set_function && $_GET['gID'] == 7575) {
				eval('$value_field = ' . $cInfo->set_function . '"' . $cInfo->configuration_value . '");');
			}
			/* One Page Checkout - END */

			$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_EDIT_INTRO'));
			$infoBox->addContentRow('<b>' . $cInfo->configuration_title . '</b><br>' . $cInfo->configuration_description . '<br>' . $value_field);
			
			$infoBoxes[$cInfo->configuration_id] = $infoBox->draw();
			break;
	}
?>
 <div class="pageHeading"><?php echo $Qgroup[0]['configuration_group_title'];?></div>
 <br />
 <div style="width:75%;float:left;">
  <div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
   <div style="width:99%;margin:5px;"><?php echo $tableGrid->draw();?></div>
  </div>
 </div>
 <div style="width:25%;float:right;"><?php
 	if (sizeof($infoBoxes) > 0){
 		foreach($infoBoxes as $configId => $html){
 			echo '<div class="infoboxContainer" id="infobox_' . $configId . '" style="display:none;">' . $html . '</div>';
 		}
 	}
 ?></div>