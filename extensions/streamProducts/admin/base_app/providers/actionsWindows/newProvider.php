<?php
$Providers = Doctrine_Core::getTable('ProductsStreamProviders');
if (isset($_GET['pID'])){
	$Provider = $Providers->find((int)$_GET['pID']);
	$boxHeading = sysLanguage::get('TEXT_INFO_HEADING_EDIT_PROVIDER');
	$boxIntro = sysLanguage::get('TEXT_INFO_EDIT_INTRO');
} else{
	$Provider = $Providers->getRecord();
	$boxHeading = sysLanguage::get('TEXT_INFO_HEADING_NEW_PROVIDER');
	$boxIntro = sysLanguage::get('TEXT_INFO_INSERT_INTRO');
}
$infoBox = htmlBase::newElement('infobox');
$infoBox->setHeader('<b>' . $boxHeading . '</b>');
$infoBox->setButtonBarLocation('top');

$saveButton = htmlBase::newElement('button')->addClass('saveButton')->usePreset('save');
$cancelButton = htmlBase::newElement('button')->addClass('cancelButton')->usePreset('cancel');

$infoBox->addButton($saveButton)->addButton($cancelButton);

$infoBox->addContentRow($boxIntro);

$infoBox->addContentRow(sysLanguage::get('TEXT_ENTRY_PROVIDER_NAME') . '<br>' . tep_draw_input_field('provider_name', $Provider->provider_name));

$ModulesBox = htmlBase::newElement('selectbox')
	->setName('provider_module')
	->addClass('modulesBox')
	->selectOptionByValue($Provider->provider_module);

$ModulesBox->addOption('', 'Please Select');

$Qmodules = Doctrine_Query::create()
	->select('modules_code')
	->from('Modules')
	->where('modules_type = ?', 'stream_provider')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
if ($Qmodules){
	foreach ($Qmodules as $mInfo){
		require(sysConfig::getDirFsCatalog() . 'extensions/streamProducts/providerModules/' . $mInfo['modules_code'] . '/module.php');
		$className = 'StreamProvider' . ucfirst($mInfo['modules_code']);

		$Module = new $className();
		if ($Module->isEnabled() === true){
			$ModulesBox->addOption($Module->getCode(), $Module->getTitle());
		}
	}
}

$infoBox->addContentRow(sysLanguage::get('TEXT_ENTRY_PROVIDER_MODULE') . '<br>' . $ModulesBox->draw());
$infoBox->addContentRow('<b><u>' . sysLanguage::get('TEXT_INFO_HEADING_PROVIDER_MODULE_SETTINGS') . '</u></b><br>' . sysLanguage::get('TEXT_INFO_HEADING_PROVIDER_MODULE_SETTINGS_INFO') . '<br><div class="providerSettings"></div>');

EventManager::attachActionResponse($infoBox->draw(), 'html');

?>