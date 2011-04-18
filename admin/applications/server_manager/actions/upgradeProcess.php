<?php
	if (isset($_GET['part'])){
		switch($_GET['part']){
			case 'download':
				$Manager = new UpgradeManager($_GET['version']);
				$Manager->getUpgradeDatabase();
				$Manager->getUpgradeZip();
				$json = array(
					'success' => true,
					'upgDir' => $Manager->getUpgradeDir(),
					'upgVersion' => $_GET['version']
				);
				break;
			case 'unzip':
				$Manager = new UpgradeManager($_GET['version'], $_GET['upgDir']);
		
				$json = array(
					'success' => true
				);
		
				$Manager->unzip();
				break;
			case 'compare':
				$Manager = new UpgradeManager($_GET['version'], $_GET['upgDir']);
		
				$json = array(
					'success' => true,
					'root' => $Manager->getRootPath(),
					'upgradeDir' => $Manager->getUpgradePath(),
					'files' => array(),
					'db' => array()
				);
		
				$Manager->compareCode(&$json['files']);
				$Manager->compareDatabase(&$json['db']);
				break;
			case 'upgradeFiles':
				/*
				 * Go ahead and create/update the configure file --BEGIN--
				 */
				if (file_exists(sysConfig::getDirFsCatalog() . 'includes/configure.xml')){
					$OldConfigFile = simplexml_load_file(sysConfig::getDirFsCatalog() . 'includes/configure.xml');
				}else{
					$OldConfigFile = simplexml_load_string('<configuration></configuration>');
				}
				$NewConfigFile = simplexml_load_file($_POST['upgradePath'] . 'includes/configure.xml');
				foreach($NewConfigFile->config as $config){
					$hasConfig = false;
					if (isset($OldConfigFile->config)){
						foreach($OldConfigFile->config as $oConfig){
							if ((string) $oConfig->key == (string) $config->key){
								$hasConfig = true;
								break;
							}
						}
					}
		
					if ($hasConfig === false){
						$addedConfig = $OldConfigFile->addChild('config');
						$addedConfig->addAttribute('protected', (string) $config['protected']);
						$addedConfig->addChild('key', (string) $config->key);
						if (defined((string) $config->key)){
							$addedConfig->addChild('value', constant((string) $config->key));
						}else{
							$addedConfig->addChild('value', (string) $config->value);
						}
					}
				}
				$OldConfigFile->asXml(sysConfig::getDirFsCatalog() . 'includes/configure.xml');
				/*
				 * Go ahead and create/update the configure file --END--
				 */
				
				$Manager = new UpgradeManager($_POST['version']);
				$Manager->upgradeFiles($_POST['rootPath'], $_POST['upgradePath']);
				
				$Manager->upgradeDatabase(array(
					'createTables',
					'addColumns'
				));
				
				$json = array(
					'success' => true
				);
				break;
			case 'upgradeDatabase':
				$Manager = new UpgradeManager($_POST['version']);
				$Manager->upgradeDatabase(array(
					'updateData',
					'removeColumns',
					'removeTables'
				));
				$Manager->finish();
				
				$json = array(
					'success' => true
				);
				break;
		}
	}
	
	header('Content-Type: text/json');
	echo json_encode($json);
	itwExit();
?>