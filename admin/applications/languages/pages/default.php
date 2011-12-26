<?php
/*
 * Sales Igniter E-Commerce System
 * Version: 2.0
 *
 * I.T. Web Experts
 * http://www.itwebexperts.com
 *
 * Copyright (c) 2011 I.T. Web Experts
 *
 * This script and its source are not distributable without the written conscent of I.T. Web Experts
 */
?>
<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE');
?></div>
<br />
<?php
	$langArr = array();
	$Languages = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'includes/languages/');
	foreach($Languages as $langDir){
		if ($langDir->isDot() || $langDir->isFile()) continue;
		$dirName = $langDir->getBasename();
		
		$langSettings = simplexml_load_file(
			$langDir->getPathname() . '/settings.xml',
			'SimpleXMLElement',
			LIBXML_NOCDATA
		);
		
		$langArr[$dirName] = array(
			'name'      => (string) $langSettings->name,
			'code'      => (string) $langSettings->code,
			'directory' => str_replace('\\', '/', $langDir->getPathname()),
			'installed' => false,
			'forced_default' => 0
		);
		
		$Qcheck = Doctrine_Query::create()
		->select('languages_id, forced_default')
		->from('Languages')
		->where('code = ?', (string) $langSettings->code)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Qcheck){
			$langArr[$dirName]['installed'] = true;
			$langArr[$dirName]['id'] = $Qcheck[0]['languages_id'];
			$langArr[$dirName]['forced_default'] = $Qcheck[0]['forced_default'];
		}else{
			unset($langArr[$dirName]);
		}
	}
	ksort($langArr);
	
	$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(false);

	$tableGrid->addButtons(array(
		htmlBase::newElement('button')->setText('Edit')->addClass('editButton')->disable(),
		htmlBase::newElement('button')->setText('Delete')->addClass('deleteButton')->disable(),
		htmlBase::newElement('button')->setText('Definitions')->addClass('defineButton')->disable(),
		htmlBase::newElement('button')->setText('Create New')->addClass('newLanguageButton')/*,
		htmlBase::newElement('button')->setText('Clean')->addClass('cleanButton')*/
	));
	
	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_LANGUAGE_NAME')),
			array('text' => sysLanguage::get('TABLE_HEADING_LANGUAGE_CODE')),
			array('text' => 'Forced Default'),
			array('text' => 'Info')
		)
	));
	
	if ($langArr){
		foreach($langArr as $lInfo){
			$languageId = $lInfo['id'];
			if (DEFAULT_LANGUAGE == $lInfo['code']){
				$gridShowName = '<b>' . $lInfo['name'] . '</b> (' . sysLanguage::get('TEXT_DEFAULT') . ')';
			}else{
				$gridShowName = $lInfo['name'];
			}
			
			$tableGrid->addBodyRow(array(
				'rowAttr' => array(
					'data-language_id' => $languageId,
					'data-language_dir' => $lInfo['directory'],
					'data-language_code' => $lInfo['code'],
					'data-is_installed' => ($lInfo['installed'] === true ? 'true' : 'false'),
					'data-is_default' => (DEFAULT_LANGUAGE == $lInfo['code'] ? 'true' : 'false')
				),
				'columns' => array(
					array('text' => $gridShowName),
					array('text' => $lInfo['code']),
					array('text' => '<a href="' . itw_app_link('action=forceDefault&lID=' . $languageId . '&force=' . ($lInfo['forced_default'] == '1' ? '0' : '1'), 'languages', 'default') . '"><span class="ui-icon ui-icon-circle-' . ($lInfo['forced_default'] == '1' ? 'check' : 'close') . ' forceDefault"></span></a>', 'align' => 'center'),
					array('text' => htmlBase::newElement('icon')->setType('info')->draw(), 'align' => 'center')
				)
			));
			
			$tableGrid->addBodyRow(array(
				'addCls' => 'gridInfoRow',
				'columns' => array(
					array('colspan' => 6, 'text' => '<table cellpadding="1" cellspacing="0" border="0" width="75%">' . 
						'<tr>' . 
							'<td><b>' . sysLanguage::get('TEXT_INFO_LANGUAGE_NAME') . '</b></td>' . 
							'<td> ' . $lInfo['name'] . '</td>' . 
							'<td><b>' . sysLanguage::get('TEXT_INFO_LANGUAGE_CODE') . '</b></td>' . 
							'<td>' . $lInfo['code'] . '</td>' .
						'</tr>' . 
						'<tr>' . 
							'<td><b>' . sysLanguage::get('TEXT_INFO_LANGUAGE_DIRECTORY') . '</b></td>' . 
							'<td>'  . $lInfo['directory'] . '</td>' . 
						'</tr>' . 
					'</table>')
				)
			));
		}
	}
?>
 <div style="width:100%;float:left;">
  <div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
   <div style="width:99%;margin:5px;"><?php echo $tableGrid->draw();?></div>
  </div>
 </div>
 <div class="dialogToLangCode" title="Choose Language" style="display:none;"><?php
 	$dbConn = $manager->getCurrentConnection();
 	$importer = $dbConn->import;
 	$Tables = $importer->listTables();
 	$langTables = array();
 	foreach($Tables as $tableName){
 		if ($tableName == 'languages') continue;
 		
 		$TableColumns = $importer->listTableColumns($tableName);
 		foreach($TableColumns as $columnName => $cInfo){
 			if ($columnName == 'language_id' || $columnName == 'languages_id'){
 				$langTables[] = $tableName;
 			}
 		}
 	}
 	
 	$loadedModels = Doctrine_Core::getLoadedModelFiles();
 	foreach($loadedModels as $modelName => $modelPath){
 		$Model = Doctrine_Core::getTable($modelName);
 		$RecordInst = $Model->getRecordInstance();
 		if (method_exists($RecordInst, 'newLanguageProcess')){
 			$modelPath = str_replace(sysConfig::getDirFsCatalog(), '', $modelPath);
 			$extName = null;
 			if (substr($modelPath, 0, 10) == 'extensions'){
 				$pathArr = explode('/', $modelPath);
  				$ext = $appExtension->getExtension($pathArr[1]);
  				if ($ext){
					$extName = $ext->getExtensionName();
  				}else{
  					$extName = $pathArr[1];
  				}
 			}
 			$langModels[] = array(
 				'modelPath' => str_replace(sysConfig::getDirFsCatalog(), '', $modelPath),
 				'modelName' => $modelName,
 				'extName' => $extName,
 				'tableName' => $Model->getTableName()
 			);
 		}
 	}
 	
 	$translateList = '<br><br><input type="checkbox" class="selectAll"><b><u>Select Extra Tanslations</u></b><br>';
 	foreach($langModels as $mInfo){
 		$showName = $mInfo['modelName'];
 		if (is_null($mInfo['extName']) === false){
 			$showName = $mInfo['extName'];
 		}
 		$translateList .= '<input type="checkbox" name="translate_model[]" value="' . $mInfo['modelName'] . '"> ' . $showName . ' ( ' . $mInfo['tableName'] . ' )<br>';
 	}
 	$translateList .= '<br>';
 	
 	$dropMenu = htmlBase::newElement('selectbox')->setName('toLanguage');
 	foreach($googleLanguages as $langCode => $langName){
 		$dropMenu->addOption($langCode, $langName);
 	}
 	echo $dropMenu->draw() . '<br><small>* This may take a few minutes.</small>';
 	echo $translateList;
 ?></div>