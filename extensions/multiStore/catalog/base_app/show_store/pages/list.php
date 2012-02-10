<?php



	$storeList = Doctrine_Query::create()
	->from('Stores')
	->orderBy('stores_name')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	$contentHtml = '<div class="main_list" style="padding: 10px;">';
	foreach($storeList as $storeInfo){
		$contentHtml .= "<div class='list_inv' style='padding:10px;'><b>Store Name:</b> ".$storeInfo['stores_name']."<br/><b>Store Location:</b>".$storeInfo['stores_location']."<br/>";
		$contentHtml .= "<a class='moreinfo' href='".itw_app_link('appExt=multiStore&store_id='.$storeInfo['stores_id'],'show_store','default')."'><b>Area info</b></a>"."</div>";
	}
    $contentHtml .= '</div>';
	$contentHtml = stripslashes($contentHtml);
	/*$continueButton = htmlBase::newElement('button')->usePreset('continue')
	->setHref(itw_app_link(null, 'index', 'default'));*/
    $contentHeading = sysLanguage::get('EXTENSION_MULTISTORE_LIST_OF_STORES');
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
	/*$pageContent->setVars(array(
		'pageHeader'     => "",
		'continueButton' => $continueButton->draw(),
		'pageContent'    => $contentHtml
	));


	$pageContent->setTemplateFile('default_list.tpl', DIR_FS_CATALOG . 'extensions/inventoryCenters/catalog/base_app/show_inventory/templates/');

	echo $pageContent->parse();*/
?>