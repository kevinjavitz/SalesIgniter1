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

	$htmlCountryName = htmlBase::newElement('input')
	->setName('countries_name')
	->setValue($Country->countries_name);

	$htmlCountryCode2 = htmlBase::newElement('input')
	->setName('countries_iso_code_2')
	->setValue($Country->countries_iso_code_2);

	$htmlCountryCode3 = htmlBase::newElement('input')
	->setName('countries_iso_code_3')
	->setValue($Country->countries_iso_code_3);

	$htmlAddressFormat = htmlBase::newElement('selectbox')
	->setName('address_format_id')
	->selectOptionByValue($Country->address_format_id);

	$QAddressFormat = Doctrine_Query::create()
	->from('AddressFormat')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	foreach($QAddressFormat as $afInfo){
		$htmlAddressFormat->addOption($afInfo['address_format_id'], $afInfo['address_summary']);
	}

	$infoBox->addContentRow($boxIntro);
	$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_COUNTRY_NAME') . '<br>' . $htmlCountryName->draw());
	$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_COUNTRY_CODE_2') . '<br>' . $htmlCountryCode2->draw());
	$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_COUNTRY_CODE_3') . '<br>' . $htmlCountryCode3->draw());
	$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_ADDRESS_FORMAT') . '<br>' . $htmlAddressFormat->draw());
	
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