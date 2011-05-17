<?php

	$inventory_centers = $appExtension->getExtension('inventoryCenters');
	//$langId = Session::get('languages_id');
	$invInfo = $inventory_centers->getInventoryCenters((isset($_GET['inv_id']) ? (int)$_GET['inv_id'] : $_GET['inv_name']));
	$invInfo = $invInfo[0];
	$contentHeading = $invInfo['inventory_center_name'];
	$contentHtml = 'Address: '. $invInfo['inventory_center_address']."<br/><br/>Description: ".$invInfo['inventory_center_details'];//."<br/><br/>Location: <br/><div id='googleMap' style='width:500px;height:500px;'></div>";
	$contentHtml .= '<input type="hidden" name="inventory_center_address" value="'.$invInfo['inventory_center_address'].'"/>';
	$script = "<script type='text/javascript' src='http://maps.google.com/maps?file=api&v=2&sensor=false&key=". Session::get('google_key') ."'></script>";
	$contentHeading = stripslashes($contentHeading);
	$contentHtml = stripslashes($contentHtml);

	$pageTitle = $contentHeading;
	$pageContents = $contentHtml;

	$pageButtons = htmlBase::newElement('button')
	->usePreset('back')
	->setHref(itw_app_link(null, 'index', 'default'))
	->draw();

	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);

?>