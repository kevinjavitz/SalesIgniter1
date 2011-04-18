<?php
	/*
	 * Set the main path to the user defined file, edits are never saved to the core files.
	 */
	$absPath = sysConfig::getDirFsCatalog();
	$userFilePath = $_GET['filePath'];
	$coreDefinitions = (stristr($userFilePath, 'language_defines') !== false);
	if ($coreDefinitions === true){
		$relPath = 'includes/languages/english/';
	}else{
		$relPath = '';
	}
	
	/*
	 * Standardize the posted paths
	 *
	 * when posted as a user define file the path will be
	 *     includes/languages/english/admin/applications/login/global.xml
	 *
	 * When posted as a core define file the path will be
	 *     admin/applications/login/language_defines/global.xml
	 *
	 * This makes the path for both into
	 *     admin/applications/login/global.xml
	 */
	if ($coreDefinitions === true){
		$userFilePath = str_replace(array($relPath, 'language_defines/'), '', $userFilePath);
	}
	
	/*
	 * Add catalog environment directory to the path, if the conditions are met ( only a catalog application )
	 *     1: Not an admin file
	 *     2: Not an extension file
	 *     3: Not an includes file ( infoboxes, orders modules, etc.. )
	 *     3: Not already a catalog file
	 */
	if ($coreDefinitions === true){
		if (substr($userFilePath, 0, 5) != 'admin'){
			if (substr($userFilePath, 0, 10) != 'extensions'){
				if (substr($userFilePath, 0, 8) != 'includes'){
					if (substr($userFilePath, 0, 7) != 'catalog'){
						$userFilePath = 'catalog/' . $userFilePath;
					}
				}
			}
		}
	}
		
	/*
	 * Separate the path on "/" so we can recursively create the directories if they do not exist
	 * ignores .xml files
	 */
	$dirs = explode('/', $userFilePath);
	$prevDir = '';
	foreach($dirs as $dirName){
		if (!stristr($dirName, '.xml')){
			if (!is_dir($absPath . $relPath . $prevDir . $dirName)){
				mkdir($absPath . $relPath . $prevDir . $dirName);

				/*
				 * Set the permissions for the newly created folder
				 *
				 * I don't like setting to 0777 but that's the only way to insure that the user can add files via ftp.
				 * tried using mkdir's permission setting and it wouldn't take, so chmod had to be put in
				 */
				chmod($absPath . $relPath . $prevDir . $dirName, 0777);
			}
			$prevDir .= $dirName . '/';
		}
	}
	
	/*
	 * Join the paths for the load
	 */
	$filePath = $absPath . $relPath . $userFilePath;
	
	/*
	 * Check if the file exists
	 * if it doesn't then load an initial string with only the root element - Must be <definitions></definitions>
	 * if it does then load it
	 */
	if (!file_exists($filePath)){
		$langData = simplexml_load_string(
			'<definitions></definitions>',
			'SimpleXMLExtended'
		);
	}else{
		$langData = simplexml_load_file(
			$filePath,
			'SimpleXMLExtended'
		);
	}
	
	/*
	 * Process the posted text
	 */
	foreach($_POST['text'] as $defineKey => $defineText){
		/*
		 * Check if the define key already exists in this file
		 * if it does then use it
		 * if it doesn't then create a node for it, set the attribute, and set the initial value to null
		 */
		$el = $langData->definitions->xpath('//definitions/define[@key="' . $defineKey . '"]');
		if (!$el){
			$defineEl = $langData->addChild('define');
			$defineEl->addAttribute('key', $defineKey);
			$defineEl->addChild('define', 'null');
		}else{
			$defineEl = $el[0];
		}
		
		/*
		 * Add the text to a CDATA section within the element
		 */
		$defineEl->setCData($defineText);
	}
	
	/*
	 * Check if the file exists to determine if we need to set the permissions on the file to 0777
	 */
	$setPerms = false;
	if (!file_exists($filePath)){
		$setPerms = true;
	}
	
	/*
	 * Open the file ( attempt to create if it doesn't exist )
	 * Truncate the file and set the pointer to the beginning
	 * Add the xml string using our custom formatter ( see sysLanguage class file )
	 */
	$fileObj = fopen($filePath, 'w');
	if ($fileObj){
		ftruncate($fileObj, -1);
		fwrite($fileObj, $langData->asPrettyXML());
		fclose($fileObj);
		
		/*
		 * Set the permissions if the file did not initially exist
		 *
		 * I don't like setting to 0777 but that's the only way to insure that the user can edit the file via ftp.
		 * There is code in the htaccess to prevent web access to the .xml files
		 */
		if ($setPerms === true){
			chmod($filePath, 0777);
		}
	}
	
	EventManager::attachActionResponse(array(
		'success'  => true
	), 'json');
?>