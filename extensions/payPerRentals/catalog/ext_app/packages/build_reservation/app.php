<?php
/*
	Pay Per Rentals Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	$appContent = $App->getAppContentFile();

	$App->addJavascriptFile('ext/jQuery/external/fullcalendar/fullcalendar.js');
	$App->addJavascriptFile('ext/jQuery/external/datepick/jquery.datepick.js');

	$App->addStylesheetFile('ext/jQuery/external/fullcalendar/fullcalendar.css');
	$App->addStylesheetFile('ext/jQuery/external/datepick/css/jquery.datepick.css');

	if (isset($_POST['action']) && ($_POST['action'] == 'checkRes' || $_POST['action'] == 'getReservedDates')){
		$action = $_POST['action'];
	}elseif (isset($_GET['action']) && ($_GET['action'] == 'checkRes' || $_GET['action'] == 'getReservedDates')){
		$action = $_GET['action'];
	}

	$navigation->remove_current_page();
?>