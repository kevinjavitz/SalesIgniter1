<?php
	$shInfo = ReservationUtilities::getShippingDetails((isset($_GET['sh_id']) ? $_GET['sh_id'] : null));

	$contentHtml = "Shipping Description: ".$shInfo['details'];

	$pageTitle = stripslashes($shInfo['title']);
	$pageContents = stripslashes($contentHtml);

	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$Template->setPopupMode(true);


