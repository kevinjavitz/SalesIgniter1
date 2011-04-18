<?php
	function filterSearchFiles($dir, &$matches, $exclude = null){
		global $searchFor;
		
		$Directory = new RecursiveDirectoryIterator($dir);
		$Iterator = new RecursiveIteratorIterator($Directory);
		$Regex = new RegexIterator($Iterator, '/^.+global\.xml$/i', RegexIterator::GET_MATCH);
		
		$extPaths = array();
		foreach($Regex as $file){
			$skipFile = false;
			if (is_null($exclude) === false){
				foreach($exclude as $excludeDir){
					if (stristr($file[0], $excludeDir)){
						$skipFile = true;
						break;
					}
				}
			}
			
			if ($skipFile === false){
				$filePath = str_replace(sysConfig::getDirFsCatalog(), '', $file[0]);
		
				$langData = simplexml_load_file(
					$file[0],
					'SimpleXMLElement',
					LIBXML_NOCDATA
				);
				foreach($langData->define as $langDefine){
					$searchIn = (string) $langDefine[0];
					$langKey = (string) $langDefine['key'];
			
					if (stristr($searchIn, $searchFor)){
						if (!isset($matches[$filePath])){
							$matches[$filePath] = array(
								'total' => 0,
								'strings' => array()
							);
						}
				
						$matches[$filePath]['total']++;
						$matches[$filePath]['strings'][] = array(
							'text' => htmlspecialchars($searchIn),
							'key' => $langKey
						);
					}
				}
			}
		}
	}
	
	$searchFor = urldecode($_GET['searchFor']);
	$filterFiles = $_GET['filter_files'];
	$filterLanguages = (isset($_GET['filter_lang']) ? $_GET['filter_lang'] : null);
	
	$matches = array();
	if ($filterFiles == 'user'){
		if (is_null($filterLanguages) === true){
			foreach(sysLanguage::getLanguages() as $lInfo){
				filterSearchFiles(
					sysConfig::getDirFsCatalog() . 'includes/languages/' . $lInfo['directory'] . '/',
					$matches/*,
					array(
						sysConfig::getDirFsCatalog() . 'includes/languages/' . $lInfo['id'] . '/admin/'
					)*/
				);
			}
		}else{
			foreach($filterLanguages as $lang){
				filterSearchFiles(
					sysConfig::getDirFsCatalog() . 'includes/languages/' . $lang . '/',
					$matches/*,
					array(
						sysConfig::getDirFsCatalog() . 'includes/languages/' . $lang . '/admin/'
					)*/
				);
			}
		}
	}elseif ($filterFiles == 'all'){
		filterSearchFiles(
			sysConfig::getDirFsCatalog(),
			$matches,
			array(
				sysConfig::getDirFsCatalog() . 'includes/languages_phar/'
			)
		);
	}else{
		filterSearchFiles(
			sysConfig::getDirFsCatalog(),
			$matches,
			array(
				sysConfig::getDirFsCatalog() . 'includes/languages/',
				sysConfig::getDirFsCatalog() . 'includes/languages_phar/'
			)
		);
	}
	
	$jsonArray = array();
	foreach($matches as $filePath => $info){
		$jsonArray[] = array(
			'path' => $filePath,
			'total' => $info['total'],
			'strings' => $info['strings']
		);
	}
	
	EventManager::attachActionResponse(array(
		'success' => true,
		'matches' => $jsonArray
	), 'json');
?>