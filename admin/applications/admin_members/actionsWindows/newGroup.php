<?php
$AdminGroupsTable = Doctrine_Core::getTable('AdminGroups');
if (isset($_GET['gID'])){
	$Group = $AdminGroupsTable->find((int) $_GET['gID']);
}else{
	$Group = $AdminGroupsTable->getRecord();
}

$infoBox = htmlBase::newElement('infobox');
$infoBox->setHeader('<b>' . ($Group->admin_groups_id > 0 ? sysLanguage::get('TEXT_INFO_HEADING_EDIT_GROUP') : sysLanguage::get('TEXT_INFO_HEADING_GROUPS')) . '</b>');
$infoBox->setButtonBarLocation('top');

$saveButton = htmlBase::newElement('button')->addClass('saveButton')->usePreset('save');
$cancelButton = htmlBase::newElement('button')->addClass('cancelButton')->usePreset('cancel');

$infoBox->addButton($saveButton)->addButton($cancelButton);

$GroupNameInput = htmlBase::newElement('input')
	->setName('admin_groups_name')
	->setLabel(sysLanguage::get('TEXT_INFO_GROUPS_NAME'))
	->setLabelSeparator('<br />')
	->setLabelPosition('before')
	->val($Group->admin_groups_name);

$CustomerLoginInput = htmlBase::newElement('checkbox')
	->setName('customer_login')
	->setLabel(' Allowed to login as customer')
	->setLabelPosition('after')
	->val(1)
	->setChecked(($Group->customer_login_allowed == 1));

$infoBox->addContentRow($GroupNameInput->draw());
$infoBox->addContentRow($CustomerLoginInput->draw());

EventManager::notify('AdminGroupsNewEditWindowBeforeDraw', $infoBox, $Group);

EventManager::attachActionResponse($infoBox->draw(), 'html');
