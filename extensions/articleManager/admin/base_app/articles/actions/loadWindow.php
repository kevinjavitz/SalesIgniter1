<?php
	if (file_exists(sysConfig::getDirFsCatalog() . 'extensions/articleManager/admin/base_app/articles/actionsWindows/' . $_GET['windowName'] . '.php')){
		require(sysConfig::getDirFsCatalog() . 'extensions/articleManager/admin/base_app/articles/actionsWindows/' . $_GET['windowName'] . '.php');
	}
	
	EventManager::attachActionResponse('', 'html');
?>