<?php



	$storeList = Doctrine_Query::create()
	->from('Stores')
	->orderBy('is_default ASC, stores_group, stores_name')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	$contentHtml = '<div class="main_list" style="padding: 10px;">';
	$groupBefore = '';
	foreach($storeList as $storeInfo){
	   	if($groupBefore != $storeInfo['stores_group']){
			$contentHtml .= '<span style="color:red"><b>'.$storeInfo['stores_group'].'</b></span><br/><br/>';
			$groupBefore = $storeInfo['stores_group'];
		}
		$contentHtml .= "<div class='list_inv' style='padding:0px;height:24;margin-top:20px;margin-bottom:10px;'><div style='width:128px;height:24px;display:inline-block;margin-left:40px;'><a class='moreinfo' href='".itw_app_link('appExt=multiStore&store_id='.$storeInfo['stores_id'],'show_store','default')."'>".$storeInfo['stores_location']."</a></div><div style='width:128px;height:24px;display:inline-block;margin-left:40px;'>".$storeInfo['stores_telephone']."</div><div style='width:128px;height:24px;display:inline-block;margin-left:40px;'>".$storeInfo['stores_email']."</div></div>";
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