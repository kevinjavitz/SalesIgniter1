<?php
	$TaxClasses = Doctrine_Core::getTable('TaxClass');
	if (isset($_GET['cID'])){
		$TaxClass = $TaxClasses->find((int) $_GET['cID']);
		$boxHeading = sysLanguage::get('TEXT_INFO_HEADING_EDIT_TAX_CLASS');
		$boxIntro = sysLanguage::get('TEXT_INFO_EDIT_CLASS_INTRO');
	}else{
		$TaxClass = $TaxClasses->getRecord();
		$boxHeading = sysLanguage::get('TEXT_INFO_HEADING_NEW_TAX_CLASS');
		$boxIntro = sysLanguage::get('TEXT_INFO_INSERT_CLASS_INTRO');
	}

	$infoBox = htmlBase::newElement('infobox');
	$infoBox->setHeader('<b>' . $boxHeading . '</b>');
	$infoBox->setButtonBarLocation('top');

	$saveButton = htmlBase::newElement('button')->addClass('saveButton')->usePreset('save');
	$cancelButton = htmlBase::newElement('button')->addClass('cancelButton')->usePreset('cancel');

	$infoBox->addButton($saveButton)->addButton($cancelButton);

	$infoBox->addContentRow($boxIntro);
	$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_CLASS_TITLE') . '<br>' . tep_draw_input_field('tax_class_title', $TaxClass->tax_class_title));
	$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_CLASS_DESCRIPTION') . '<br>' . tep_draw_input_field('tax_class_description', $TaxClass->tax_class_description));
	
	EventManager::attachActionResponse($infoBox->draw(), 'html');
?>