<?php
	require(sysConfig::getDirFsCatalog() . 'extensions/streamProducts/providerModules/install.php');
	$Install = new StreamProviderInstaller($_GET['module'], (isset($_GET['extName']) ? $_GET['extName'] : null));
	$Install->install();
	
	EventManager::attachActionResponse(itw_app_link('appExt=streamProducts', 'providers', 'modules'), 'redirect');
?>