<?php

	$storeInfo = Doctrine_Query::create()
	->from('Stores')
	->where('stores_id = ?', $_GET['store_id'])
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	$storeInfo = $storeInfo[0];
	$contentHeading = $storeInfo['stores_name'];
	$contentHtml = 'Location: '. $storeInfo['stores_location']."<br/><br/>Description: ".$storeInfo['stores_info'];//."<br/><br/>Location: <br/><div id='googleMap' style='width:500px;height:500px;'></div>";
	$contentHeading = stripslashes($contentHeading);
	$contentHtml = stripslashes($contentHtml);

	$pageTitle = $contentHeading;
	$pageContents = $contentHtml;

	$pageButtons = htmlBase::newElement('button')
	->usePreset('back')
	->setHref(itw_app_link('appExt=multiStore','show_store','list'))
	->draw();

	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);

?>