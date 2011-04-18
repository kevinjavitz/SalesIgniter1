<?php
/*
	Product Designer Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	$appContent = $App->getAppContentFile();
	
	$App->addStylesheetFile('ext/jQuery/external/uploadify/jquery.uploadify.css');
	$App->addStylesheetFile('extensions/productDesigner/catalog/base_app/clipart/stylesheets/jquery.treeview.css');

	$App->addJavascriptFile('ext/jQuery/external/uploadify/swfobject.js');
	$App->addJavascriptFile('ext/jQuery/external/uploadify/jquery.uploadify.js');
	$App->addJavascriptFile('extensions/productDesigner/catalog/javascript/simpleColorPicker.js');
	$App->addJavascriptFile('extensions/productDesigner/catalog/base_app/clipart/javascript/jquery.treeview.js');

	if (isset($_GET['products_id'])){
		$product = new product($_GET['products_id']);
	}
?>