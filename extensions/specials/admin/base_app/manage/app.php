<?php
/*
	Products Specials Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	$appContent = $App->getAppContentFile();

	if ($App->getAppPage() == 'new'){
			$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.datepicker.js');
	}

	require('includes/classes/currencies.php');
	$currencies = new currencies();
?>