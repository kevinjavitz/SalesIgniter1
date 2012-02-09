<?php
if ($App->getPageName() == 'infoboxes'){
	if (isset($_GET['extName'])){
		$moduleDirRel = 'extensions/' . $_GET['extName'] . '/catalog/infoboxes/' . $_GET['module'] . '/';
	}
	else {
		$moduleDirRel = 'includes/modules/infoboxes/' . $_GET['module'] . '/';
	}
	$moduleDir = sysConfig::getDirFsCatalog() . $moduleDirRel;
	if (is_dir($moduleDir . 'Doctrine/base/')){
		Doctrine_Core::createTablesFromModels($moduleDir . 'Doctrine/base/');
	}

	$className = 'InfoBox' . ucfirst($_GET['module']);
	require($moduleDir . 'infobox.php');
	$class = new $className;

	$Infobox = new TemplatesInfoboxes();
	$Infobox->box_code = $class->getBoxCode();
	$Infobox->box_path = $moduleDirRel;
	if (isset($_GET['extName'])){
		$Infobox->ext_name = $_GET['extName'];
	}
	$Infobox->save();
}
elseif (in_array($_GET['moduleType'], array('orderTotal', 'orderPayment', 'orderShipping', 'productType'))) {
	$Installer = new ModuleInstaller($_GET['moduleType'], $_GET['module'], (isset($_GET['extName']) ? $_GET['extName'] : null), (isset($_GET['modulePath']) ? $_GET['modulePath'] : null));
	$Installer->install();
}

if (isset($_GET['rType']) && $_GET['rType'] == 'ajax'){
	EventManager::attachActionResponse(array(
			'success' => true
		), 'json');
}
else {
	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'module', 'moduleType', 'modulePath'))), 'redirect');
}
?>