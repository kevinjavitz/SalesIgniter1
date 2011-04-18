<?php
/*
	Product Designer Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	$appContent = $App->getAppContentFile();

	if (isset($_GET['kID'])){
		$infoBoxId = $_GET['kID'];
	} elseif ($action == 'new'){
		$infoBoxId = 'new';
	}
	$App->setInfoBoxId($infoBoxId);
?>