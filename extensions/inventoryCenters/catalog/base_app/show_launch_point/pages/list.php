<?php


	$inventory_centers = $appExtension->getExtension('inventoryCenters');
	//$langId = Session::get('languages_id');
	$invcent = Doctrine_Query::create()
				->from('InventoryCentersLaunchPoints')
				->orderBy('lp_name');
	$invcent = $invcent->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	$contentHtml = '<div id="gMapList"></div><br/><br/><div class="main_list1">';
	foreach($invcent as $invInfo){
		//check for multistore Ext

			$contentHtml .= "<div class='list_inv1'><b>Launch Point:</b> ".$invInfo['lp_name']."<br/>";
			//$contentHtml .= '<b>Address:</b> '. $invInfo['inventory_center_address']."<br/>";
			$contentHtml .= "<a class='moreinfo' href='".itw_app_link('appExt=inventoryCenters&lp_id='.$invInfo['lp_id'],'show_launch_point','default')."'><b>More info</b></a>"."</div>";
		//$contentHtml .= '<input type="hidden" name="inventory_center_address" value="'.$invInfo['inventory_center_address'].'"/>';
		//$script = "<script type='text/javascript' src='http://maps.google.com/maps?file=api&v=2&sensor=false&key=". EXTENSION_INVENTORY_CENTERS_GOOGLE_MAPS_API_KEY."'></script>";
	}
    $contentHtml .= '</div>';
	$contentHtml = stripslashes($contentHtml);
	/*$continueButton = htmlBase::newElement('button')->usePreset('continue')
	->setHref(itw_app_link(null, 'index', 'default'));*/
    $contentHeading = sysLanguage::get('EXTENSION_INVENTORY_CENTERS_LIST_OF_LAUNCH_POINTS');
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