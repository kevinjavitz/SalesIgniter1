<?php
$AdminTable = Doctrine_Core::getTable('Admin');
if (isset($_GET['aID'])){
	$Admin = $AdminTable->find((int) $_GET['aID']);
}else{
	$Admin = $AdminTable->getRecord();
}

$infoBox = htmlBase::newElement('infobox');
$infoBox->setHeader('<b>' . ($Admin->admin_id > 0 ? sysLanguage::get('TEXT_INFO_HEADING_EDIT') : sysLanguage::get('TEXT_INFO_HEADING_NEW')) . '</b>');
$infoBox->setButtonBarLocation('top');

$saveButton = htmlBase::newElement('button')->addClass('saveButton')->usePreset('save');
$cancelButton = htmlBase::newElement('button')->addClass('cancelButton')->usePreset('cancel');

$infoBox->addButton($saveButton)->addButton($cancelButton);

$firstNameInput = htmlBase::newElement('input')
	->setName('admin_firstname')
	->setLabel(sysLanguage::get('TEXT_INFO_FIRSTNAME'))
	->setLabelSeparator('<br />')
	->setLabelPosition('before')
	->val($Admin->admin_firstname);

$lastNameInput = htmlBase::newElement('input')
	->setName('admin_lastname')
	->setLabel(sysLanguage::get('TEXT_INFO_LASTNAME'))
	->setLabelSeparator('<br />')
	->setLabelPosition('before')
	->val($Admin->admin_lastname);

$emailInput = htmlBase::newElement('input')
	->setName('admin_email_address')
	->setLabel(sysLanguage::get('TEXT_INFO_EMAIL'))
	->setLabelSeparator('<br />')
	->setLabelPosition('before')
	->val($Admin->admin_email_address);

$overridePasswordInput = htmlBase::newElement('input')
	->setName('admin_override_password')
	->setLabel(sysLanguage::get('TEXT_INFO_OVERRIDE_PASSWORD'))
	->setLabelSeparator('<br />')
	->setLabelPosition('before')
	->val($Admin->admin_override_password);

$htmlSimpleAdmin = htmlBase::newElement('checkbox')
	->setName('simple_admin')
	->setId('simpleAdmin')
	->setLabel('Use Simple Admin')
	->setLabelPosition('before')
	->setChecked((($Admin->admin_simple_admin == 1)?true:false));

$favoritesInput = htmlBase::newElement('selectbox')
	->setName('admin_favorites_id')
	->setLabel(sysLanguage::get('TEXT_INFO_FAVORITES'))
	->setLabelSeparator('<br />')
	->setLabelPosition('before');

$favoritesInput->addOption('-1', sysLanguage::get('TEXT_NONE'));
$favoritesInput->addOption('0', sysLanguage::get('TEXT_STANDARD'));

$favoritesInput->selectOptionByValue($Admin->admin_favs_id);

$Qfavs = Doctrine_Query::create()
	->from('AdminFavorites')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
if ($Qfavs){
	foreach($Qfavs as $gInfo){
		$favoritesInput->addOption($gInfo['admin_favs_id'], $gInfo['admin_favs_name']);
	}
}

if ($Admin->admin_id == 1){
	$groupInput = htmlBase::newElement('input')
		->setType('hidden')
		->setName('admin_groups_id')
		->val($Admin->admin_groups_id);
}
else {
	$groupInput = htmlBase::newElement('selectbox')
		->setName('admin_groups_id')
		->setLabel(sysLanguage::get('TEXT_INFO_GROUP'))
		->setLabelSeparator('<br />')
		->setLabelPosition('before')
		->selectOptionByValue($Admin->admin_groups_id);

	$groupInput->addOption('0', sysLanguage::get('TEXT_NONE'));

	$Qgroups = Doctrine_Query::create()
		->select('admin_groups_id, admin_groups_name')
		->from('AdminGroups')
		->orderBy('admin_groups_name')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if ($Qgroups){
		foreach($Qgroups as $gInfo){
			$groupInput->addOption($gInfo['admin_groups_id'], $gInfo['admin_groups_name']);
		}
	}
}

$infoBox->addContentRow($firstNameInput->draw());
$infoBox->addContentRow($lastNameInput->draw());
$infoBox->addContentRow($emailInput->draw());
$infoBox->addContentRow($overridePasswordInput->draw());
$infoBox->addContentRow($groupInput->draw());
$infoBox->addContentRow($htmlSimpleAdmin->draw());
$infoBox->addContentRow($favoritesInput->draw());

EventManager::notify('AdminMembersNewEditWindowBeforeDraw', $infoBox, $Admin);

EventManager::attachActionResponse($infoBox->draw(), 'html');
