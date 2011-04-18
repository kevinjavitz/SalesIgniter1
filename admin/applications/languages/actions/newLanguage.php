<?php
	function updateProgressBar($name, $message){
		$Qcheck = tep_db_query('select name from progress_bar where name="' . $name . '"');
		if (tep_db_num_rows($Qcheck)){
			tep_db_query('update progress_bar set message = "' . $message . '" where name = "' . $name . '"');
		}else{
			tep_db_query('insert into progress_bar (message, name) values ("' . $message . '", "' . $name . '")');
		}
	}
	
	$progressBarName = 'newLanguage';
	
	/*
	 * Permission settings for new files
	 *
	 ** Folder Permissions Thoughts
	 *    I don't like setting to 0777 but that's the only way to insure that the user can add files via ftp.
	 *
	 ** File Permissions Thoughts
	 *    I don't like setting to 0777 but that's the only way to insure that the user can edit files via ftp.
	 */
	$folderPerm = 0777;
	$filePerm = 0777;
	
	$languages = sysLanguage::getGoogleLanguages();
	
	$langCode = $_POST['toLangCode'];
	$langName = $languages[$langCode];

	$catalogAbsPath = sysConfig::getDirFsCatalog();
	$newLangPath = $catalogAbsPath . 'includes/languages/' . strtolower($langName) . '/';
	$globalLangPath = $catalogAbsPath . 'includes/languages/english/';
	
	$exclude = array($catalogAbsPath . 'includes/languages');
	
	updateProgressBar($progressBarName, 'Compiling list of files to copy');
	/*
	 * Search all folders for global.xml files
	 * 
	 * @TODO: Update when application page specific language files are created
	 */
	$Directory = new RecursiveDirectoryIterator($catalogAbsPath);
	$Iterator = new RecursiveIteratorIterator($Directory);
	$Regex = new RegexIterator($Iterator, '/^.+global\.xml$/i', RegexIterator::GET_MATCH);
		
	$files = array();
	foreach($Regex as $arr){
		$skipFile = false;
		/*
		 * Exclude any files inside the folders specified in the exclude array
		 */
		foreach($exclude as $excludeDir){
			if (stristr($arr[0], $excludeDir)){
				$skipFile = true;
				break;
			}
		}
			
		if ($skipFile === false){
			$files[] = str_replace($catalogAbsPath, '', $arr[0]);
		}
	}
	
	updateProgressBar($progressBarName, 'Creating Directories');
	/*
	 * Create the new languages directory
	 */
	mkdir($newLangPath);
	
	/*
	 * Set the permissions to 0777 for the new languages directory
	 */
	chmod($newLangPath, 0777);
	
	foreach($files as $file){
		$fileName = basename($file);
		
		/*
		 * Standardize the file path
		 */
		$filePath = str_replace(array(
			$fileName,
			'language_defines/'
		), '', $file);
		
		/*
		 * Add catalog environment directory to the path, if the conditions are met ( only a catalog application )
		 *     1: Not an admin file
		 *     2: Not an extension file
		 *     3: Not an includes file ( infoboxes, orders modules, etc.. )
		 *     3: Not already a catalog file
		 */
		if (substr($filePath, 0, 5) != 'admin'){
			if (substr($filePath, 0, 10) != 'extensions'){
				if (substr($filePath, 0, 8) != 'includes'){
					if (substr($filePath, 0, 7) != 'catalog'){
						$filePath = 'catalog/' . $filePath;
					}
				}
			}
		}

		if (!is_dir($newLangPath . $filePath)){
			/*
			 * Separate the path on "/" so we can recursively create the directories if they do not exist
			 * ignores .xml files
			 */
			$dirs = explode('/', $filePath);
			$prevDir = '';
			foreach($dirs as $dirName){
				if (!empty($dirName) && !stristr($dirName, '.xml')){
					if (!is_dir($newLangPath . $prevDir . $dirName . '/')){
						mkdir($newLangPath . $prevDir . $dirName . '/');

						/*
						 * Set the permissions for the newly created folder
						 */
						chmod($newLangPath . $prevDir . $dirName, $folderPerm);
					}
					$prevDir .= $dirName . '/';
				}
			}
		}
		
		updateProgressBar($progressBarName, 'Copying File: ' . $newLangPath . $filePath . $fileName);
		/*
		 * Copy the fallback language file to the new languages directory
		 */
		copy(
			$catalogAbsPath . $file,
			$newLangPath . $filePath . $fileName
		);
		
		/*
		 * Set the permissions for the new file
		 *
		 * I don't like setting to 0777 but that's the only way to insure that the user can edit the file via ftp.
		 * There is code in the htaccess to prevent web access to the .xml files
		 */
		chmod($newLangPath . $filePath . $fileName, $filePerm);
		
		updateProgressBar($progressBarName, 'Translating File: ' . $newLangPath . $filePath . $fileName);
		/*
		 * Translate the new file to the selected language
		 */
		sysLanguage::translateFile($newLangPath . $filePath . $fileName, $langCode, $langName);
	}
	
	/*
	 * Copy the global file for the admin and set the permissions
	 */
	updateProgressBar($progressBarName, 'Copying File: ' . $newLangPath . 'admin/global.xml');
	copy(
		$globalLangPath . 'admin/global.xml',
		$newLangPath . 'admin/global.xml'
	);
	chmod($newLangPath . 'admin/global.xml', $filePerm);
	
	updateProgressBar($progressBarName, 'Translating File: ' . $newLangPath . 'admin/global.xml');
	sysLanguage::translateFile($newLangPath . 'admin/global.xml', $langCode, $langName);

	/*
	 * Copy the global file for the catalog and set the permissions
	 */
	updateProgressBar($progressBarName, 'Copying File: ' . $newLangPath . 'catalog/global.xml');
	copy(
		$globalLangPath . 'catalog/global.xml',
		$newLangPath . 'catalog/global.xml'
	);
	chmod($newLangPath . 'catalog/global.xml', $filePerm);
	
	updateProgressBar($progressBarName, 'Translating File: ' . $newLangPath . 'catalog/global.xml');
	sysLanguage::translateFile($newLangPath . 'catalog/global.xml', $langCode, $langName);

	$success = false;
	if (is_dir($newLangPath)){
		$success = true;
		
		updateProgressBar($progressBarName, 'Copying settings file and applying changes');
		$langData = simplexml_load_file(
			$globalLangPath . 'settings.xml',
			'SimpleXMLExtended'
		);
		
		$langData->name->setCData($langName);
		$langData->code->setCData($langCode);
		$langData->html_params->setCData('dir=ltr lang=' . $langCode);
		
		$fileObj = fopen($newLangPath . 'settings.xml', 'w+');
		if ($fileObj){
			ftruncate($fileObj, -1);
			fwrite($fileObj, $langData->asXML());
			fclose($fileObj);
			chmod($newLangPath . 'settings.xml', $filePerm);
		}
		
		$newLang = new Languages();
		$newLang->code = $langCode;
		$newLang->name = $langName;
		$newLang->directory = strtolower($langName);
		
		$newLang->save();
		
		$Translated = sysLanguage::translateText($langName, $newLang->languages_id);
		$newLang->name_real = $Translated[0];
		$newLang->save();
		
		if (isset($_POST['translate_model'])){
 			foreach($_POST['translate_model'] as $modelName){
 				$Model = Doctrine_Core::getTable($modelName);
 				$RecordInst = $Model->getRecordInstance();
 				if (method_exists($RecordInst, 'newLanguageProcess')){
					updateProgressBar($progressBarName, 'Translating Description Table: ' . $modelName);
 					$RecordInst->newLanguageProcess('1', $newLang->languages_id);
 				}
			}
		}
	}

	EventManager::attachActionResponse(array(
		'success'  => $success,
		'langCode' => $langCode,
		'langDir'  => $newLangPath
	), 'json');
?>