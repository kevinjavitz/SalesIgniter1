<?php
	$extDownload = $appExtension->getExtension('downloadProducts');
	
	$ProviderInfo = $extDownload->getProviderInfo((int) $_GET['pID']);
	
	$Provider = $extDownload->getProviderModule(
		$ProviderInfo['provider_module'],
		$ProviderInfo['provider_module_settings']
	);
	
	EventManager::attachActionResponse($Provider->getFileBrowser(), 'html');
?>