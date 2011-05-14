<?php
class ExtensionBase {

	public function __construct($extensionKey){
		$this->extName = $extensionKey;
		$this->dir = sysConfig::getDirFsCatalog() . 'extensions/' . $this->extName . '/';

		$this->info = $this->loadXmlFile($this->dir . 'data/info.xml');
		$this->addColumns = $this->loadXmlFile($this->dir . 'data/base/add_columns.xml');
		$this->config = array();

		$configXml = $this->loadXmlFile($this->dir . 'data/base/configuration.xml');
		if (is_null($configXml) === false){
			$this->parseConfigXmlToArray($configXml->Configuration);
		}

		if ($this->isInstalled() === false){
			$this->enabled = (bool) false;
		}else{
			$this->enabled = (bool) (constant((string) $this->info->status_key) == 'True' ? true : false);
		}
	}

	public function loadXmlFile($filePath){
		if (file_exists($filePath)){
			return simplexml_load_file($filePath, 'SimpleXMLElement', LIBXML_NOCDATA);
		}
		return null;
	}

	public function parseConfigXmlToArray($data){
		foreach((array) $data as $configKey => $configSettings){
			$this->config[] = $configKey;
		}
	}

	public function getOtherExtensionsConfig(){
		$dirObj = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'extensions/');
		foreach($dirObj as $dInfo){
			if ($dInfo->isDot()) continue;

			$curPath = $dInfo->getPathname() . '/data/ext/' . $this->extName . '/';
			if (file_exists($curPath . 'configuration.xml')){
				$configData = $this->loadXmlFile($curPath . 'configuration.xml');
				if (sizeof($configData) > 0){
					$this->parseConfigXmlToArray($configData->Configuration);
				}
			}
		}
	}

	public function isInstalled(){
		return sysConfig::exists((string) $this->info->status_key);
	}

	public function isEnabled(){
		return $this->enabled;
	}

	public function getExtensionKey(){
		return (string) $this->info->key;
	}

	public function getExtensionConfigKeys(){
		return (array) $this->config;
	}

	public function getExtensionVersion(){
		return (string) $this->info->version;
	}

	public function getExtensionName(){
		return (string) $this->info->name;
	}

	public function getExtensionDir(){
		return $this->dir;
	}

	public function hasDoctrine(){
		return is_dir($this->dir . 'Doctrine/');
	}

	public function setUpAddColumns(){
		global $manager, $messageStack;
		$dbConn = $manager->getCurrentConnection();
		if ($this->enabled && sizeof($this->addColumns) > 0){
			foreach((array) $this->addColumns as $tableName => $cols){
				if (Doctrine_Core::isValidModelClass($tableName)){
					$tableObj = Doctrine_Core::getTable($tableName);
					$tableObjRecord = $tableObj->getRecordInstance();

					$tableColumns = $dbConn->import->listTableColumns($tableObj->getTableName());

					foreach((array) $cols as $colName => $colSettings){
						$length = (isset($colSettings['length']) ? $colSettings['length'] : null);
						if (array_key_exists($colName, $tableColumns) === false){
							ExceptionManager::report('Database table column does not exist.', E_USER_ERROR, array(
									'Extension Name' => $this->extName,
									'Table Name'     => $tableName,
									'Column Name'    => $colName,
									'Resoultion'     => '<a href="' . itw_app_link('action=fixMissingColumns&extName=' . $this->extName, 'extensions', 'default') . '">Click here to resolve</a>'
								));
						}else{
							$tableObjRecord->hasColumn($colName, $colSettings['type'], $length);
						}
					}
				}
			}
		}
	}

	public function setUpExtAddColumns(){
		global $manager, $appExtension, $messageStack;
		$dbConn = $manager->getCurrentConnection();
		$extObj = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'extensions');
		foreach($extObj as $eInfo){
			if ($eInfo->isDot() || $eInfo->isFile()) continue;

			$extCheck = $appExtension->getExtension($eInfo->getBasename());
			if ($extCheck !== false && $extCheck->isInstalled() === true){
				if (is_dir($eInfo->getPathname() . '/data/ext/' . $this->extName)){
					$addColumns = simplexml_load_file($eInfo->getPathname() . '/data/ext/' . $this->extName . '/add_columns.xml', 'SimpleXMLElement', LIBXML_NOCDATA);
					foreach((array) $addColumns as $tableName => $cols){
						if (Doctrine_Core::isValidModelClass($tableName)){
							$tableObj = Doctrine_Core::getTable($tableName);
							$tableObjRecord = $tableObj->getRecordInstance();

							$tableColumns = $dbConn->import->listTableColumns($tableObj->getTableName());

							foreach((array) $cols as $colName => $colSettings){
								$length = (isset($colSettings['length']) ? $colSettings['length'] : null);
								if (array_key_exists($colName, $tableColumns) === false){
									ExceptionManager::report('Database table column does not exist.', E_USER_ERROR, array(
											'Extension Name' => $this->extName,
											'Table Name'     => $tableName,
											'Column Name'    => $colName,
											'Resoultion'     => '<a href="' . itw_app_link('action=fixMissingColumns&extName=' . $this->extName, 'extensions', 'default') . '">Click here to resolve</a>'
										));
								}else{
									$tableObjRecord->hasColumn($colName, $colSettings['type'], $length);
								}
							}
						}
					}
				}
			}
		}
	}

	public function setUpDoctrine(){
		global $App;
		if ($this->hasDoctrine()){
			$extObj = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'extensions/' . $this->extName . '/Doctrine/base/');
			foreach($extObj as $eInfo){
				if ($eInfo->isDot() || $eInfo->isDir()) continue;
				Doctrine_Core::addExtModelsDirectory($eInfo->getBasename('.php'), $eInfo->getPath() . '/');
			}
			//Doctrine_Core::loadModels(sysConfig::getDirFsCatalog() . 'extensions/' . $this->extName . '/Doctrine/base/', Doctrine_Core::MODEL_LOADING_CONSERVATIVE);

			if (isset($_GET['verifyModels'])){
				$extObj = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'extensions/' . $this->extName . '/Doctrine/base/');
				foreach($extObj as $eInfo){
					if ($eInfo->isDot() || $eInfo->isDir()) continue;

					$App->checkModel($eInfo->getBasename('.php'), $this->extName);
				}
			}
		}
	}

	public function setUpExtDoctrine(){
		global $App, $appExtension;
		$extObj = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'extensions');
		foreach($extObj as $eInfo){
			if ($eInfo->isDot() || $eInfo->isFile()) continue;

			$extCheck = $appExtension->getExtension($eInfo->getBasename());
			if ($extCheck !== false && $extCheck->isInstalled() === true){
				if (is_dir($eInfo->getPathname() . '/Doctrine/ext/' . $this->extName)){
					$exteObj = new DirectoryIterator($eInfo->getPathname() . '/Doctrine/ext/' . $this->extName);
					foreach($exteObj as $eeInfo){
						if ($eeInfo->isDot() || $eeInfo->isDir()) continue;
						
						Doctrine_Core::addExtModelsDirectory($eeInfo->getBasename('.php'), $eeInfo->getPath() . '/');
					}
					//Doctrine_Core::loadModels($eInfo->getPathname() . '/Doctrine/ext/' . $this->extName, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);

					if (isset($_GET['verifyModels'])){
						$extObj = new DirectoryIterator($eInfo->getPathname() . '/Doctrine/ext/' . $this->extName);
						foreach($extObj as $eeInfo){
							if ($eeInfo->isDot() || $eeInfo->isDir()) continue;

							$App->checkModel($eeInfo->getBasename('.php'), $this->extName);
						}
					}
				}
			}
		}
	}

	public function fixMissingColumns(){
		global $manager, $App, $appExtension, $messageStack;
		$dbConn = $manager->getCurrentConnection();

		if ($this->hasDoctrine()){
			$extObj = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'extensions/' . $this->extName . '/Doctrine/base/');
			foreach($extObj as $eInfo){
				if ($eInfo->isDot() || $eInfo->isDir()) continue;
				$App->addMissingModelColumns($eInfo->getBasename('.php'), $this->extName);
			}
		}

		if ($this->enabled && sizeof($this->addColumns) > 0){
			foreach((array) $this->addColumns as $tableName => $cols){
				if (Doctrine_Core::isValidModelClass($tableName)){
					$tableObj = Doctrine_Core::getTable($tableName);

					$tableColumns = $dbConn->import->listTableColumns($tableObj->getTableName());

					foreach((array) $cols as $colName => $colSettings){
						$length = (isset($colSettings['length']) ? $colSettings['length'] : null);
						if (array_key_exists($colName, $tableColumns) === false){
							$dbConn->export->alterTable($tableObj->getTableName(), array(
									'add' => array(
										$colName => (array) $colSettings
									)
								));
							$messageStack->addSession('pageStack', '<table><tr><td><b>Server Message:</b></td><td>Database table column added.</td></tr><tr><td><b>Extension Key:</b></td><td>' . $this->extName . '</td></tr><tr><td><b>Table Name:</b></td><td>' . $tableName . '</td></tr><tr><td><b>Column Name:</b></td><td>' . $colName . '</td></tr></table>', 'success');
						}
					}
				}
			}
		}

		$extObj = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'extensions');
		foreach($extObj as $eInfo){
			if ($eInfo->isDot() || $eInfo->isFile()) continue;

			$extCheck = $appExtension->getExtension($eInfo->getBasename());
			if ($extCheck !== false && $extCheck->isInstalled() === true){
				if (is_dir($eInfo->getPathname() . '/data/ext/' . $this->extName)){
					$addColumns = simplexml_load_file($eInfo->getPathname() . '/data/ext/' . $this->extName . '/add_columns.xml', 'SimpleXMLElement', LIBXML_NOCDATA);
					foreach((array) $addColumns as $tableName => $cols){
						if (Doctrine_Core::isValidModelClass($tableName)){
							$tableObj = Doctrine_Core::getTable($tableName);
							$tableObjRecord = $tableObj->getRecordInstance();

							$tableColumns = $dbConn->import->listTableColumns($tableObj->getTableName());

							foreach((array) $cols as $colName => $colSettings){
								$length = (isset($colSettings['length']) ? $colSettings['length'] : null);
								if (array_key_exists($colName, $tableColumns) === false){
									$dbConn->export->alterTable($tableObj->getTableName(), array(
											'add' => array(
												$colName => (array) $colSettings
											)
										));
									$messageStack->addSession('pageStack', '<table><tr><td><b>Server Message:</b></td><td>Database table column added.</td></tr><tr><td><b>Extension Key:</b></td><td>' . $this->extName . '</td></tr><tr><td><b>Table Name:</b></td><td>' . $tableName . '</td></tr><tr><td><b>Column Name:</b></td><td>' . $colName . '</td></tr></table>', 'success');
								}
							}
						}
					}
				}
			}
		}
	}
}

class Extension {
	public function __construct(){
		$this->extDir = sysConfig::getDirFsCatalog() . 'extensions/';
		$this->extBasefile = 'ext.php';
		$this->extensions = array();
	}

	private function loadExtensionClasses(){
		$dirObj = new DirectoryIterator($this->extDir);
		while($dirObj->valid()){
			if ($dirObj->isDot() || $dirObj->isFile() || !file_exists($dirObj->getPathname() . '/' . $this->extBasefile)){
				$dirObj->next();
				continue;
			}

			$extension = $dirObj->getBasename();

			$className = 'Extension_' . $extension;
			if (!class_exists($className)){
				require($dirObj->getPathname() . '/' . $this->extBasefile);
			}

			if (!isset($this->extensions[$extension])){
				$this->extensions[$extension] = new $className;
			}

			if ($this->extensions[$extension]->isInstalled() === false){
				unset($this->extensions[$extension]);
			}

			$dirObj->next();
		}
	}

	public function preSessionInit(){
		if (sizeof($this->extensions) == 0) $this->loadExtensionClasses();

		foreach($this->extensions as $extension => $extCls){
			if (method_exists($extCls, 'preSessionInit') === true){
				$extCls->preSessionInit();
			}
		}
	}

	public function postSessionInit(){
		if (sizeof($this->extensions) == 0) $this->loadExtensionClasses();

		foreach($this->extensions as $extension => $extCls){
			if (method_exists($extCls, 'postSessionInit') === true){
				$extCls->postSessionInit();
			}
		}
	}

	public function isAdmin(){
		global $App;
		return ($App->getEnv() == 'admin');
	}

	public function isCatalog(){
		global $App;
		return ($App->getEnv() == 'catalog');
	}

	public function loadExtensions(){
		if (sizeof($this->extensions) == 0) $this->loadExtensionClasses();
		/*
		 * Must be separate from the foreach below in order for the ext doctrine and add columns to work
		 */
		foreach($this->extensions as $extension => $extCls){
			$this->extensions[$extension]->setUpDoctrine();
		}

		foreach($this->extensions as $extension => $extCls){
			$extCls->setUpExtDoctrine();
			$extCls->setUpAddColumns();
			$extCls->setUpExtAddColumns();
		}

		$this->initExtensions();
	}

	public function bindMethods(&$class){
		foreach($this->extensions as $extension => $extCls){
			if (method_exists($extCls, 'bindMethods')){
				$extCls->bindMethods($class);
			}
		}
	}

	public function getLanguageFiles($appInfo, &$filesArray){
		global $App;
		$findDirBase = $appInfo['env'] . '/base_app/' . $appInfo['appName'] . '/language_defines/';

		$findDirExt = $appInfo['env'] . '/ext_app/';
		if (isset($_GET['appExt'])){
			$findDirExt .= $_GET['appExt'] . '/';
		}
		$findDirExt .= $appInfo['appName'] . '/language_defines/';

		$dirObj = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'extensions/');
		while($dirObj->valid()){
			if ($dirObj->isDot() || $dirObj->isFile() || $dirObj->getBasename() == $appInfo['appName']){
				$dirObj->next();
				continue;
			}

			if (is_dir($dirObj->getPathname() . '/' . $findDirBase)){
				$filesArray[] = $dirObj->getPathname() . '/' . $findDirBase;
			}

			if (is_dir($dirObj->getPathname() . '/' . $findDirExt)){
				$ext = $this->getExtension($dirObj->getBasename());
				if ($ext !== false && $ext->isEnabled()){
					$filesArray[] = $dirObj->getPathname() . '/' . $findDirExt;
				}
			}
			$dirObj->next();
		}
	}

	public function getOverwriteLanguageFiles($appInfo, &$filesArray){
		global $App;
		$findDirBase = $appInfo['env'] . '/base_app/' . $appInfo['appName'] . '/';

		$findDirExt = $appInfo['env'] . '/ext_app/';
		if (isset($_GET['appExt'])){
			$findDirExt .= $_GET['appExt'] . '/';
		}
		$findDirExt .= $appInfo['appName'] . '/';

		if (is_dir(sysConfig::getDirFsCatalog() . 'includes/languages/' . Session::get('language') . '/extensions/')){
			$dirObj = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'includes/languages/' . Session::get('language') . '/extensions/');
			while($dirObj->valid()){
				if ($dirObj->isDot() || $dirObj->isFile() || $dirObj->getBasename() == $appInfo['appName']){
					$dirObj->next();
					continue;
				}

				if (is_dir($dirObj->getPathname() . '/' . $findDirBase)){
					$filesArray[] = $dirObj->getPathname() . '/' . $findDirBase;
				}

				if (is_dir($dirObj->getPathname() . '/' . $findDirExt)){
					$ext = $this->getExtension($dirObj->getBasename());
					if ($ext !== false && $ext->isEnabled()){
						$filesArray[] = $dirObj->getPathname() . '/' . $findDirExt;
					}
				}
				$dirObj->next();
			}
		}
	}

	public function getGlobalFiles($folder, $appInfo, &$filesArray){
		global $App;
		$findDir = $appInfo['env'] . '/' . $folder . '/';

		if (isset($appInfo['format']) && $appInfo['format'] == 'relative'){
			$returnDir = sysConfig::getDirWsCatalog() . 'extensions/';
		}else{
			$returnDir = sysConfig::getDirFsCatalog() . 'extensions/';
		}

		$dirObj = new DirectoryIterator($this->extDir);
		while($dirObj->valid()){
			if ($dirObj->isDot() || $dirObj->isFile()){
				$dirObj->next();
				continue;
			}

			if (is_dir($dirObj->getPathname() . '/' . $findDir)){
				$ext = $this->getExtension($dirObj->getBasename());
				if ($ext !== false && $ext->isEnabled()){
					$globalDirObj = new DirectoryIterator($dirObj->getPathname() . '/' . $findDir);
					while($globalDirObj->valid()){
						if ($globalDirObj->isDot() || $globalDirObj->isDir()){
							$globalDirObj->next();
							continue;
						}

						$filesArray[] = $returnDir . $dirObj->getBasename() . '/' . $findDir . $globalDirObj->getBasename();

						$globalDirObj->next();
					}
				}
			}
			$dirObj->next();
		}
	}

	public function getAppFiles($folder, $appInfo, &$filesArray, $verifyFiles = true){
		global $App;
		$findDir = $appInfo['env'] . '/ext_app/';
		if (isset($_GET['appExt'])){
			$findDir .= $_GET['appExt'] . '/';
		}
		$findDir .= $appInfo['appName'] . '/' . $folder . '/';

		if (array_key_exists('appFile', $appInfo) === true){
			$appFile = $appInfo['appFile'];
		}

		if (isset($appInfo['format']) && $appInfo['format'] == 'relative'){
			$returnDir = sysConfig::getDirWsCatalog() . 'extensions/';
		}else{
			$returnDir = sysConfig::getDirFsCatalog() . 'extensions/';
		}

		$dirObj = new DirectoryIterator($this->extDir);
		while($dirObj->valid()){
			if ($dirObj->isDot() || $dirObj->isFile() || $dirObj->getBasename() == $appInfo['appName']){
				$dirObj->next();
				continue;
			}

			if ($verifyFiles === true){
				if (file_exists($dirObj->getPathname() . '/' . $findDir . $appFile)){
					$ext = $this->getExtension($dirObj->getBasename());
					if ($ext !== false && $ext->isEnabled()){
						$filesArray[] = $returnDir . $dirObj->getBasename() . '/' . $findDir . $appFile;
					}
				}
			}else{
				if (is_dir($dirObj->getPathname() . '/' . $findDir)){
					$ext = $this->getExtension($dirObj->getBasename());
					if ($ext !== false && $ext->isEnabled()){
						$filesArray[] = $returnDir . $dirObj->getBasename() . '/' . $findDir;
					}
				}
			}
			$dirObj->next();
		}
	}

	public function initExtensions(){
		global $App;
		foreach($this->extensions as $extName => $clsObj){
			$clsObj->init();

			$className = $extName . '_' . $App->getEnv() . '_';
			$checkFile = sysConfig::getDirFsCatalog() . 'extensions/' . $extName . '/' . $App->getEnv() . '/ext_app/';
			if (isset($_GET['appExt'])){
				$checkFile .= $_GET['appExt'] . '/';
			}

			if ($App->getEnv() == 'admin'){
				$checkFile .=  $App->getAppName() . '/pages/' . $App->getAppPage() . '.php';
				if (file_exists($checkFile)){
					if (isset($_GET['appExt'])){
						$className .= $_GET['appExt'] . '_';
					}
					$className .= $App->getAppName() . '_' . $App->getAppPage();
				}
			}elseif ($App->getEnv() == 'catalog'){
				$checkFile .= $App->getAppName() . '/pages/' . $App->getAppPage() . '.php';
				if (file_exists($checkFile)){
					if (isset($_GET['appExt'])){
						$className .= $_GET['appExt'] . '_';
					}
					$className .= $App->getAppName() . '_' . $App->getAppPage();
				}else{
					$currentFile = basename($_SERVER['PHP_SELF']);
					$checkFile .= substr($currentFile, 0, strrpos($currentFile, '.')) . '/pages/' . $currentFile;
					if (file_exists($checkFile)){
						$className .= substr($currentFile, 0, strrpos($currentFile, '.'));
					}
				}
			}

			if ($className != $extName . '_' . $App->getEnv() . '_'){
				require($checkFile);
				$clsObj->pagePlugin = new $className;
				$clsObj->pagePlugin->load();

				unset($className);
			}
		}
	}

	public function isInstalled($extName){
		return array_key_exists($extName, $this->extensions);
	}

	public function isEnabled($extName){
		if (($ext = $this->getExtension($extName)) !== false){
			return $ext->isEnabled();
		}
		return false;
	}

	public function getExtension($extName){
		if (array_key_exists($extName, $this->extensions)){
			return $this->extensions[$extName];
		}
		return false;
	}

	public function registerAsResource($name, &$resource){
		$this->resources[$name] = $resource;
	}

	public function &getResource($name){
		return $this->resources[$name];
	}

	public function getExtensions(){
		return $this->extensions;
	}
}
?>
