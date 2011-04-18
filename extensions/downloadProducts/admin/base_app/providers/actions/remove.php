<?php
	require(sysConfig::getDirFsCatalog() . 'extensions/downloadProducts/providerModules/install.php');
	$Install = new DownloadProviderInstaller($_GET['module'], (isset($_GET['extName']) ? $_GET['extName'] : null));
	$Install->remove();
	
	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>