<?php
	require(sysConfig::getDirFsAdmin() . 'includes/classes/table_block.php');
	require(sysConfig::getDirFsAdmin() . 'includes/classes/box.php');
	require(sysConfig::getDirFsAdmin() . 'includes/classes/split_page_results.php');

	require(sysConfig::getDirFsCatalog() . 'includes/classes/currencies.php');
	$currencies = new currencies();
	
	$appContent = $App->getAppContentFile();

	$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.datepicker.js');
	$App->addJavascriptFile('ext/jQuery/ui/jquery.effects.core.js');
	$App->addJavascriptFile('ext/jQuery/ui/jquery.effects.slide.js');
	$App->addJavascriptFile('ext/jQuery/ui/jquery.effects.fold.js');
	$App->addJavascriptFile('ext/jQuery/ui/jquery.effects.fade.js');
?>