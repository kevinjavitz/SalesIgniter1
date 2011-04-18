<?php
/*
	Product Designer Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/
	
	$appContent = $App->getAppContentFile();
	
	$App->addJavascriptFile('extensions/productDesigner/catalog/javascript/simpleColorPicker.js');
	$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.spinner.js');
	$App->addJavascriptFile('extensions/productDesigner/catalog/base_app/design/javascript/default.js');
	$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.resizable.js');
	$App->addJavascriptFile('extensions/productDesigner/catalog/base_app/clipart/javascript/jquery.cookie.js');
	$App->addJavascriptFile('extensions/productDesigner/catalog/base_app/clipart/javascript/jquery.treeview.js');

	$App->addStylesheetFile('ext/jQuery/themes/smoothness/ui.spinner.css');
	$App->addStylesheetFile('extensions/productDesigner/catalog/base_app/clipart/stylesheets/jquery.treeview.css');

	if (isset($_GET['dID'])){
		$App->setInfoBoxId($_GET['dID']);
	}
?>