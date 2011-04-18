<?php
	if (file_exists(sysConfig::getDirFsCatalog() . 'extensions/articleManager/admin/base_app/topics/actionsWindows/' . $_GET['windowName'] . '.php')){
		require(sysConfig::getDirFsCatalog() . 'extensions/articleManager/admin/base_app/topics/actionsWindows/' . $_GET['windowName'] . '.php');
	}
	
	EventManager::attachActionResponse('', 'html');
?>