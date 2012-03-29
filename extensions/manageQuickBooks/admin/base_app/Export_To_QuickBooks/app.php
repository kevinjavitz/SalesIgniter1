<?php
/*
	Manage QuickBooks Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2012 I.T. Web Experts

	This script and its source is not redistributable
*/

	$appContent = $App->getAppContentFile();

       $App->addJavascriptFile('https://appcenter.intuit.com/Content/IA/intuit.ipp.anywhere.js'); //external work?
       $App->addJavascriptFile('extensions/manageQuickBooks/js/intuit.js');
?>