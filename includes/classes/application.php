<?php
class Application {
	private $envDir;
	private $addedStylesheetFiles = array();
	private $addedJavascriptFiles = array();

	public function __construct($appName, $appPage){
		$this->appName = $appName;
		$this->appPage = $appPage;
		$this->infoBoxId = null;
		$this->env = APPLICATION_ENVIRONMENT;

		if ($this->env == 'admin'){
			$this->envDirs = array(sysConfig::getDirFsAdmin() . 'applications/' . $this->appName . '/');
		}else{
			$this->envDirs = array(sysConfig::getDirFsCatalog() . 'applications/' . $this->appName . '/');
		}

		if (array_key_exists('appExt', $_GET)){
			$this->envDirs[] = sysConfig::getDirFsCatalog() . 'extensions/' . $_GET['appExt'] . '/' . $this->env . '/base_app/' . $this->appName . '/';
		}

		$this->actionExt = array();

		$this->appLocation = false;
		foreach($this->envDirs as $dir){
			if (file_exists($dir . 'app.php')){
				$this->appLocation = $dir;
				break;
			}
		}

		$this->appDir = array(
			'relative' => $this->getAppLocation('relative'),
			'absolute' => $this->getAppLocation()
		);
	}

	public function isValid(){
		if (!isset($_GET['app'])){
			return true;
		}
		return ($this->appLocation !== false);
	}

	public function setInfoBoxId($val){
		$this->infoBoxId = $val;
	}

	public function getInfoBoxId(){
		return $this->infoBoxId;
	}

	public function getAppPage(){
		return $this->appPage;
	}
	/* To replace function above */

	public function getPageName(){
		return $this->appPage;
	}

	public function setAppPage($pageName){
		$this->appPage = $pageName;
	}

	public function getAppName(){
		return $this->appName;
	}

	public function getAppLocation($type = 'absolute'){
		if ($type == 'relative'){
			if ($this->env == 'admin'){
				return str_replace(array(sysConfig::getDirFsAdmin(), sysConfig::getDirFsCatalog()), array(sysConfig::getDirWsAdmin(), sysConfig::getDirWsCatalog()), $this->appLocation);
			}else{
				return str_replace(array(sysConfig::getDirFsAdmin(), sysConfig::getDirFsCatalog()), '', $this->appLocation);
			}
		}else{
			return $this->appLocation;
		}
	}

	public function getAppFile(){
		return $this->getAppLocation() . 'app.php';
	}

	private function getEnvDirs(){
		return $this->envDirs;
	}

	public function getEnv(){
		return $this->env;
	}

	public function getAppContentFile($useFile = false){
		if ($useFile !== false){
			return $this->appDir['absolute'] . 'pages/' . $useFile;
		}
		return $this->appDir['absolute'] . 'pages/' . $this->getAppPage() . '.php';
	}

	public function loadLanguageDefines(){
		global $appExtension;

		/* TODO: Remove when all applications are built */
		if (!isset($_GET['app']))
			return;

		/*
		 * Load application fallback file
		 */
		$languageFiles = array(
			$this->appDir['absolute'] . 'language_defines/global.xml'
		);

		/*
		 * Load extension files for application
		 */
		$appExtension->getLanguageFiles(array(
				'env' => $this->env,
				'appName' => $this->getAppName()
			), $languageFiles);

		/*
		 * Application definitions overwrite file path
		 */
		$languageFiles[] = sysConfig::getDirFsCatalog() . 'includes/languages/' . Session::get('language') . '/' . $this->env . '/applications/' . $_GET['app'] . '/';

		/*
		 * Application extension definitions overwrite file path
		 */
		$appExtension->getOverwriteLanguageFiles(array(
				'env' => $this->env,
				'appName' => $this->getAppName()
			), $languageFiles);

		/*
		 * Load all definition files and overwrite definitions
		 */
		foreach($languageFiles as $filePath){
			sysLanguage::loadDefinitions($filePath);
		}
	}

	public function getAppBaseJsFiles(){
		global $appExtension;
		$javascriptFiles = array();

		$appExtension->getGlobalFiles('javascript', array(
				'env' => $this->env,
				'format' => 'relative'
			), &$javascriptFiles);

		$pageJsFile = $this->getAppPage() . '.js';
		if (file_exists($this->appDir['absolute'] . 'javascript/' . $pageJsFile)){
			if ($this->env == 'admin'){
				$javascriptFiles[] = $this->appDir['relative'] . 'javascript/' . $pageJsFile;
			}else{
				$javascriptFiles[] = sysConfig::getDirWsCatalog() . $this->appDir['relative'] . 'javascript/' . $pageJsFile;
			}
		}

		$appExtension->getAppFiles('javascript', array(
				'env' => $this->env,
				'appName' => $this->getAppName(),
				'appFile' => $pageJsFile,
				'format' => 'relative'
			), &$javascriptFiles);

		if (!empty($this->addedJavascriptFiles)){
			foreach($this->addedJavascriptFiles as $file){
				if (substr($file, 0, 7) != 'http://'){
					$javascriptFiles[] = sysConfig::getDirWsCatalog() . $file;
				}else{
					$javascriptFiles[] = $file;
				}
			}
		}

		return $javascriptFiles;
	}

	public function getAppBaseStylesheetFiles(){
		global $appExtension;

		$stylesheetFiles = array();

		$pageCssFile = $this->getAppPage() . '.css';
		if (file_exists($this->appDir['absolute'] . 'stylesheets/' . $pageCssFile)){
			$stylesheetFiles[] = sysConfig::getDirWsCatalog() . $this->appDir['relative'] . 'stylesheets/' . $pageCssFile;
		}

		$appExtension->getAppFiles('stylesheets', array(
				'env' => $this->env,
				'appName' => $this->getAppName(),
				'appFile' => $pageCssFile,
				'format' => 'relative'
			), &$stylesheetFiles);

		if (!empty($this->addedStylesheetFiles)){
			foreach($this->addedStylesheetFiles as $file){
				$stylesheetFiles[] = sysConfig::getDirWsCatalog() . $file;
			}
		}

		return $stylesheetFiles;
	}

	function addJavascriptFile($file){
		$this->addedJavascriptFiles[] = $file;
	}

	function hasJavascriptFiles(){
		$files = $this->getAppBaseJsFiles();
		return (!empty($files));
	}

	function getJavascriptFiles(){
		$files = $this->getAppBaseJsFiles();
		return $files;
	}

	function addStylesheetFile($file){
		$this->addedStylesheetFiles[] = $file;
	}

	function hasStylesheetFiles(){
		$files = $this->getAppBaseStylesheetFiles();
		return (!empty($files));
	}

	function getStylesheetFiles(){
		$files = $this->getAppBaseStylesheetFiles();
		return $files;
	}

	public function getActionFiles($action){
		global $appExtension;
		$actionFiles = array();
		if (file_exists($this->appDir['absolute'] . 'actions/' . $action . '.php')){
			$actionFiles[] = $this->appDir['absolute'] . 'actions/' . $action . '.php';
		}

		$appExtension->getAppFiles('actions', array(
				'env' => $this->env,
				'appName' => $this->getAppName(),
				'appFile' => $action . '.php'
			), &$actionFiles);

		return $actionFiles;
	}

	public function getFunctionFiles(){
		global $appExtension;
		$functionFiles = array();
		$pageFunctionFile = $this->getAppPage() . '.php';

		if (file_exists($this->appDir['absolute'] . 'pages_functions/' . $pageFunctionFile)){
			$functionFiles[] = $this->appDir['absolute'] . 'pages_functions/' . $pageFunctionFile;
		}

		$appExtension->getAppFiles('pages_functions', array(
				'env' => $this->env,
				'appName' => $this->getAppName(),
				'appFile' => $pageFunctionFile
			), &$functionFiles);

		return $functionFiles;
	}

	public function checkModel($modelName, $extName = null){
		global $manager, $messageStack;
		$dbConn = $manager->getCurrentConnection();
		$tableObj = Doctrine_Core::getTable($modelName);

		$reportInfo = array();
		if (is_null($extName) === false){
			$reportInfo['Extension Name'] = $extName;
		}
		$reportInfo['Table Name'] = $tableObj->getTableName();

		if ($dbConn->import->tableExists($tableObj->getTableName())){
			$tableObjRecord = $tableObj->getRecordInstance();

			$DBtableColumns = $dbConn->import->listTableColumns($tableObj->getTableName());
			$tableColumns = array();
			foreach($DBtableColumns as $k => $v){
				$tableColumns[strtolower($k)] = $v;
			}
			$modelColumns = $tableObj->getColumns();
			foreach($modelColumns as $colName => $colSettings){
				if ($colName == 'id')
					continue;

				if (array_key_exists($colName, $tableColumns) === false){
					$resolutionLinkParams = 'action=fixMissingColumns';
					if (is_null($extName) === false){
						$resolutionLinkParams .= '&extName=' . $extName;
					}else{
						$resolutionLinkParams .= '&Model=' . $modelName;
					}

					$reportInfo['Column Name'] = $colName;
					$reportInfo['Resoultion'] = '<a href="' . itw_app_link($resolutionLinkParams, 'extensions', 'default') . '">Click here to resolve</a>';
					ExceptionManager::report('Database table column does not exist.', E_USER_ERROR, $reportInfo);
				}
			}
			unset($reportInfo['Resoultion']);

			/* foreach($tableColumns as $colName => $colSettings){
			  if (array_key_exists($colName, $modelColumns) === false){
			  $reportInfo['Column Name'] = $colName;
			  ExceptionManager::report('Database column does not exist in model.', E_USER_ERROR, $reportInfo);
			  }
			  } */
		}else{
			$resolutionLinkParams = 'action=fixMissingTables';
			$resolutionLinkParams .= '&Model=' . $modelName;
			if (is_null($extName) === false){
				$resolutionLinkParams .= '&extName=' . $extName;
			}
			$reportInfo['Resoultion'] = '<a href="' . itw_app_link($resolutionLinkParams, 'extensions', 'default') . '">Click here to resolve</a>';
			ExceptionManager::report('Database table does not exist.', E_USER_ERROR, $reportInfo);
		}
	}

	public function addMissingModelTable($modelName, $extName = null){
		global $manager, $messageStack;
		$dbConn = $manager->getCurrentConnection();

		if (is_null($extName) === false){
			$modelPath = sysConfig::getDirFsCatalog() . 'extensions/' . $extName . '/Doctrine/base/';
		}else{
			$modelPath = sysConfig::getDirFsCatalog() . 'ext/Doctrine/Models/';
		}

		Doctrine_Core::createTablesFromArray(array(
				$modelName
			));

		$tableObj = Doctrine_Core::getTable($modelName);
		if ($dbConn->import->tableExists($tableObj->getTableName())){
			$message = '<table>' .
				'<tr>' .
				'<td><b>Server Message:</b></td>' .
				'<td>Database table added.</td>' .
				'</tr>' .
				(is_null($extName) === false ? '<tr>' .
					'<td><b>Extension Key:</b></td>' .
					'<td>' . $extName . '</td>' .
					'</tr>' : '') .
				'<tr>' .
				'<td><b>Table Name:</b></td>' .
				'<td>' . $tableObj->getTableName() . '</td>' .
				'</tr>' .
				'</table>';
			$messageStack->addSession('pageStack', $message, 'success');
		}
	}

	public function addMissingModelColumns($modelName, $extName = null){
		global $manager, $messageStack;
		$dbConn = $manager->getCurrentConnection();

		$tableObj = Doctrine_Core::getTable($modelName);
		$tableObjRecord = $tableObj->getRecordInstance();

		$tableColumns = $dbConn->import->listTableColumns($tableObj->getTableName());
		$modelColumns = $tableObj->getColumns();

		foreach($modelColumns as $colName => $colSettings){
			if (array_key_exists($colName, $tableColumns) === false){
				$dbConn->export->alterTable($tableObj->getTableName(), array(
						'add' => array(
							$colName => (array) $colSettings
						)
					));

				$message = '<table>' .
					'<tr>' .
					'<td><b>Server Message:</b></td>' .
					'<td>Database table column added.</td>' .
					'</tr>' .
					(is_null($extName) === false ? '<tr>' .
						'<td><b>Extension Key:</b></td>' .
						'<td>' . $extName . '</td>' .
						'</tr>' : '') .
					'<tr>' .
					'<td><b>Table Name:</b></td>' .
					'<td>' . $tableObj->getTableName() . '</td>' .
					'</tr>' .
					'<tr>' .
					'<td><b>Column Name:</b></td>' .
					'<td>' . $colName . '</td>' .
					'</tr>' .
					'</table>';
				$messageStack->addSession('pageStack', $message, 'success');
			}
		}
	}
}
?>
