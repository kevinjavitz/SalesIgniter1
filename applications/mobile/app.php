<?php
$appContent = $App->getAppContentFile();

$pageName = $App->getPageName();
if (file_exists(sysConfig::getDirFsCatalog() . 'applications/mobile/pagesApps/' . $pageName . '.php')){
	require(sysConfig::getDirFsCatalog() . 'applications/mobile/pagesApps/' . $pageName . '.php');
}
