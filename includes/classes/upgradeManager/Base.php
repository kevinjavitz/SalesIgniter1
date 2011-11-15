<?php
if (!class_exists('CurlDownload')){
	require(sysConfig::getDirFsCatalog() . '/includes/classes/curl/Download.php');
}

class UpgradeManager {

	/**
	 * @var string Upgrade server domain
	 */
	private $domain;

	/**
	 * @var float Current version of the framework
	 */
	private $oldVersion;

	/**
	 * @var float New version to upgrade to
	 */
	private $newVersion;

	/**
	 * @var array|mixed Array of ignored files
	 */
	private $ignoreFiles = array();

	/**
	 * Class constructor
	 */
	public function __construct($newVersion, $upgradeDir = ''){
		$this->domain = sysConfig::get('HTTP_HOST');
		$this->upgradeServer = 'https://' . sysConfig::get('SYSTEM_UPGRADE_SERVER') . '/';
		$this->upgradeUsername = sysConfig::get('SYSTEM_UPGRADE_USERNAME');
		$this->upgradePassword = sysConfig::get('SYSTEM_UPGRADE_PASSWORD');

		$this->oldVersion = sysConfig::get('SYSTEM_VERSION');
		$this->newVersion = $newVersion;

		$this->rootPath = sysConfig::getDirFsCatalog();
		$this->tempFolder = 'temp/';
		$this->upgradeFolder = 'upgraded/';
		$this->versionFolder = $this->oldVersion . '-' . $this->newVersion . '/';
		
		if (!empty($upgradeDir)){
			$this->dateFolder = $upgradeDir;
		}else{
			$this->dateFolder = date('Ymdhis') . '/';
		}

		$this->zipFileName = 'update.zip';
		$this->zipFileDownloadPath = $this->rootPath . $this->tempFolder . $this->versionFolder . $this->dateFolder;
		$this->zipFileExtractPath = $this->rootPath . $this->upgradeFolder . $this->versionFolder . $this->dateFolder;

		$this->databaseFileName = 'update.sql';
		$this->databaseFileDownloadPath = $this->rootPath . $this->tempFolder . $this->versionFolder . $this->dateFolder;

		$this->globalProgressBarName = 'upgradeCheckGlobal';
		$this->currentProgressBarName = 'upgradeCheckProcess';
		$this->dirPerms = 0755;

		$this->useProgressBar = true;
		if ($this->useProgressBar === true){
			if (empty($upgradeDir)){
				$this->clearProgressBar();
				mysql_query('insert into progress_bar (name, message, percentage) values ("' . $this->globalProgressBarName . '", "Global Process Progress Bar", "0")');
				mysql_query('insert into progress_bar (name, message, percentage) values ("' . $this->currentProgressBarName . '", "Per Process Progress Bar", "0")');
			}
		}

		$checkDirs = array(
			$this->tempFolder,
			$this->tempFolder . $this->versionFolder,
			$this->tempFolder . $this->versionFolder . $this->dateFolder,
			$this->upgradeFolder,
			$this->upgradeFolder . $this->versionFolder,
			$this->upgradeFolder . $this->versionFolder . $this->dateFolder,
		);
		foreach($checkDirs as $dir){
			if (!is_dir($this->rootPath . $dir)){
				mkdir($this->rootPath . $dir, $this->dirPerms);
			}
		}

		if (file_exists($this->rootPath . '.upgrade_ignore')){
			$Ignored = new SplFileObject($this->rootPath . '.upgrade_ignore');
			while($Ignored->valid()){
				$Line = $Ignored->current();
				if (!empty($Line) && substr($Line, 1) != '#'){
					$this->ignoreFiles[] = str_replace("\n", '', trim($Line));
				}
				$Ignored->next();
			}
		}
		//ignore_user_abort(TRUE);
		set_time_limit(60 * 7);
	}
	
	public function getUpgradeDir(){
		return $this->dateFolder;
	}

	public function getUpgradePath(){
		return $this->zipFileExtractPath;
	}

	public function getRootPath(){
		return $this->rootPath;
	}

	/**
	 * Download the upgrade files zip
	 * @return void
	 */
	public function getUpgradeZip(){
		$this->updateGlobalProgressBar(0);
		$this->updateProgressBar('Downloading Upgrades', 0);

		$Download = new CurlDownload(
			$this->upgradeServer . 'sesUpgrades/codeUpgrade.php',
			$this->upgradeUsername,
			$this->upgradePassword
		);
		$Download->setRequestData(array(
			'domain' => $this->domain,
			'version' => $this->newVersion,
			'action' => 'process'
		));
		$Download->setAuthMethod('post');
		$Download->setLocalFolder($this->zipFileDownloadPath);
		$Download->setLocalFileName($this->zipFileName);
		$Download->useProgressBar(true);
		$Download->setProgressBarName($this->currentProgressBarName);
		$Download->download();
		$this->updateGlobalProgressBar(.1);
	}

	/**
	 * Download the upgrade database file
	 * @return void
	 */
	public function getUpgradeDatabase(){
	}

	/**
	 * Unzip the upgrade zip to the temp folder
	 * @return void
	 */
	public function unzip(){
		$this->updateGlobalProgressBar(.1);
		$this->updateProgressBar('Unpacking Upgrades To Temp Directory', 0);
		$NewCode = new ZipArchive();
		if ($NewCode->open($this->zipFileDownloadPath . $this->zipFileName) !== true){
			die('Error Opening Zip: ' . $this->zipFileDownloadPath . $this->zipFileName);
		}
		else{
			$numOfFiles = $NewCode->numFiles;
			for($i = 0; $i < $numOfFiles; $i++){
				$this->updateProgressBar(
					'Unpacking: ' . $NewCode->getNameIndex($i),
					number_format((($i+1) / $numOfFiles), 2)
				);
				$this->updateGlobalProgressBar(
					number_format((10 + (20 * (($i+1) / $numOfFiles))) / 100, 2)
				);
				if ($NewCode->extractTo($this->zipFileExtractPath, array($NewCode->getNameIndex($i)))){
					continue;
				}else{
					die('Error Extracting File: ' . $NewCode->getNameIndex($i) . ' To ' . $this->zipFileExtractPath);
				}

			}
			$NewCode->close();
		}
		$this->updateGlobalProgressBar(.3);
	}

	/**
	 * Compare the current file against the new upgrade file
	 * @param string $oldPath Current File
	 * @param string $newPath Upgrade File
	 * @param array $Report Report Array
	 * @return void
	 */
	private function compareFiles($oldPath, $newPath){
		$cleanPath = str_replace($this->rootPath, '', $oldPath);
		
		$Report = false;
		if (sprintf("%u", filesize($oldPath)) != sprintf("%u", filesize($newPath))){
			$oldFile = fopen($oldPath, 'r');
			$newFile = fopen($newPath, 'r');
		
			$readBlockSize = 256;
			while(!feof($newFile)){
				$newBlock = fread($newFile, $readBlockSize);
				$oldBlock = fread($oldFile, $readBlockSize);
			
				if ($oldBlock != $newBlock){
					$Report = array(
						'file' => $cleanPath,
						'message' => 'Has Modifications On One Side',
						'checked' => true,
						'hasDiff' => true
					);
					break;
				}
			}
		
			fclose($oldFile);
			fclose($newFile);
			unset($newBlock);
			unset($oldBlock);
			gc_collect_cycles();
		}
		
		return $Report;
	}

	/**
	 * Run the compare operation to compare all current files against all upgrade files
	 * @param array $Report Report Array
	 * @return void
	 */
	public function compareCode(array &$Report){
		$this->updateProgressBar('Determining New Files And Files To Upgrade', 0);
		$Common = array();
		$RootMissing = array();
		$UpgradeNew = array();

		$UpgradeDir = new RecursiveDirectoryIterator($this->zipFileExtractPath);
		$UpgradeFiles = new RecursiveIteratorIterator($UpgradeDir, RecursiveIteratorIterator::SELF_FIRST);
		foreach($UpgradeFiles as $File){
			if ($File->isDir()){
				continue;
			}
			$filePath = str_replace($this->zipFileExtractPath, '', $File->getPathname());
			$process = true;
			foreach($this->ignoreFiles as $path){
				if (substr($path, 0, 1) == '*' && basename($filePath) == basename($path)){
					$process = false;
				}elseif (substr($filePath, 0, strlen($path)) == $path){
					$process = false;
				}
			}
			if ($process === true){
				$UpgradeNew[$filePath] = $filePath;
			}
		}

		$RootDir = new RecursiveDirectoryIterator($this->rootPath);
		$RootFiles = new RecursiveIteratorIterator($RootDir, RecursiveIteratorIterator::SELF_FIRST);
		foreach($RootFiles as $File){
			if ($File->isDir()){
				continue;
			}
			$filePath = str_replace($this->rootPath, '', $File->getPathname());
			$process = true;
			foreach($this->ignoreFiles as $path){
				if (substr($path, 0, 1) == '*' && basename($filePath) == basename($path)){
					$process = false;
				}elseif (substr($filePath, 0, strlen($path)) == $path){
					$process = false;
				}
			}
			if ($process === true){
				if (!isset($UpgradeNew[$filePath])){
					$RootMissing[$filePath] = $filePath;
				}
				else{
					$Common[$filePath] = $filePath;
					unset($UpgradeNew[$filePath]);
				}
			}
		}
		gc_collect_cycles();

		$this->updateProgressBar('Determining New Files And Files To Upgrade', 1);
		foreach($UpgradeNew as $filePath){
			$Report[] = array(
				'file' => $filePath,
				'message' => 'New File From Upgrade',
				'checked' => true,
				'hasDiff' => false
			);
		}
		$UpgradeNew = null;
		unset($UpgradeNew);

		foreach($RootMissing as $filePath){
			$Report[] = array(
				'file' => $filePath,
				'message' => 'Old Or Custom File',
				'checked' => true,
				'hasDiff' => false
			);
		}
		$RootMissing = null;
		unset($RootMissing);

		gc_collect_cycles();

		$numOfFiles = sizeof($Common);
		$fileCount = 0;
		foreach($Common as $idx => $filePath){
			$curFilePath = $this->rootPath . $filePath;
			$zipFilePath = $this->zipFileExtractPath . $filePath;
			$fileCount++;
			$this->updateProgressBar(
				'Comparing File<br><br>' . $filePath,
				($fileCount / $numOfFiles)
			);
			$this->updateGlobalProgressBar(
				number_format((30 + (30 * ($fileCount / $numOfFiles))) / 100, 2)
			);

			if (file_exists($zipFilePath) && file_exists($curFilePath)){
				$Result = $this->compareFiles($curFilePath, $zipFilePath, &$Report);
				if ($Result !== false){
					$Report[] = $Result;
				}
			}
			else{
				$Report[] = array(
					'file' => $filePath,
					'message' => 'ERROR: Non-Existant File Slipped Through<br><br>Root Path: ' . $curFilePath . '<br>Upgrade Path: ' . $zipFilePath . '<br><br>',
					'checked' => true,
					'hasDiff' => false
				);
			}
			
			unset($Common[$idx]);
		}
		gc_collect_cycles();
		clearstatcache();
	}
	
	public function upgradeFiles($rootPath, $upgradePath){
		$Upgrade = new UpgradeManagerCompareFile(
			$rootPath,
			$upgradePath
		);
		$numOfFiles = sizeof($_POST['files']);
		for($i=0; $i<$numOfFiles; $i++){
			$filePath = $_POST['files'][$i];
			
			$this->updateProgressBar('Upgrading File ( ' . $i . ' of ' . $numOfFiles . ' )<br><br>' . $filePath, 0);
			$Upgrade->compare($filePath);
			$this->updateProgressBar('Upgrading File ( ' . $i . ' of ' . $numOfFiles . ' )<br><br>' . $filePath, 1);
			$this->updateGlobalProgressBar(
				number_format((60 + (30 * (($i+1) / $numOfFiles))) / 100, 2)
			);
			
			unset($_POST['files'][$i]);
		}
		
		$this->updateProgressBar('Removing Empty Directories', 0);
		$RootDir = new RecursiveDirectoryIterator($rootPath);
		$RootFiles = new RecursiveIteratorIterator($RootDir, RecursiveIteratorIterator::SELF_FIRST);
		$dirs = array();
		foreach($RootFiles as $File){
			if ($File->getBasename() == '.' || $File->getBasename() == '..' || $File->isFile()) continue;
			if ($File->isDir()){
				$dirs[] = $File->getPathname();
			}
		}
		rsort($dirs);
		
		foreach($dirs as $path){
			$scanCheck = scandir($path);
			if ($scanCheck && (count($scanCheck) == 2)){
				if (
					$path == sysConfig::getDirFsCatalog() || 
					($path . '/') == sysConfig::getDirFsCatalog()
				){
					die('DONT DELETE THE ROOT, WHAT IS YOUR PROBLEM!!!!!');
				}
				rmdir($path);
			}
		}
		$this->updateProgressBar('Removing Empty Directories', 1);
	}

	public function upgradeDatabase($options){
		$Database = new UpgradeDatabase($_POST['version']);
		if (in_array('createTables', $options)){
			$this->updateProgressBar('Creating New Tables', 0);
			$Database->createTables();
			$this->updateProgressBar('Creating New Tables', 1);
		}
		
		if (in_array('addColumns', $options)){
			$this->updateProgressBar('Adding New Columns', 0);
			$Database->addColumns();
			$this->updateProgressBar('Adding New Columns', 1);
		}
		
		if (in_array('updateData', $options)){
			$this->updateProgressBar('Updating Table Data', 0);
			$Database->updateData();
			$this->updateProgressBar('Updating Table Data', 1);
		}
		
		if (in_array('removeColumns', $options)){
			$this->updateProgressBar('Removing Columns', 0);
			$Database->removeColumns();
			$this->updateProgressBar('Removing Columns', 1);
		}
		
		if (in_array('removeTables', $options)){
			$this->updateProgressBar('Removing Tables', 0);
			$Database->removeTables();
			$this->updateProgressBar('Removing Tables', 1);
		}
		$this->updateGlobalProgressBar(.97);
	}
	
	public function finish(){
		$this->updateGlobalProgressBar(1);
	}

	/**
	 * Compare the current database against the upgrade database
	 * @return void
	 */
	public function compareDatabase(){
	}

	/**
	 * Add a progress bar entry for the current process
	 * @param string $message
	 * @param float $percent
	 * @return void
	 */
	private function updateProgressBar($message, $percent){
		if ($this->useProgressBar === true){
			mysql_query('update progress_bar set message = "' . $message . '", percentage = "' . ($percent * 100) . '" where name = "' . $this->currentProgressBarName . '"');
		}
	}

	/**
	 * Add a progress bar entry for the global process
	 * @param string $message
	 * @param float $percent
	 * @return void
	 */
	public function updateGlobalProgressBar($percent){
		if ($this->useProgressBar === true){
			mysql_query('update progress_bar set message = "Global Process Progress", percentage = "' . ($percent * 100) . '" where name = "' . $this->globalProgressBarName . '"');
		}
	}

	/**
	 * Remove all progress bar entries related to the upgrade
	 * @return void
	 */
	private function clearProgressBar(){
		if ($this->useProgressBar === true){
			Doctrine_Query::create()
			->delete('ProgressBar')
			->whereIn('name', array($this->globalProgressBarName, $this->currentProgressBarName))
			->execute();
		}
	}
}

?>