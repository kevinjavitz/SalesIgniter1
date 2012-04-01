<?php
/**
 * Installer class for extensions
 */
class extensionInstaller {

	/**
	 * @param string $extension Name of the extension that's being installed
	 */
	public function __construct($extension){
		global $manager;
		$this->extension = $extension;
		$this->extensionDir = sysConfig::getDirFsCatalog() . 'extensions/' . $extension . '/';
		$this->installError = false;

		$this->extensionInfo = $this->loadXmlFile($this->extensionDir . 'data/info.xml');
		$this->dbConn = $manager->getCurrentConnection();
	}

	/**
	 * Load an xml file
	 * @param string $filePath Absolute path to the xml file
	 * @return SimpleXMLElement
	 */
	public function loadXmlFile($filePath){
		return simplexml_load_file($filePath, 'SimpleXMLElement', LIBXML_NOCDATA);
	}

	/**
	 * Install the extension
	 * @return void
	 */
	public function install(){
		global $appExtension;

		/* Add configuration settings for this extension */
		if (file_exists($this->extensionDir . 'data/base/configuration.xml')){
			$this->addBaseExtensionConfigration();
		}

		/* Create tables for this extension */
		if (is_dir($this->extensionDir . 'Doctrine/base')){
			$this->installBaseExtensionTables();
		}
		
		/* Create tables for other extensions that this extension has */
		if (is_dir($this->extensionDir . 'Doctrine/ext')){
			$this->installExternalExtensionTables();
		}
		
		/* Create tables for other extensions that have tables for this extension */
		$this->installOtherExtensionsTables();

		/* Add columns to core tables for this extension */
		if (file_exists($this->extensionDir . 'data/base/add_columns.xml')){
			$this->addBaseExtensionColumns();
		}
		
		/* Add columns to other extensions tables for this extension */
		if (is_dir($this->extensionDir . 'data/ext')){
			$this->addExternalExtensionColumns();
		}
		
		/* Add columns from other extensions that have columns for this extensions tables */
		$this->addOtherExtensionsColumns();
	}

	/**
	 * Uninstall the extension
	 * @param bool $remove Remove all data and files
	 * @return void
	 */
	public function uninstall($remove = false){
		if ($remove === true){
			/* Remove columns from other extensions that have columns for this extensions tables */
			$this->removeOtherExtensionsColumns();
		
			/* Remove columns to other extensions tables for this extension */
			if (is_dir($this->extensionDir . 'data/ext')){
				$this->removeExternalExtensionColumns();
			}
		
			/* Remove columns to core tables for this extension */
			if (file_exists($this->extensionDir . 'data/base/add_columns.xml')){
				$this->removeBaseExtensionColumns();
			}
		
			$tables = array();
			/* Remove tables for other extensions that have tables for this extension */
			$this->getOtherExtensionsTables(&$tables);

			/* Remove tables for other extensions that this extension has */
			if (is_dir($this->extensionDir . 'Doctrine/ext')){
				$this->getExternalExtensionTables(&$tables);
			}
		
			/* Remove tables for this extension */
			if (is_dir($this->extensionDir . 'Doctrine/base')){
				$this->getBaseExtensionTables(&$tables);
			}
			
			if (!empty($tables)){
				$this->removeTables($tables);
			}
		
			/* Remove configuration settings for this extension */
			if (file_exists($this->extensionDir . 'data/base/configuration.xml')){
				$this->removeBaseExtensionConfigration();
			}
		
			/*
			* @todo: Add method for removing all files
			*/
		}else{
			$cfg = Doctrine_Query::create()
			->update('Configuration')
			->set('configuration_value', '?', 'False')
			->where('configuration_key = ?', (string) $this->extensionInfo->status_key)
			->execute();
		}
	}

	/**
	 * Remove the tables associated with the extension
	 * @param array $tables All the tables to remove
	 * @return void
	 */
	public function removeTables($tables){
		$Exporter = $this->dbConn->export;
		$Importer = $this->dbConn->import;
		$allGone = false;
		$count = 0;
		while($allGone === false){
			foreach($tables as $idx => $table){
				$tableObj = Doctrine_Core::getTable($table);
				if ($Importer->tableExists($tableObj->getTableName())){
					try {
						$Exporter->dropTable($tableObj->getTableName());
						unset($tables[$idx]);
					}catch (Exception $e){
/*						$indexes = $Importer->listTableIndexes($tableObj->getTableName());
						if (sizeof($indexes) > 0){
							foreach($indexes as $indexName){
								$Exporter->dropIndex($tableObj->getTableName(), $indexName);
							}
						}
						$relations = $Importer->listTableRelations($tableObj->getTableName());
						if (sizeof($relations) > 0){
							foreach($relations as $rInfo){
								$Exporter->dropForeignKey($tableObj->getTableName(), $rInfo['local']);
							}
						}
*/						
					}
				}else{
					unset($tables[$idx]);
				}
			}
			$count++;
			if (empty($tables)){
				$allGone = true;
			}elseif ($count > 50){
				print_r($tables);
				itwExit();
			}else{
				reset($tables);
			}
		}
	}

	/**
	 * Add a column to the table specified, data generally comes from an xml file
	 * @param string $tableName Table to add the column to
	 * @param string $colName Column name to add
	 * @param array $colSettings Settings for the column to use
	 * @return void
	 */
	public function addColumn($tableName, $colName, $colSettings){
		$tableObj = Doctrine_Core::getTable($tableName);
		$tableColumns = $this->dbConn->import->listTableColumns($tableObj->getTableName());

		if (array_key_exists($colName, $tableColumns) === false){
			$this->dbConn->export->alterTable($tableObj->getTableName(), array(
				'add' => array(
					$colName => $colSettings
				)
			));
		}
	}

	/**
	 * Remove a column from the specified table
	 * @param string $tableName Table to remove the column from
	 * @param string $colName Column name to remove
	 * @return void
	 */
	public function removeColumn($tableName, $colName){
		$tableObj = Doctrine_Core::getTable($tableName);
		$tableColumns = $this->dbConn->import->listTableColumns($tableObj->getTableName());

		if (array_key_exists($colName, $tableColumns)){
			$this->dbConn->export->alterTable($tableObj->getTableName(), array(
				'remove' => array(
					$colName => array()
				)
			));
		}
	}

	/**
	 * Parses the xml data to add columns to tables
	 * @param SimpleXMLElement $data Xml data containing the column information
	 * @return void
	 */
	public function addColumnsFromXml($data){
		foreach((array) $data as $tableName => $cols){
			foreach((array) $cols as $colName => $colSettings){
				$this->addColumn($tableName, $colName, (array)$colSettings);
			}
		}
	}

	/**
	 * Parses the xml data to remove columns from tables
	 * @param SimpleXMLElement $data Xml data containing the column information
	 * @return void
	 */
	public function removeColumnsFromXml($data){
		foreach((array) $data as $tableName => $cols){
			foreach((array) $cols as $colName => $colSettings){
				$this->removeColumn($tableName, $colName);
			}
		}
	}

	/**
	 * Parses the xml data to add configuration entries
	 * @param SimpleXMLElement $data Xml data containing the configuration data
	 * @return ConfigurationGroup DoctrineCollection
	 */
	public function addConfigurationGroupFromXml($data){
		$Group = new ConfigurationGroup();
		$Group->configuration_group_title = (string) $data->title;
		$Group->configuration_group_key = (string) $data->key;
		$Group->configuration_group_description = (string) $data->description;
		$Group->visible = '0';
		$Group->save();
		return $Group;
	}

	/**
	 * Removes a configuration group and all its configuration entries
	 * @param string $groupKey Configuration group name to remove
	 * @return void
	 */
	public function removeConfigurationGroupFromXml($groupKey){
		Doctrine_Query::create()
		->delete('ConfigurationGroup')
		->where('configuration_group_key = ?', (string) $groupKey)
		->execute();
	}

	/**
	 * Adds configuration entries from xml data
	 * @param SimpleXMLElement $data Xml data containing the configuration data
	 * @param int $groupId group id to add the configuration entries to
	 * @return void
	 */
	public function addConfigFromXml($data, $groupId){
		foreach((array) $data as $configKey => $configSettings){
			$entry = new Configuration();
			$entry->configuration_title = (string) $configSettings->title;
			$entry->configuration_value = (string) $configSettings->value;
			$entry->configuration_key = (string) $configKey;
			$entry->configuration_description = (string) $configSettings->description;
			$entry->configuration_group_id = $groupId;
			$entry->sort_order = (string) $configSettings->sort_order;

			if (isset($configSettings->use_function)){
				$entry->use_function = (string) $configSettings->use_function;
			}

			if (isset($configSettings->set_function)){
				$entry->set_function = (string) $configSettings->set_function;
			}
			$entry->save();

			sysConfig::set($entry->configuration_key, $entry->configuration_value);
		}
	}

	/**
	 * Install tables from doctrine models for the extension
	 * @return void
	 */
	public function installBaseExtensionTables(){
		if (is_dir($this->extensionDir . 'Doctrine/base/')){
			Doctrine_Core::createTablesFromModels($this->extensionDir . 'Doctrine/base/');
		}
	}
	
	/**
	 * Install tables from other extensions doctrine models for the extension
	 * @return void
	 */
	public function installExternalExtensionTables(){
		global $appExtension;
		$dir = new DirectoryIterator($this->extensionDir . 'Doctrine/ext/');
		foreach($dir as $dInfo){
			if ($dInfo->isDot() || $dInfo->isFile()) continue;
				
			$extObj = $appExtension->getExtension($dInfo->getBasename());
			if ($extObj !== false && $extObj->isInstalled() === true){
				Doctrine_Core::createTablesFromModels($dInfo->getPathname());
			}
		}
	}
	
	/**
	 * Install tables for the extension from all other extensions
	 * @return void
	 */
	public function installOtherExtensionsTables(){
		global $appExtension;
		$extensions = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'extensions/');
		foreach($extensions as $extInfo){
			if ($extInfo->isDot() || $extInfo->isFile()) continue;
			
			$curPath = $extInfo->getPathname() . '/';
			$extName = $extInfo->getBasename();
			if (is_dir($curPath . 'Doctrine/ext/' . $this->extension)){
				$extObj = $appExtension->getExtension($extInfo->getBasename());
				if ($extObj !== false && $extObj->isInstalled() === true){
					Doctrine_Core::createTablesFromModels($curPath . 'Doctrine/ext/' . $this->extension);
				}
			}
		}
	}

	/**
	 * Get all the tables for the extension
	 * @param array $tables Array passed by reference to add tables to
	 * @return void
	 */
	public function getBaseExtensionTables(&$tables){
		$dir = new DirectoryIterator($this->extensionDir . 'Doctrine/base/');
		foreach($dir as $dInfo){
			if ($dInfo->isDot()) continue;
			
			$tables[] = substr($dInfo->getBasename(), 0, -4);
		}
	}
	
	/**
	 * Get all the tables for other extensions from the extension
	 * @param array $tables Array passed by reference to add tables to
	 * @return void
	 */
	public function getExternalExtensionTables(&$tables){
		global $appExtension;
		$dir = new DirectoryIterator($this->extensionDir . 'Doctrine/ext/');
		foreach($dir as $dInfo){
			if ($dInfo->isDot()) continue;
				
			$extObj = $appExtension->getExtension($dInfo->getBasename());
			if ($extObj !== false && $extObj->isInstalled() === true){
				$extDir = new DirectoryIterator($dInfo->getPathname());
				foreach($extDir as $dInfo_ext){
					if ($dInfo_ext->isDot()) continue;

					$tables[] = substr($dInfo_ext->getBasename(), 0, -4);
				}
			}
		}
	}
	
	/**
	 * Get all the tables for the extension from other extensions
	 * @param array $tables Array passed by reference to add tables to
	 * @return void
	 */
	public function getOtherExtensionsTables(&$tables){
		global $appExtension;
		$extensions = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'extensions/');
		$tables = array();
		foreach($extensions as $extInfo){
			if ($extInfo->isDot() || $extInfo->isFile()) continue;
			
			$curPath = $extInfo->getPathname() . '/';
			$extName = $extInfo->getBasename();
			if (is_dir($curPath . 'Doctrine/ext/' . $this->extension)){
				$extObj = $appExtension->getExtension($extInfo->getBasename());
				if ($extObj !== false && $extObj->isInstalled() === true){
					$dirObj = new DirectoryIterator($curPath . 'Doctrine/ext/' . $this->extension);
					foreach($dirObj as $tFile){
						if ($tFile->isDot()) continue;
						
						$tables[] = substr($tFile->getBasename(), 0, -4);
					}
				}
			}
		}
	}

	/**
	 * Process the add_columns.xml file for the extension and add the columns
	 * @return void
	 */
	public function addBaseExtensionColumns(){
		$columnData = $this->loadXmlFile($this->extensionDir . 'data/base/add_columns.xml');
		if (sizeof($columnData) > 0){
			$this->addColumnsFromXml($columnData);
		}
	}
	
	/**
	 * Process the add_columns.xml file for the extension and remove the columns
	 * @return void
	 */
	public function removeBaseExtensionColumns(){
		$columnData = $this->loadXmlFile($this->extensionDir . 'data/base/add_columns.xml');
		if (sizeof($columnData) > 0){
			$this->removeColumnsFromXml($columnData);
		}
	}
	
	/**
	 * Process the add_columns.xml file from other extensions for the extension and add the columns
	 * @return void
	 */
	public function addExternalExtensionColumns(){
		global $appExtension;
		$dirObj = new DirectoryIterator($this->extensionDir . 'data/ext/');
		foreach($dirObj as $dInfo){
			if ($dInfo->isDot() || $dInfo->isFile()) continue;
			
			$curPath = $dInfo->getPathname() . '/';
			if (file_exists($curPath . 'add_columns.xml')){
				$extObj = $appExtension->getExtension($dInfo->getBasename());
				if ($extObj !== false && $extObj->isInstalled() === true){
					$columnData = $this->loadXmlFile($curPath . 'add_columns.xml');
					if (sizeof($columnData) > 0){
						$this->addColumnsFromXml($columnData);
					}
				}
			}
		}
	}
	
	/**
	 * Process the add_columns.xml file from other extensions for the extension and remove the columns
	 * @return void
	 */
	public function removeExternalExtensionColumns(){
		global $appExtension;
		$dirObj = new DirectoryIterator($this->extensionDir . 'data/ext');
		foreach($dirObj as $dInfo){
			$curPath = $dInfo->getPathname() . '/';
			
			if (file_exists($curPath . 'add_columns.xml')){
				$extObj = $appExtension->getExtension($dInfo->getBasename());
				if ($extObj !== false && $extObj->isInstalled() === true){
					$columnData = $this->loadXmlFile($curPath . 'add_columns.xml');
					if (sizeof($columnData) > 0){
						$this->removeColumnsFromXml($columnData);
					}
				}
			}
		}
	}
	
	/**
	 * Process the add_columns.xml file from other extensions for the extension and add the columns
	 * @return void
	 */
	public function addOtherExtensionsColumns(){
		global $appExtension;
		$extensions = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'extensions/');
		foreach($extensions as $extInfo){
			if ($extInfo->isDot() || $extInfo->isFile()) continue;
			
			$extName = $extInfo->getBasename();
			$curPath = $extInfo->getPathname() . '/data/ext/' . $this->extension . '/';
			if (is_dir($curPath)){
				if (file_exists($curPath . 'add_columns.xml')){
					$extObj = $appExtension->getExtension($extInfo->getBasename());
					if ($extObj !== false && $extObj->isInstalled() === true){
						$columnData = $this->loadXmlFile($curPath . 'add_columns.xml');
						if (sizeof($columnData) > 0){
							$this->addColumnsFromXml($columnData);
						}
					}
				}
			}
		}
	}
	
	/**
	 * Process the add_columns.xml file from other extensions for the extension and remove the columns
	 * @return void
	 */
	public function removeOtherExtensionsColumns(){
		global $appExtension;
		$extensions = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'extensions/');
		foreach($extensions as $extInfo){
			if ($extInfo->isDot() || $extInfo->isFile()) continue;
			
			$extName = $extInfo->getBasename();
			$curPath = $extInfo->getPathname() . '/data/ext/' . $this->extension . '/';
			if (is_dir($curPath)){
				if (file_exists($curPath . 'add_columns.xml')){
					$extObj = $appExtension->getExtension($extInfo->getBasename());
					if ($extObj !== false && $extObj->isInstalled() === true){
						$columnData = $this->loadXmlFile($curPath . 'add_columns.xml');
						if (sizeof($columnData) > 0){
							$this->removeColumnsFromXml($columnData);
						}
					}
				}
			}
		}
	}
	
	public function addBaseExtensionConfigration(){
		$configData = $this->loadXmlFile($this->extensionDir . 'data/base/configuration.xml');
		if (sizeof($configData) > 0){
			$Group = $this->addConfigurationGroupFromXml($configData);

			$key = (string)$this->extensionInfo->installed_key;

			$Group->Configuration[$key]->configuration_key = $key;
			$Group->Configuration[$key]->configuration_value = 'True';
			$Group->save();
		}
	}

	public function removeBaseExtensionConfigration(){
		$configData = $this->loadXmlFile($this->extensionDir . 'data/base/configuration.xml');
		$this->removeConfigurationGroupFromXml($configData->key);
	}

	public function checkUpgrades(){
		global $messageStack;

		$currentVersion = (string)$this->extensionInfo->version;
		$currentKey = (string)$this->extensionInfo->key;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://www.itwebexperts.com/rs2_extensions/ext.php');
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'action=check&k=' . $currentKey . '&v=' . $currentVersion);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);  //Windows 2003 Compatibility
		//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

		$response = curl_exec($ch);
		curl_close($ch);

		switch(trim($response)){
			case 'True_dev':
				$messageStack->addSession('pageStack', 'Upgrades are available, please contact <b><u><a href="http://www.itwebexperts.com">I.T. Web Experts</a></u></b> to get them', 'warning');
				return false;
				break;
			case 'True':
				$messageStack->addSession('pageStack', 'Upgrades are available, please click upgrade to get them', 'warning');
				return true;
				break;
			default:
				$messageStack->addSession('pageStack', 'No upgrades available', 'warning');
				return false;
				break;
		}
	}

	public function runUpgrades(){
		global $messageStack;
		$currentVersion = (string) $this->extensionInfo->version;
		$currentKey = (string) $this->extensionInfo->key;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://www.itwebexperts.com/rs2_extensions/ext.php');
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'action=run&k=' . $currentKey . '&v=' . $currentVersion);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);  //Windows 2003 Compatibility
		//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

		$response = curl_exec($ch);
		curl_close($ch);

		if ($response != 'False'){
			$xml = simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA);
			if ($xml){
				$fromExt = str_replace('.', '_', $xml['from']);
				$modPerformed = false;

				if (isset($xml->file_mods)){
					foreach($xml->file_mods->file as $modInfo){
						$modFile = false;
						if (substr($modInfo['path'], 0, 1) == '/'){
							if (file_exists(sysConfig::getDirFsCatalog() . substr($modInfo['path'], 1) . $modInfo['name'])){
								$modFilePath = sysConfig::getDirFsCatalog() . substr($modInfo['path'], 1) . $modInfo['name'];
								$modFile = file($modFilePath);
							}
						}else{
							if (file_exists($this->extensionDir . $modInfo['path'] . $modInfo['name'])){
								$modFilePath = $this->extensionDir . $modInfo['path'] . $modInfo['name'];
								$modFile = file($modFilePath);
							}
						}

						if ($modFile !== false){
							$newFileContent = array();
							$afterLine = false;
							$beforeLine = false;
							$modFound = false;

							foreach($modFile as $lineNum => $line){
								if (stristr($line, 'Extension') && stristr($line, 'Version')){
									$line = substr($line, 0, strpos($line, 'Version')) . 'Version ' . $xml['to'] . "\n";
								}

								$newFileContent[] = $line;

								if (trim($line) == (string)$modInfo->mod->before){
									$beforeLine = $lineNum;
								}
								
								if (trim($line) == (string)$modInfo->mod->after){
									$afterLine = $lineNum;
								}

								if ($afterLine !== false && $beforeLine !== false && $afterLine < $beforeLine){
									$modFound = true;
									array_splice($newFileContent, $afterLine+1, 0, "\n" .
									'/* Auto Upgrade ( Version ' . $xml['from'] . ' to ' . $xml['to'] . ' ) --BEGIN-- */' .
									rtrim((string)$modInfo->mod->content) . "\n" .
									'/* Auto Upgrade ( Version ' . $xml['from'] . ' to ' . $xml['to'] . ' ) --END-- */' .
									"\n"
									);

									$afterLine = false;
									$beforeLine = false;
								}
							}

							if ($modFound === false){
								die('Modification not performed: ' . $beforeLine . '::' . $afterLine . ' -- ' . (string)$modInfo->mod->before);
							}else{
								if (copy($modFilePath, $modFilePath . '_' . $fromExt)){
									$newFile = fopen($modFilePath, 'w+');
									ftruncate($newFile, 0);
									fputs($newFile, implode('', $newFileContent));
									fclose($newFile);
									$modPerformed = true;
								}else{
									$messageStack->addSession('pageStack', 'Could not create backup: ' . $modFilePath, 'error');
									return false;
								}
							}
						}else{
							$messageStack->addSession('pageStack', 'File to be modified was not found: ' . $modFilePath, 'error');
							return false;
						}
					}
				}

				if (isset($xml->language_mods)){
					foreach($xml->language_mods->file as $modInfo){
						$modFile = false;
						if (substr($modInfo['path'], 0, 1) == '/'){
							if (file_exists(sysConfig::getDirFsCatalog() . substr($modInfo['path'], 1) . $modInfo['name'])){
								$modFilePath = sysConfig::getDirFsCatalog() . substr($modInfo['path'], 1) . $modInfo['name'];
								$modFile = file($modFilePath);
							}
						}else{
							if (file_exists($this->extensionDir . $modInfo['path'] . $modInfo['name'])){
								$modFilePath = $this->extensionDir . $modInfo['path'] . $modInfo['name'];
								$modFile = file($modFilePath);
							}
						}

						if ($modFile !== false){
							$newFileContent = array();
							$afterLine = false;
							$beforeLine = false;
							$modFound = false;

							$lastLine = sizeof($modFile)-1;

							$newFileContent = $modFile;
							array_splice($newFileContent, $lastLine-1, 0, "\n" .
							'<!-- Auto Upgrade ( Version ' . $xml['from'] . ' to ' . $xml['to'] . ' ) -BEGIN- -->' . "\n" . 
							'	<define key="' . (string)$modInfo->add->key . '"><![CDATA[' . (string)$modInfo->add->text . ']]></define>' . "\n" .
							'<!-- Auto Upgrade ( Version ' . $xml['from'] . ' to ' . $xml['to'] . ' ) -END- -->' . "\n\n"
							);

							if (copy($modFilePath, $modFilePath . '_' . $fromExt)){
								$newFile = fopen($modFilePath, 'w+');
								ftruncate($newFile, 0);
								fputs($newFile, implode('', $newFileContent));
								fclose($newFile);
								$modPerformed = true;
							}else{
								$messageStack->addSession('pageStack', 'Could not create backup: ' . $modFilePath, 'error');
								return false;
							}
						}else{
							$messageStack->addSession('pageStack', 'File to be modified was not found: ' . $modFilePath, 'error');
							return false;
						}
					}
				}
				
				if ($modPerformed === true){
					$this->extensionInfo['version'] = $xml['to'];
					if (copy($this->extensionDir . 'data/info.xml', $this->extensionDir . 'data/info.xml_' . $fromExt)){
						$infoFileXml = simplexml_load_file($this->extensionDir . 'data/info.xml');
						$infoFileXml->version = $xml['to'];

						$infoFile = fopen($this->extensionDir . 'data/info.xml', 'w+');
						ftruncate($infoFile, -1);
						fwrite($infoFile, $infoFileXml->asXml());
						fclose($infoFile);
						
						return true;
					}else{
						$messageStack->addSession('pageStack', 'Could not create backup: ' . $this->extensionDir . 'data/info.xml', 'error');
						return false;
					}
				}
			}else{
				$messageStack->addSession('pageStack', 'Error loading xml string', 'error');
			}
		}else{
			$messageStack->addSession('pageStack', 'Upgrade server returned false::' . 'action=check&k=' . $currentKey . '&v=' . $currentVersion, 'error');
		}
		return false;
	}
}
?>