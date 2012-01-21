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
	}elseif ($App->getPageName() == 'orderTotal'){
		require(sysConfig::getDirFsCatalog() . 'includes/modules/orderTotalModules/install.php');
		$Install = new OrderTotalInstaller($_GET['module'], (isset($_GET['extName']) ? $_GET['extName'] : null));
		$Install->remove();
	}elseif ($App->getPageName() == 'orderPayment'){
		require(sysConfig::getDirFsCatalog() . 'includes/modules/orderPaymentModules/install.php');
		$Install = new OrderPaymentInstaller($_GET['module'], (isset($_GET['extName']) ? $_GET['extName'] : null));
		$Install->remove();
	}elseif ($App->getPageName() == 'orderShipping'){
		require(sysConfig::getDirFsCatalog() . 'includes/modules/orderShippingModules/install.php');
		$Install = new OrderShippingInstaller($_GET['module'], (isset($_GET['extName']) ? $_GET['extName'] : null));
		$Install->remove();
	}
	
	if (isset($_GET['rType']) && $_GET['rType'] == 'ajax'){
		EventManager::attachActionResponse(array(
			'success' => true
		), 'json');
	}else{
		EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action'))), 'redirect');
	}
?>