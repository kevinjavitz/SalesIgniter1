<?php
	if ($App->getPageName() == 'infoboxes'){
	$InfoBox = Doctrine_Core::getTable('TemplatesInfoboxes')->findOneByBoxCode($_GET['module']);
	if ($InfoBox){
		$className = 'InfoBox' . ucfirst($_GET['module']);
		if (!class_exists($className)){
			require(sysConfig::getDirFsCatalog() . $InfoBox->box_path . 'infobox.php');
		}
		$classObj = new $className();

		if (is_dir(sysConfig::getDirFsCatalog() . $InfoBox->box_path . 'Doctrine/base/')){
			$dir = new DirectoryIterator(sysConfig::getDirFsCatalog() . $InfoBox->box_path . 'Doctrine/base/');
			$dbConn = $manager->getCurrentConnection();
			$Exporter = $dbConn->export;
			$Importer = $dbConn->import;
			foreach($dir as $dInfo){
				if ($dInfo->isDot()) {
					continue;
				}

				$tableObj = Doctrine_Core::getTable(substr($dInfo->getBasename(), 0, -4));
				if ($Importer->tableExists($tableObj->getTableName())){
					try {
						$Exporter->dropTable($tableObj->getTableName());
					} catch(Exception $e){
						die(print_r($e));
					}
				}
			}
		}

		$InfoBox->delete();
	}
}
elseif (in_array($_GET['moduleType'], array('orderTotal', 'orderPayment', 'orderShipping', 'productType'))) {
	$Installer = new ModuleInstaller($_GET['moduleType'], $_GET['module'], (isset($_GET['extName']) ? $_GET['extName'] : null));
	$Installer->remove();
}

if (isset($_GET['rType']) && $_GET['rType'] == 'ajax'){
	EventManager::attachActionResponse(array(
			'success' => true,
			'moduleType', $_GET['moduleType']
		), 'json');
}
else {
	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action'))), 'redirect');
}
?>