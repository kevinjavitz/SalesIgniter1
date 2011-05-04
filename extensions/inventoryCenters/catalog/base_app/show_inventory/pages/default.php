<?php

	$inventory_centers = $appExtension->getExtension('inventoryCenters');
	//$langId = Session::get('languages_id');
	$invInfo = $inventory_centers->getInventoryCenters((isset($_GET['inv_id']) ? (int)$_GET['inv_id'] : $_GET['inv_name']));
	$invInfo = $invInfo[0];

	/*Select in PPRBox*/
    Session::set('isppr_city', $invInfo['inventory_center_city']);
	Session::set('isppr_state', $invInfo['inventory_center_state']);
	Session::set('isppr_country', $invInfo['inventory_center_country']);
	Session::set('isppr_continent', $invInfo['inventory_center_continent']);

	$contentHeading = $invInfo['inventory_center_name'];
	$contentHtml = 'Address: '. $invInfo['inventory_center_address']."<br/><br/>Description: ".$invInfo['inventory_center_details']."<br/><br/>Location: <br/><div id='googleMap' style='width:500px;height:500px;'></div>";
	$contentHtml .= '<input type="hidden" name="inventory_center_address" value="'.$invInfo['inventory_center_address'].'"/>';
	$script = "<script type='text/javascript' src='http://maps.google.com/maps?file=api&v=2&sensor=false&key=". Session::get('google_key') ."'></script>";
	$contentHeading = stripslashes($contentHeading);
	$contentHtml = stripslashes($contentHtml);

	$pageTitle = $contentHeading;
	$pageContents = $contentHtml;

	//$pageButtons = htmlBase::newElement('button')
	//->usePreset('continue')
	//->setHref(itw_app_link(null, 'index', 'default'))
	//->draw();

	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	//$pageContent->set('pageButtons', $pageButtons);

	if (isset($_GET['dialog'])){
		$Template->setPopupMode(true);
	}

	/*$continueButton = htmlBase::newElement('button')->usePreset('continue')
	->setHref(itw_app_link(null, 'index', 'default'));
		
	$pageContent->setVars(array(
		'pageHeader'     => $contentHeading,
		'continueButton' => $continueButton->draw(),
		'pageContent'    => $contentHtml,
		'script'    =>  $script
	));


	$pageContent->setTemplateFile('default.tpl', DIR_FS_CATALOG . 'extensions/inventoryCenters/catalog/base_app/show_inventory/templates/');

	echo $pageContent->parse();*/
?>