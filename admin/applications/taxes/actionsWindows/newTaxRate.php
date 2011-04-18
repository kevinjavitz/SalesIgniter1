<?php
	$TaxRates = Doctrine_Core::getTable('TaxRates');
	if (isset($_GET['rID'])){
		$TaxRate = $TaxRates->find((int) $_GET['rID']);
		$boxHeading = sysLanguage::get('TEXT_INFO_HEADING_EDIT_TAX_RATE');
		$boxIntro = sysLanguage::get('TEXT_INFO_EDIT_RATE_INTRO');
	}else{
		$TaxRate = $TaxRates->getRecord();
		$boxHeading = sysLanguage::get('TEXT_INFO_HEADING_NEW_TAX_RATE');
		$boxIntro = sysLanguage::get('TEXT_INFO_INSERT_RATE_INTRO');
	}

	$infoBox = htmlBase::newElement('infobox');
	$infoBox->setHeader('<b>' . $boxHeading . '</b>');
	$infoBox->setButtonBarLocation('top');

	$saveButton = htmlBase::newElement('button')->addClass('saveButton')->usePreset('save');
	$cancelButton = htmlBase::newElement('button')->addClass('cancelButton')->usePreset('cancel');

	$infoBox->addButton($saveButton)->addButton($cancelButton);

	$infoBox->addContentRow($boxIntro);
	$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_CLASS_TITLE') . '<br>' . tep_tax_classes_pull_down('name="tax_class_id" style="font-size:10px"', $TaxRate->tax_class_id));
	$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_ZONE_NAME') . '<br>' . tep_geo_zones_pull_down('name="tax_zone_id" style="font-size:10px"', $TaxRate->tax_zone_id));
	$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_TAX_RATE') . '<br>' . tep_draw_input_field('tax_rate', $TaxRate->tax_rate));
	$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_RATE_DESCRIPTION') . '<br>' . tep_draw_input_field('tax_description', $TaxRate->tax_description));
	$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_TAX_RATE_PRIORITY') . '<br>' . tep_draw_input_field('tax_priority', $TaxRate->tax_priority));
	
	EventManager::attachActionResponse($infoBox->draw(), 'html');
?>