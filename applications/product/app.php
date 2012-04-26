<?php

	require('includes/functions/google_maps_ppr.php');

	$appContent = $App->getAppContentFile();

	$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.tabs.js');
	$App->addJavascriptFile('streamer/flowplayer/flowplayer-3.2.4.min.js');
	$App->addJavascriptFile('ext/jQuery/external/fancybox/jquery.fancybox.js');
	$App->addJavascriptFile('ext/jQuery/external/jqzoom/jquery.jqzoom.js');

	$App->addStylesheetFile('ext/jQuery/external/fancybox/jquery.fancybox.css');
	$App->addStylesheetFile('ext/jQuery/external/jqzoom/jquery.jqzoom.css');

	$App->addJavascriptFile('ext/jQuery/external/fullcalendar/fullcalendar.js');
	$App->addJavascriptFile('ext/jQuery/external/datepick/jquery.datepick.js');

	$App->addStylesheetFile('ext/jQuery/external/fullcalendar/fullcalendar.css');
	$App->addStylesheetFile('ext/jQuery/external/datepick/css/jquery.datepick.css');
	$App->addStylesheetFile('ext/jQuery/themes/smoothness/ui.tabs.css');

	if (isset($_POST['action']) && ($_POST['action'] == 'checkRes' || $_POST['action'] == 'getReservedDates')){
		$action = $_POST['action'];
	}elseif (isset($_GET['action']) && ($_GET['action'] == 'checkRes' || $_GET['action'] == 'getReservedDates')){
		$action = $_GET['action'];
	}

	$pageTabsFolder = sysConfig::getDirFsCatalog() . 'applications/product/pages_tabs/' . $App->getAppPage() . '/';
	if (isset($_GET['products_id'])){
		$product = new Product((int)$_GET['products_id']);
	}
	if(isset($_GET['tplDir']) && $_GET['tplDir'] == 'codeGenerator'){
		$_GET['actualPage'] =  $_GET['products_id'];
	}

	if(!isset($_GET['products_id']) && !isset($_GET['pID']) && !isset($_POST['products_id']) && !isset($_POST['pID'])){
		tep_redirect(itw_app_link(null,'index','default'));
	}
	
	switch($App->getPageName()){
		case 'info':
			$breadcrumb->add(sysLanguage::get('NAVBAR_TITLE_INFO'), itw_app_link(tep_get_all_get_params(), 'product', 'info'));
			break;
		case 'reviews':
			$breadcrumb->add(sysLanguage::get('NAVBAR_TITLE_REVIEWS'), itw_app_link(tep_get_all_get_params(), 'product', 'reviews'));
			break;
	}
?>