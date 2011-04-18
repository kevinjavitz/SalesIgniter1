<?php
	if ($App->getPageName() == 'infoboxes'){
		if (isset($_GET['extName'])){
			$moduleDirRel = 'extensions/' . $_GET['extName'] . '/catalog/infoboxes/' . $_GET['module'] . '/';
		}else{
			$moduleDirRel = 'includes/modules/infoboxes/' . $_GET['module'] . '/';
		}
		$moduleDir = sysConfig::getDirFsCatalog() . $moduleDirRel;
		if (is_dir($moduleDir . 'Doctrine/base/')){
			Doctrine_Core::createTablesFromModels($moduleDir . 'Doctrine/base/');
		}
		
		/*copy(
			$moduleDir . 'language_defines/global.xml',
			sysConfig::getDirFsCatalog() . 'includes/languages_phar/english/infoboxes/' . $_GET['module'] . '/global.xml'
		);*/
			
		$className = 'InfoBox' . ucfirst($_GET['module']);
		require($moduleDir . 'infobox.php');
		$class = new $className;
			
		$Infobox = new TemplatesInfoboxes();
		$Infobox->box_code = $class->getBoxCode();
		$Infobox->box_path = $moduleDirRel;
		if (isset($_GET['extName'])){
			$Infobox->ext_name = $_GET['extName'];
		}
		$Infobox->save();
	}elseif ($App->getPageName() == 'orderTotal'){
		require(sysConfig::getDirFsCatalog() . 'includes/modules/orderTotalModules/install.php');
		$Install = new OrderTotalInstaller($_GET['module'], (isset($_GET['extName']) ? $_GET['extName'] : null));
		$Install->install();
	}elseif ($App->getPageName() == 'orderPayment'){
		require(sysConfig::getDirFsCatalog() . 'includes/modules/orderPaymentModules/install.php');
		$Install = new OrderPaymentInstaller($_GET['module'], (isset($_GET['extName']) ? $_GET['extName'] : null));
		$Install->install();
	}elseif ($App->getPageName() == 'orderShipping'){
		require(sysConfig::getDirFsCatalog() . 'includes/modules/orderShippingModules/install.php');
		$Install = new OrderShippingInstaller($_GET['module'], (isset($_GET['extName']) ? $_GET['extName'] : null));
		$Install->install();
	}
	
	if (isset($_GET['rType']) && $_GET['rType'] == 'ajax'){
		EventManager::attachActionResponse(array(
			'success' => true
		), 'json');
	}else{
		EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action'))), 'redirect');
	}
?>