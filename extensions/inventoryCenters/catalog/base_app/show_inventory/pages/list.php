<?php


	$inventory_centers = $appExtension->getExtension('inventoryCenters');
	//$langId = Session::get('languages_id');

	$invcent = Doctrine_Query::create()
				->select('inventory_center_name, inventory_center_address, inventory_center_id')
				->from('ProductsInventoryCenters')
				->orderBy('inventory_center_name')
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	$contentHtml = '';
	foreach($invcent as $invInfo){
		$contentHtml .= "<div class='list_inv'><b>Spot:</b> ".$invInfo['inventory_center_name']."<br/>";
		//$contentHtml .= '<b>Address:</b> '. $invInfo['inventory_center_address']."<br/>";
		$contentHtml .= "<a class='moreinfo' href='".itw_app_link('appExt=inventoryCenters&inv_id='.$invInfo['inventory_center_id'],'show_inventory','default')."'><b>More info</b></a>"."</div>";

		//$contentHtml .= '<input type="hidden" name="inventory_center_address" value="'.$invInfo['inventory_center_address'].'"/>';
		//$script = "<script type='text/javascript' src='http://maps.google.com/maps?file=api&v=2&sensor=false&key=". EXTENSION_INVENTORY_CENTERS_GOOGLE_MAPS_API_KEY."'></script>";
	}

	$contentHtml = stripslashes($contentHtml);
	$continueButton = htmlBase::newElement('button')->usePreset('continue')
	->setHref(itw_app_link(null, 'index', 'default'));

	$pageContent->setVars(array(
		'pageHeader'     => "List of Inventory Centers",
		'continueButton' => $continueButton->draw(),
		'pageContent'    => $contentHtml
	));


		$pageContent->setTemplateFile('default_list.tpl', DIR_FS_CATALOG . 'extensions/inventoryCenters/catalog/base_app/show_inventory/templates/');

	echo $pageContent->parse();
?>