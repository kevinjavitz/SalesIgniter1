<?php

	$inventory_centers = $appExtension->getExtension('inventoryCenters');
	$invInfo = Doctrine_Query::create()
		->from('InventoryCentersLaunchPoints');
	if(isset($_GET['lp_id'])){
		$invInfo->where('lp_id = ?', $_GET['lp_id']);
	}else{
		$invInfo->where('lp_name = ?', $_GET['lp_name']);
	}
	$invInfo = $invInfo->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	$invInfo = $invInfo[0];
	$contentHeading = $invInfo['lp_name'];
	$contentHtml = 'Position: '. $invInfo['lp_position']."<br/><br/>Description: ".$invInfo['lp_desc'];//."<br/><br/>Location: <br/><div id='googleMap' style='width:500px;height:500px;'></div>";

	$contentHeading = stripslashes($contentHeading);
	$contentHtml = stripslashes($contentHtml);

	$pageTitle = $contentHeading;
	$pageContents = $contentHtml;

	$pageButtons = htmlBase::newElement('button')
	->usePreset('back')
	->setHref(itw_app_link(null, 'index', 'default'))
	->draw();

	$pageContent->set('pageTitle', $contentHeading);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
	if (isset($_GET['dialog'])){
		$Template->setPopupMode(true);
	}

?>