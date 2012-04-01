<?php
$CurrenciesTable = Doctrine_Core::getTable('CurrenciesTable');
if (isset($_GET['cID'])){
	$Currency = $CurrenciesTable->find((int) $_GET['cID']);
}else{
	$Currency = $CurrenciesTable->getRecord();
}

$infoBox = htmlBase::newElement('infobox');
$infoBox->setHeader('<b>' . ($Currency->currencies_id > 0 ? sysLanguage::get('TEXT_INFO_HEADING_EDIT_CURRENCY') : sysLanguage::get('TEXT_INFO_HEADING_NEW_CURRENCY')) . '</b>');
$infoBox->setButtonBarLocation('top');

$saveButton = htmlBase::newElement('button')->addClass('saveButton')->usePreset('save');
$cancelButton = htmlBase::newElement('button')->addClass('cancelButton')->usePreset('cancel');

$infoBox->addButton($saveButton)->addButton($cancelButton);

$titleInput = htmlBase::newElement('input')
	->setLabel(sysLanguage::get('TEXT_INFO_CURRENCY_TITLE'))
	->setLabelPosition('before')
	->setLabelSeparator('<br>')
	->setName('title')
	->setValue($Currency->title);

$codeInput = htmlBase::newElement('input')
	->setLabel(sysLanguage::get('TEXT_INFO_CURRENCY_CODE'))
	->setLabelPosition('before')
	->setLabelSeparator('<br>')
	->setName('code')
	->setValue($Currency->code);

$symbolLeftInput = htmlBase::newElement('input')
	->setLabel(sysLanguage::get('TEXT_INFO_CURRENCY_SYMBOL_LEFT'))
	->setLabelPosition('before')
	->setLabelSeparator('<br>')
	->setName('symbol_left')
	->setValue($Currency->symbol_left);

$symbolRightInput = htmlBase::newElement('input')
	->setLabel(sysLanguage::get('TEXT_INFO_CURRENCY_SYMBOL_RIGHT'))
	->setLabelPosition('before')
	->setLabelSeparator('<br>')
	->setName('symbol_right')
	->setValue($Currency->symbol_right);

$decimalPointInput = htmlBase::newElement('input')
	->setLabel(sysLanguage::get('TEXT_INFO_CURRENCY_DECIMAL_POINT'))
	->setLabelPosition('before')
	->setLabelSeparator('<br>')
	->setName('decimal_point')
	->setValue($Currency->decimal_point);

$thousandsPointInput = htmlBase::newElement('input')
	->setLabel(sysLanguage::get('TEXT_INFO_CURRENCY_THOUSANDS_POINT'))
	->setLabelPosition('before')
	->setLabelSeparator('<br>')
	->setName('thousands_point')
	->setValue($Currency->thousands_point);

$decimalPlacesInput = htmlBase::newElement('input')
	->setLabel(sysLanguage::get('TEXT_INFO_CURRENCY_DECIMAL_PLACES'))
	->setLabelPosition('before')
	->setLabelSeparator('<br>')
	->setName('decimal_places')
	->setValue($Currency->decimal_places);

$valueInput = htmlBase::newElement('input')
	->setLabel(sysLanguage::get('TEXT_INFO_CURRENCY_VALUE'))
	->setLabelPosition('before')
	->setLabelSeparator('<br>')
	->setName('value')
	->setValue($Currency->value);

$infoBox->addContentRow($titleInput->draw());
$infoBox->addContentRow($codeInput->draw());
$infoBox->addContentRow($symbolLeftInput->draw());
$infoBox->addContentRow($symbolRightInput->draw());
$infoBox->addContentRow($decimalPointInput->draw());
$infoBox->addContentRow($thousandsPointInput->draw());
$infoBox->addContentRow($decimalPlacesInput->draw());
$infoBox->addContentRow($valueInput->draw());

if ($Currency->code != sysConfig::get('DEFAULT_CURRENCY')){
	$defaultInput = htmlBase::newElement('checkbox')
		->setLabel(sysLanguage::get('TEXT_INFO_SET_AS_DEFAULT'))
		->setLabelPosition('after')
		->setName('default');
	$infoBox->addContentRow($defaultInput->draw());
}

EventManager::notify('CurrenciesNewWindowBeforeDraw', $infoBox, $Currency);

EventManager::attachActionResponse($infoBox->draw(), 'html');
