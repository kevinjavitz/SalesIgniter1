<?php
	$GeoZones = Doctrine_Core::getTable('GeoZones');
	if (isset($_GET['zID'])){
		$GeoZone = $GeoZones->find((int) $_GET['zID']);
		$boxHeading = sysLanguage::get('TEXT_INFO_HEADING_EDIT_ZONE');
		$boxIntro = sysLanguage::get('TEXT_INFO_EDIT_ZONE_INTRO');
	}else{
		$GeoZone = $GeoZones->getRecord();
		$boxHeading = sysLanguage::get('TEXT_INFO_HEADING_NEW_ZONE');
		$boxIntro = sysLanguage::get('TEXT_INFO_NEW_ZONE_INTRO');
	}

	$infoBox = htmlBase::newElement('infobox');
	$infoBox->setHeader('<b>' . $boxHeading . '</b>');
	$infoBox->setButtonBarLocation('top');

	$saveButton = htmlBase::newElement('button')->addClass('saveButton')->usePreset('save');
	$cancelButton = htmlBase::newElement('button')->addClass('cancelButton')->usePreset('cancel');

	$infoBox->addButton($saveButton)->addButton($cancelButton);

	$infoBox->addContentRow($boxIntro);
	$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_ZONE_NAME') . '<br>' . tep_draw_input_field('geo_zone_name', $GeoZone->geo_zone_name));
	$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_ZONE_DESCRIPTION') . '<br><textarea rows="3" cols="50" name="geo_zone_description">' . $GeoZone->geo_zone_description . '</textarea>');
	
	$infoBox->addContentRow('<b>Associations</b>');
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
			array('align' => 'left', 'text' => sysLanguage::get('TABLE_HEADING_COUNTRY')),
			array('align' => 'left', 'text' => sysLanguage::get('TABLE_HEADING_COUNTRY_ZONE')),
			array('align' => 'right', 'text' => htmlBase::newElement('icon')->setType('insert')->addClass('insertIcon'))
		)
	));
	
	$Zones = $GeoZone->ZonesToGeoZones;
	if (!empty($Zones)){
		foreach($Zones as $zInfo){
			$hiddenCountry = htmlBase::newElement('input')
			->setType('hidden')
			->setName('zone_country_id[]')
			->val($zInfo->zone_country_id)
			->draw();
			
			$hiddenZone = htmlBase::newElement('input')
			->setType('hidden')
			->setName('zone_id[]')
			->val($zInfo->zone_id)
			->draw();
			
			$listTable->addBodyRow(array(
				'columns' => array(
					array('text' => $zInfo->Countries->countries_name . $hiddenCountry),
					array('text' => $zInfo->Zones->zone_name . $hiddenZone),
					array('align' => 'right', 'text' => htmlBase::newElement('icon')->setType('delete')->addClass('deleteIcon')->draw())
				)
			));
		}
	}
	$infoBox->addContentRow($listTable);
	
	EventManager::attachActionResponse($infoBox->draw(), 'html');
?>