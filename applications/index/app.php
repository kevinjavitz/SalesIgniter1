<?php

$category_depth = 'top';
	if(isset($_GET['appPage']) && !empty($_GET['appPage']) || isset($_GET['actualPage']) && !empty($_GET['actualPage'])){
		if(isset($_GET['actualPage'])){
			$catSeoUrl = $_GET['actualPage'];
		}else{
			$catSeoUrl = $_GET['appPage'];
		}
		$ResultSet = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc('select categories_id from categories_description where categories_seo_url = "' . $catSeoUrl .'" ');

		if (sizeof($ResultSet) > 0){
			$current_category_id = $ResultSet[0]['categories_id'];
			Session::set('current_category_id', $current_category_id);
		}
		/*if(isset($_GET['cPath']) && !empty($_GET['cPath'])){
			$ResultSet = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchAssoc('select categories_id from categories_description where categories_id = "' . $_GET['cPath'] .'" and language_id = "'.Session::get('languages_id').'" ');
		$current_category_id = $_GET['cPath'];
		Session::set('current_category_id', $current_category_id);
		}*/
	}

	if (!empty($catSeoUrl) && $catSeoUrl != 'default'){
		$navigation->add_current_page();
		$ResultSet = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc('select * from categories_description where categories_id = "' . $current_category_id .'" and language_id = "'.Session::get('languages_id').'"');
		$breadcrumb->add($ResultSet[0]['categories_name'], itw_app_link(null, 'index', $ResultSet[0]['categories_seo_url']));
		//Session::set('current_app_page',$ResultSet[0]['categories_seo_url']);
		$_GET['actualPage'] = $ResultSet[0]['categories_seo_url'];
		//$App->setAppPage($ResultSet[0]['categories_seo_url']);
		if(sysConfig::get('TOOLTIP_DESCRIPTION_ENABLED') == 'true'){
            $App->addStylesheetFile('ext/jQuery/external/mopTip/mopTip-2.2.css');
            $App->addJavascriptFile('ext/jQuery/external/mopTip/mopTip-2.2.js');
		}
		$App->addJavascriptFile('applications/products/javascript/common.js');

		$App->setAppPage('products');

		//$appContent = sysConfig::getDirFsCatalog() . 'applications/index/pages/products.php';
	}else{ // default page
		$App->setAppPage('default');
		//$appContent = sysConfig::getDirFsCatalog() . 'applications/index/pages/default.php';
	}
    $appContent = $App->getAppContentFile();
?>