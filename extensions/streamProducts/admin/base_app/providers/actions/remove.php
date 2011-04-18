<?php
	require(sysConfig::getDirFsCatalog() . 'extensions/streamProducts/providerModules/install.php');
	$Install = new StreamProviderInstaller($_GET['module'], (isset($_GET['extName']) ? $_GET['extName'] : null));
	$Install->remove();
	
	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>