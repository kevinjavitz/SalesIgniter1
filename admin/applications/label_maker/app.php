<?php
	$appContent = $App->getAppContentFile();

	require(DIR_WS_CLASSES . 'currencies.php');
	$currencies = new currencies();
	
	require('../includes/classes/product.php');
	require('includes/classes/pdf_labels.php');
	require(dirname(__FILE__) . '/classes/labels.php');

	$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.datepicker.js');
?>