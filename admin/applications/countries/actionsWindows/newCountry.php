<?php
	$Countries = Doctrine_Core::getTable('Countries');
	if (isset($_GET['cID'])){
		$Country = $Countries->find((int) $_GET['cID']);
		$boxHeading = sysLanguage::get('TEXT_INFO_HEADING_EDIT_COUNTRY');
		$boxIntro = sysLanguage::get('TEXT_INFO_EDIT_INTRO');
	}else{
		$Country = $Countries->getRecord();
		$boxHeading = sysLanguage::get('TEXT_INFO_HEADING_NEW_COUNTRY');
		$boxIntro = sysLanguage::get('TEXT_INFO_INSERT_INTRO');
	}

	$infoBox = htmlBase::newElement('infobox');
	$infoBox->setHeader('<b>' . $boxHeading . '</b>');
	$infoBox->setButtonBarLocation('top');

	$saveButton = htmlBase::newElement('button')->addClass('saveButton')->usePreset('save');
	$cancelButton = htmlBase::newElement('button')->addClass('cancelButton')->usePreset('cancel');

	$infoBox->addButton($saveButton)->addButton($cancelButton);

	$infoBox->addContentRow($boxIntro);
	$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_COUNTRY_NAME') . '<br>' . tep_draw_input_field('countries_name', $Country->countries_name));
	$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_COUNTRY_CODE_2') . '<br>' . tep_draw_input_field('countries_iso_code_2', $Country->countries_iso_code_2));
	$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_COUNTRY_CODE_3') . '<br>' . tep_draw_input_field('countries_iso_code_3', $Country->countries_iso_code_3));
	$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_ADDRESS_FORMAT') . '<br>' . tep_draw_pull_down_menu('address_format_id', tep_get_address_formats(), $Country->address_format_id));
	
	$infoBox->addContentRow('<b>Countries Zones</b>');
	$listTable = htmlBase::newElement('table')
	->setCellSpacing(0)
	->setCellPadding(2)
	->addClass('ui-widget ui-widget-content')
	->css(array(
		'width' => '50%'
	));
		
	$listTable->addHeaderRow(array(
		'addCls' => 'ui-state-hover',
		'columns' => array(
			array('align' => 'left', 'text' => sysLanguage::get('TABLE_HEADING_ZONE_NAME')),
			array('align' => 'left', 'text' => sysLanguage::get('TABLE_HEADING_ZONE_CODE')),
			array('align' => 'right', 'text' => htmlBase::newElement('icon')->setType('insert')->addClass('insertIcon'))
		)
	));
	
	$Zones = $Country->Zones;
	if (!empty($Zones)){
		foreach($Zones as $zInfo){
			$hiddenZone = htmlBase::newElement('input')
			->setType('hidden')
			->setName('zone_id[]')
			->val($zInfo->zone_id)
			->draw();
			
			$hiddenCode = htmlBase::newElement('input')
			->setType('hidden')
			->setName('zone_code[]')
			->val($zInfo->zone_code)
			->draw();
			
			$listTable->addBodyRow(array(
				'columns' => array(
					array('text' => $zInfo->zone_name . $hiddenZone),
					array('text' => $zInfo->zone_code . $hiddenCode),
					array('align' => 'right', 'text' => htmlBase::newElement('icon')->setType('delete')->addClass('deleteIcon')->draw())
				)
			));
		}
	}
	$infoBox->addContentRow($listTable);
	
	EventManager::attachActionResponse($infoBox->draw(), 'html');
?>