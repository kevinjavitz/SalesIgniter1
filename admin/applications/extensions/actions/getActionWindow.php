<?php
$extensionCode = $_GET['extension'];

if (file_exists(sysConfig::getDirFsCatalog() . 'extensions/' . $extensionCode . '/actionsWindows/' . $_GET['window'] . '.php')){
	require(sysConfig::getDirFsCatalog() . 'extensions/' . $extensionCode . '/actionsWindows/' . $_GET['window'] . '.php');
}else{
	require(sysConfig::getDirFsAdmin() . 'applications/extensions/actionsWindows/' . $_GET['window'] . '.php');
}
?>