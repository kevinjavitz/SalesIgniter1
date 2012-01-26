<?php
set_time_limit(0);
require('includes/application_top.php');

function getColumnsToEncode($TableName, $collation){
	global $dbConn;

	$DBtableColumns = $dbConn->import->listTableColumns($TableName);

	$encodeColumns = array();
	$autoIncrementCol = null;
	foreach($DBtableColumns as $colName => $colSettings){
		if ($colSettings['autoincrement'] == 1){
			$autoIncrementCol = $colName;
		}

		if (!empty($colSettings['collation']) && $colSettings['collation'] != $collation){
			$encodeColumns[$colName] = $colSettings;
		}
	}

	return array(
		'autoIncrementCol' => $autoIncrementCol,
		'encodeCols' => $encodeColumns
	);
}

function getEncodeUpdateQueries($TableName, $EncodeInfo){
	$updateQueries = array();
	if (is_null($EncodeInfo['autoIncrementCol']) === false && !empty($EncodeInfo['encodeCols'])){
		$selectCols = array($EncodeInfo['autoIncrementCol']);
		foreach($EncodeInfo['encodeCols'] as $colName => $colInfo){
			$selectCols[] = $colName;
		}

		$Result = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchAll('select ' . implode(', ', $selectCols) . ' from ' . $TableName);
		foreach($Result as $rInfo){
			$update = array();
			foreach($EncodeInfo['encodeCols'] as $colName => $colInfo){
				$encoded = Encoding::fixUTF8($rInfo[$colName]);
				if ($encoded !== false){
					$update[] = $colName . ' = "' . addslashes($encoded) . '"';
				}
			}

			if (!empty($update)){
				$updateQueries[] = 'update ' . $TableName . ' set ' . implode(', ', $update) . ' where ' . $EncodeInfo['autoIncrementCol'] . '=' . $rInfo[$EncodeInfo['autoIncrementCol']] . "\n";
			}
		}
	}
	return $updateQueries;
}

function processUpdateQueries($UpdateQueries){
	foreach($UpdateQueries as $query){
		Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->exec($query);
	}
}


function checkModel($modelName, $charset, $collation){
	global $manager;
	$dbConn = $manager->getCurrentConnection();
	$tableObj = Doctrine_Core::getTable($modelName);
	$tableName = $tableObj->getTableName();
	$isOk = true;
	$isCharset = false;
	$info = array();
	$resLink = '';

	if ($dbConn->import->tableExists($tableName)){
		$tableObjRecord = $tableObj->getRecordInstance();
		$DBtableColumns = $dbConn->import->listTableColumns($tableName);
		$DBtableStatus = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchAssoc('SHOW TABLE STATUS LIKE "' . $tableName . '"');

		$tableColumns = array();

		foreach($DBtableColumns as $k => $v){
			$tableColumns[strtolower($k)] = $v;
		}

		if ($DBtableStatus[0]['Collation'] != $collation){
			//changeTableCollation&table=' . $tableName . '&to=' . $charset . '&collate=' . $collation

			/*$EncodeInfo = getColumnsToEncode($tableName, $collation);
			$UpdateQueries = getEncodeUpdateQueries($tableName, $EncodeInfo);
			Doctrine_Manager::getInstance()
				->getCurrentConnection()
				->exec('alter table ' . $tableName . ' convert to character set ' . $charset . ' collate ' . $collation);

			if (!empty($UpdateQueries)){
				processUpdateQueries($UpdateQueries);
			}  */
		}

		$modelColumns = $tableObj->getColumns();
		$tableIsCharset = $isCharset;

		foreach($modelColumns as $colName => $colSettings){

			if ($colName == 'id') continue;

			if (array_key_exists($colName, $tableColumns) === false){
				$tableObj = Doctrine_Core::getTable($modelName);
				$colSettings = $tableObj->getColumnDefinition($colName);

				$dbConn->export->alterTable($tableObj->getTableName(), array(
						'add' => array(
							$colName => (array)$colSettings
						)
					));


			}else{
				if ($tableIsCharset === true && !empty($tableColumns[$colName]['collation']) && $tableColumns[$colName]['collation'] != $collation){

					//changeColumnCollation&table=' . $tableName . '&column=' . $colName . '&to=' . $charset . '&collate=' . $collation
				}
				if (
					$colSettings['type'] != $tableColumns[$colName]['type'] ||
					$colSettings['length'] != $tableColumns[$colName]['length']
				){
					if (
						($colSettings['type'] != 'timestamp') &&
						($colSettings['type'] != 'datetime') &&
						($colSettings['type'] != 'date') &&
						(($colSettings['type'] == 'clob' && empty($colSettings['length']) && is_null($tableColumns[$colName]['length'])) === false) &&
						(($colSettings['type'] == 'string' && $colSettings['length'] == '999' && is_null($tableColumns[$colName]['length'])) === false) &&						(($colSettings['type'] == 'text' && empty($colSettings['length']) && is_null($tableColumns[$colName]['length'])) === false)
					){
						//syncColumnSettings&model=' . $modelName . '&column=' . $colName
						/*
						 $tableObj = Doctrine_Core::getTable($modelName);
						$colSettings = $tableObj->getColumnDefinition($colName);

						$dbConn->export->alterTable($tableObj->getTableName(), array(
							'change' => array(
								$colName => array(
									'definition' => (array)$colSettings
								)
							)
						));
						  */
					}
				}
			}
		}
		foreach($tableColumns as $colName => $colSettings){
			if (array_key_exists($colName, $modelColumns) === false){
				//removeColumn &table=' . $tableName . '&column=' . $colName

				/*$success = Doctrine_Manager::getInstance()
					->getCurrentConnection()
					->exec('alter table ' . $tableName . ' drop ' . $colName);*/
			}
		}
	}else{

		Doctrine_Core::createTablesFromArray(array(
				$modelName
			));

		$tableObj = Doctrine_Core::getTable($modelName);
		if ($dbConn->import->tableExists($tableObj->getTableName())){


		}
	}

}

function updateAllDbFields(){
	Doctrine_Core::loadAllModels();
	$Models = Doctrine_Core::getLoadedModels();
	sort($Models);

	$DatabaseTables = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc('SHOW TABLES FROM ' . sysConfig::get('DB_DATABASE'));

	$tablesInDb = array();
	foreach($DatabaseTables as $tInfo){
		foreach($tInfo as $tableName){
			$tablesInDb[$tableName] = $tableName;
		}
	}

	foreach($Models as $mInfo){
		$ModelCheck = checkModel($mInfo, sysConfig::get('SYSTEM_CHARACTER_SET'), sysConfig::get('SYSTEM_CHARACTER_SET_COLLATION'));
		if (isset($tablesInDb[$ModelCheck['table_name']])){
			unset($tablesInDb[$ModelCheck['table_name']]);
		}
	}
}

function addMissingConfigs(){
	if(!class_exists('fileSystemBrowser')){
		require(sysConfig::getDirFsCatalog() . 'includes/classes/fileSystemBrowser.php');
	}
	$templates = new fileSystemBrowser(sysConfig::getDirFsCatalog()  . 'extensions/');
	$directories = $templates->getDirectories();

	foreach($directories as $dirInfo){

		$extension = $dirInfo['basename'];
		$extensionDir = sysConfig::getDirFsCatalog() . 'extensions/' . $extension . '/';

		$config = simplexml_load_file($extensionDir . 'data/base/configuration.xml', 'SimpleXMLElement', LIBXML_NOCDATA);
		$ConfigurationGroup = Doctrine_Core::getTable('ConfigurationGroup');

		$Group = Doctrine_Query::create()
			->select('configuration_group_id')
			->from('ConfigurationGroup')
			->where('configuration_group_title = ?', (string) $config->title)
			->fetchOne();
		if ($Group !== false){
			$groupID = $Group['configuration_group_id'];
			foreach((array) $config->Configuration as $configKey => $configSettings){
				$Qcheck = Doctrine_Query::create()
					->select('configuration_id')
					->from('Configuration')
					->where('configuration_key = ?', $configKey)
					->execute();
				if ($Qcheck->count() <= 0){
					$newConfig = new Configuration();
					$newConfig->configuration_key = (string) $configKey;
					$newConfig->configuration_title = (string) $configSettings->title;
					$newConfig->configuration_value = (string) $configSettings->value;
					$newConfig->configuration_description = (string) $configSettings->description;
					$newConfig->configuration_group_id = (int) $groupID;
					$newConfig->sort_order = (int) $configSettings->sort_order;

					if (isset($configSettings->use_function)){
						$newConfig->use_function = (string) $configSettings->use_function;
					}

					if (isset($configSettings->set_function)){
						$newConfig->set_function = (string) $configSettings->set_function;
					}
					$newConfig->save();
				}
				$Qcheck->free();
			}
		}
	}
}

function add_extra_fields($table, $column, $column_attr = 'VARCHAR(255) NULL'){

	$db=sysConfig::get('DB_DATABASE');
	$link = mysql_connect(sysConfig::get('DB_SERVER'), sysConfig::get('DB_SERVER_USERNAME'), sysConfig::get('DB_SERVER_PASSWORD'));
	if (! $link){
		die(mysql_error());
	}
	mysql_select_db($db , $link) or die("Select Error: ".mysql_error());

	$exists = false;
	$columns = mysql_query("show columns from $table");
	while($c = mysql_fetch_assoc($columns)){
		if($c['Field'] == $column){
			$exists = true;
			break;
		}
	}

	if(!$exists){
		mysql_query("ALTER TABLE `$table` ADD `$column`  $column_attr") or die("An error occured when running \n ALTER TABLE `$table` ADD `$column`  $column_attr \n" . mysql_error());
	}

}

function update_configs(){

	/*add_extra_fields('admin','admin_override_password',"VARCHAR( 40 ) NOT NULL DEFAULT  ''");
	add_extra_fields('admin','admins_stores'," text NOT NULL");
	add_extra_fields('admin','admins_main_store',"int(11) NOT NULL");
	add_extra_fields('admin','admin_simple_admin',"int(1) NOT NULL default '0'");
	add_extra_fields('admin','admin_favs_id',"int(11) NOT NULL");
	add_extra_fields('languages','forced_default',"int(1) NOT NULL default '0'");*/


	mkdir(sysConfig::getDirFsCatalog().'temp/pdf');

	require(sysConfig::getDirFsCatalog() . 'includes/classes/ftp/base.php');
	$Ftp = new SystemFTP();
	$Ftp->connect();
	$Ftp->makeWritable(sysConfig::getDirFsCatalog().'images');
	$Ftp->makeWritable(sysConfig::getDirFsCatalog().'cache');
	$Ftp->makeWritable(sysConfig::getDirFsCatalog().'temp');
	$Ftp->makeWritable(sysConfig::getDirFsCatalog().'temp/pdf');
	$Ftp->makeWritable(sysConfig::getDirFsCatalog().'extensions/imageRot/images');
	$Ftp->makeWritable(sysConfig::getDirFsCatalog().'extensions/pdfPrinter/images');
	$Ftp->makeWritable(sysConfig::getDirFsCatalog().'templates');


	require(sysConfig::getDirFsCatalog() . 'includes/classes/fileSystemBrowser.php');
	$templates = new fileSystemBrowser(sysConfig::getDirFsCatalog()  . 'templates/');
	$directories = $templates->getDirectories();

	foreach($directories as $dirInfo){
		$Ftp->makeWritable(sysConfig::getDirFsCatalog().'templates/'.$dirInfo['basename'].'/images');
	}

	$iterator = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator(sysConfig::getDirFsCatalog().'includes/languages'),
		RecursiveIteratorIterator::SELF_FIRST);

	$iterator->setFlags(RecursiveDirectoryIterator::SKIP_DOTS);

	foreach($iterator as $file) {
		if(($file->isDir() || $file->isFile())) {
			$Ftp->makeWritable($file->getRealpath());
		}
	}

	$Ftp->disconnect();

}
function updateModules(){
	//if module system is the old one.
	//install all modules from the old one
}

function updateToolsConfiguration(){
	//needs to be updated manually.
}

function run_updates(){
	updateAllDbFields();
	update_configs();
	addMissingConfigs();
	updateModules();
	updateToolsConfiguration();
}


$ftpConn = ftp_connect(sysConfig::get('SYSTEM_FTP_SERVER'));
if ($ftpConn === false){
	die('Error ftp_connect');
}
else {
	$ftpCmd = ftp_login($ftpConn,sysConfig::get('SYSTEM_FTP_USERNAME') , sysConfig::get('SYSTEM_FTP_PASSWORD'));
	if (!$ftpCmd){
		die('Error ftp_login');
	}
}



$ftpCmd = ftp_chdir($ftpConn, 'public_html');
if (!$ftpCmd){
	die('Error ftp_chdir public_html');
}

run_updates();

ftp_close($ftpConn);

require('includes/application_bottom.php');
?>