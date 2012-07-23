<?php
	$appContent = $App->getAppContentFile();

	$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.datepicker.js');
	$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.autocomplete.js');
	$App->addStylesheetFile('ext/jQuery/themes/smoothness/ui.autocomplete.css');
	if(!class_exists('Product')){
		require(sysConfig::getDirFsCatalog() . 'includes/classes/product.php');
	}
	require(sysConfig::getDirFsCatalog() . 'includes/classes/currencies.php');
	$currencies = new currencies();
?>