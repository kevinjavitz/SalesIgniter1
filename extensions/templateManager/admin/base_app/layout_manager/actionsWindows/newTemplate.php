<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Stephen
 * Date: 3/26/11
 * Time: 5:14 PM
 * To change this template use File | Settings | File Templates.
 */

$infoBox = htmlBase::newElement('infobox');
$infoBox->setHeader('<b>' . sysLanguage::get('TEXT_INFO_HEADING_NEW') . '</b>');
$infoBox->setButtonBarLocation('top');

$saveButton = htmlBase::newElement('button')->addClass('saveButton')->usePreset('save');
$cancelButton = htmlBase::newElement('button')->addClass('cancelButton')->usePreset('cancel');

$infoBox->addButton($saveButton)->addButton($cancelButton);

$SettingsTable = htmlBase::newElement('table')
->setCellPadding(3)
->setCellSpacing(0);

$SettingsTable->addBodyRow(array(
	'columns' => array(
		array('text' => 'Template Name:'),
		array('text' => htmlBase::newElement('input')->setName('templateName')->attr('id', 'templateName')->draw())
	)
));

$pathMenu = htmlBase::newElement('selectbox')
->setName('templateDirectory');
$pathMenu->addOption('newDir', 'New Directory');

$Templates = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'templates/');
$ignoredTemplates = array(
	'email',
	'help',
	'help-text'
);
foreach($Templates as $tInfo){
	if ($tInfo->isFile() === true || $tInfo->isDot() === true || in_array($tInfo->getBasename(), $ignoredTemplates)){
		continue;
	}
	$pathMenu->addOption($tInfo->getBasename(), $tInfo->getBasename());
}

$SettingsTable->addBodyRow(array(
	'columns' => array(
		array('text' => 'Template Directory: '),
		array('text' => $pathMenu)
	)
));

$SettingsTable->addBodyRow(array(
	'columns' => array(
		array('text' => 'If New Directory Enter Here: '),
		array('text' => htmlBase::newElement('input')->setName('templateNewDirectory')->draw())
	)
));

$infoBox->addContentRow($SettingsTable->draw());

EventManager::attachActionResponse($infoBox->draw(), 'html');
?>