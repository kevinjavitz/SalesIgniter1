<?php
	$extDownload = $appExtension->getExtension('downloadProducts');
	
	$ProviderInfo = $extDownload->getProviderInfo((int) $_GET['pID']);
	
	$Provider = $extDownload->getProviderModule(
		$ProviderInfo['provider_module'],
		$ProviderInfo['provider_module_settings']
	);
	
	$infoBox = htmlBase::newElement('infobox');
	$infoBox->setHeader('<b>' . $ProviderInfo['provider_name'] . ' Browser</b>');
	$infoBox->setButtonBarLocation('top');

	$backButton = htmlBase::newElement('button')->addClass('backButton')->usePreset('back');

	$infoBox->addButton($backButton);
	
	$infoBox->addContentRow($Provider->getFileBrowser());
	
	EventManager::attachActionResponse($infoBox->draw(), 'html');
?>