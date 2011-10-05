<?php

	$module_type = 'pdfinfoboxes';
	$module_directory = sysConfig::getDirFsCatalog() . 'includes/modules/pdfinfoboxes/';
	$module_key = 'MODULE_PDF_INFOBOXES_INSTALLED';
	$ext_module_directory = sysConfig::getDirFsCatalog() . 'extensions/';
		if (isset($_GET['extName'])){
			$moduleDirRel = 'extensions/' . $_GET['extName'] . '/catalog/pdfinfoboxes/' . $_GET['module'] . '/';
		}else{
			$moduleDirRel = 'includes/modules/pdfinfoboxes/' . $_GET['module'] . '/';
		}
		$moduleDir = sysConfig::getDirFsCatalog() . $moduleDirRel;
		if (is_dir($moduleDir . 'Doctrine/base/')){
			Doctrine_Core::createTablesFromModels($moduleDir . 'Doctrine/base/');
		}
		/*copy(
			$moduleDir . 'language_defines/global.xml',
			sysConfig::getDirFsCatalog() . 'includes/languages_phar/english/infoboxes/' . $_GET['module'] . '/global.xml'
		);*/
			
		$className = 'PDFInfoBox' . ucfirst($_GET['module']);
		require($moduleDir . 'pdfinfobox.php');
		$class = new $className;
			
		$Infobox = new PDFTemplatesInfoboxes();
		$Infobox->box_code = $class->getBoxCode();
		$Infobox->box_path = $moduleDirRel;
		if (isset($_GET['extName'])){
			$Infobox->ext_name = $_GET['extName'];
		}
		$Infobox->save();
	
	if (isset($_GET['rType']) && $_GET['rType'] == 'ajax'){
		EventManager::attachActionResponse(array(
			'success' => true
		), 'json');
	}else{
		EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action'))), 'redirect');
	}
?>