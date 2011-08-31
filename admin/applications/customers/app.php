<?php
	if (isset($_GET['cID'])){
		$cID = $_GET['cID'];
	}

	require('includes/classes/data_populate/export.php');
	include(sysConfig::getDirFsCatalog() . 'includes/functions/crypt.php');
	
	require(sysConfig::getDirFsCatalog() . 'includes/classes/currencies.php');
	$currencies = new currencies();

	$appContent = $App->getAppContentFile();

	if ($App->getAppPage() == 'edit'){
		$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.tabs.js');
		$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.datepicker.js');
		$userAccount = new rentalStoreUser($cID);
		$userAccount->loadPlugins();
	}
?>