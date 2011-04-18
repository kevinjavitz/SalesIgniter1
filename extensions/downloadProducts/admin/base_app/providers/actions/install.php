<?php
	require(sysConfig::getDirFsCatalog() . 'extensions/downloadProducts/providerModules/install.php');
	$Install = new DownloadProviderInstaller($_GET['module'], (isset($_GET['extName']) ? $_GET['extName'] : null));
	$Install->install();
	
	EventManager::attachActionResponse(itw_app_link('appExt=downloadProducts', 'providers', 'modules'), 'redirect');
?>