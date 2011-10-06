<?php
	$module_type = 'pdfinfoboxes';
	$module_directory = sysConfig::getDirFsCatalog() . 'includes/modules/pdfinfoboxes/';
	$module_key = 'MODULE_PDF_INFOBOXES_INSTALLED';
	$ext_module_directory = sysConfig::getDirFsCatalog() . 'extensions/';
		$InfoBox = Doctrine_Core::getTable('PDFTemplatesInfoboxes')->findOneByBoxCode($_GET['module']);
		if ($InfoBox){
			$className = 'PDFInfoBox' . ucfirst($_GET['module']);
			if (!class_exists($className)){
				require(sysConfig::getDirFsCatalog() . $InfoBox->box_path . 'pdfinfobox.php');
			}
			$classObj = new $className();
		
			if (is_dir(sysConfig::getDirFsCatalog() . $InfoBox->box_path . 'Doctrine/base/')){
				$dir = new DirectoryIterator(sysConfig::getDirFsCatalog() . $InfoBox->box_path . 'Doctrine/base/');
				$dbConn = $manager->getCurrentConnection();
				$Exporter = $dbConn->export;
				$Importer = $dbConn->import;
				foreach($dir as $dInfo){
					if ($dInfo->isDot()) continue;
			
					$tableObj = Doctrine_Core::getTable(substr($dInfo->getBasename(), 0, -4));
					if ($Importer->tableExists($tableObj->getTableName())){
						try {
							$Exporter->dropTable($tableObj->getTableName());
						}catch (Exception $e){
							die(print_r($e));
						}
					}
				}
			}

			$InfoBox->delete();
	}
	
	if (isset($_GET['rType']) && $_GET['rType'] == 'ajax'){
		EventManager::attachActionResponse(array(
			'success' => true
		), 'json');
	}else{
		EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action'))), 'redirect');
	}
?>