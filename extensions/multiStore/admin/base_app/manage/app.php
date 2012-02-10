<?php
/*
	Multi Stores Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	$appContent = $App->getAppContentFile();

	$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.tabs.js');
	$App->addJavascriptFile('admin/rental_wysiwyg/ckeditor.js');
	$App->addJavascriptFile('admin/rental_wysiwyg/adapters/jquery.js');

	if (isset($_GET['sID'])){
		$infoBoxId = $_GET['sID'];
	}
?>