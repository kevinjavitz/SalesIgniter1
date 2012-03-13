<?php


	$inventory_centers = $appExtension->getExtension('inventoryCenters');
	//$langId = Session::get('languages_id');

	$invcent = Doctrine_Query::create()
		->from('ProductsInventoryCenters ic')
		->leftJoin('ic.ProductsInventoryBarcodesToInventoryCenters b2c')
		->leftJoin('b2c.ProductsInventoryBarcodes b')
		->leftJoin('b.ProductsInventory i')
		->leftJoin('i.Products p')
		->leftJoin('p.ProductsToCategories p2c')
		->leftJoin('p2c.Categories c')
		->leftJoin('c.CategoriesDescription cd')
		->where('cd.language_id = ?', Session::get('languages_id'));

	if(Session::exists('redirectCategoryBefore')){
		$invcent->where('cd.categories_seo_url = ?', Session::get('redirectCategoryBefore'));
	}

	$invcent->andWhere('i.use_center = ?', '1')
	->orderBy('inventory_center_name');

	if(Session::exists('isppr_city') && Session::get('isppr_city') != ''){
		$invcent->andWhere('inventory_center_city=?', Session::get('isppr_city'));
	}

	if(Session::exists('isppr_continent') && Session::get('isppr_continent') != ''){
		$invcent->andWhere('inventory_center_continent=?', Session::get('isppr_continent'));
	}

	if(Session::exists('isppr_state') && Session::get('isppr_state') != ''){
		$invcent->andWhere('inventory_center_state=?', Session::get('isppr_state'));
	}

	if(Session::exists('isppr_country') && Session::get('isppr_country') != ''){
		$invcent->andWhere('inventory_center_country=?', Session::get('isppr_country'));
	}

	$invcent = $invcent->execute(array(), Doctrine_Core::HYDRATE_ARRAY);


	$contentHtml = "<div class='list_inv'>";

	foreach($invcent as $invInfo){
		//check for multistore Ext
		$f = true;
		$multiStore = $appExtension->getExtension('multiStore');
		if ($multiStore !== false && $multiStore->isEnabled() === true){
			$store = explode(';',$invInfo['inventory_center_stores']);
			if(in_array(Session::get('current_store_id'), $store)){
				$f = true;
			}else{
				$f = false;
			}
		}

		 if($f){
			$contentHtml .= '<div class="inv_block" style="margin-bottom: 17px; border-bottom: 1px solid #000000;margin-top:17px;padding-bottom: 10px;">';
			$contentHtml .= '<div class="inv_image" style="display: inline-block;margin-right:2%;width:20%;vertical-align: top;">';
			$thumbUrl = 'imagick_thumb.php?path=rel&imgSrc=images/'.$invInfo['inventory_center_image'].'&width='.'150'.'&height='.'150';
			$contentHtml .= '<img src="'.$thumbUrl.'"/>';
			$contentHtml .= "</div>";

			$contentHtml .= '<div class="inv_address" style="display: inline-block;margin-right:2%;width:40%;vertical-align: top;">';
			$contentHtml .= "<a class='moreinfoTitle' style='font-size:14px;font-weight:bold;' href='".itw_app_link('appExt=inventoryCenters&inv_id='.$invInfo['inventory_center_id'],'show_inventory','default')."'>".$invInfo['inventory_center_name']."</a><br/>";
			$contentHtml .= $invInfo['inventory_center_specific_address'];
			$contentHtml .= "<a class='moreinfo' href='".itw_app_link('appExt=inventoryCenters&inv_id='.$invInfo['inventory_center_id'],'show_inventory','default')."'>More Location Details</a>";
			$contentHtml .= "</div>";

			$contentHtml .= '<div class="inv_details" style="display: inline-block;margin-right:2%;width:20%;vertical-align: top;">';
			$contentHtml .= $invInfo['inventory_center_short_details'];
			$contentHtml .= "</div>";

			$contentHtml .= '<div class="inv_button" style="display: inline-block;vertical-align: top;width:14%;">';
			$htmlButton = htmlBase::newElement('button')
			->setText('Select')
			->setHref(itw_app_link('appExt=inventoryCenters&action=selectInv&inv_id='.$invInfo['inventory_center_id'],'show_inventory','list_select'));
			$contentHtml .= $htmlButton->draw();
			$contentHtml .= "</div>";

			$contentHtml .= "</div>";

		}
	}
    $contentHtml .= '</div>';
	$contentHtml = stripslashes($contentHtml);
	/*$continueButton = htmlBase::newElement('button')->usePreset('continue')
	->setHref(itw_app_link(null, 'index', 'default'));*/
    $contentHeading = sysLanguage::get('EXTENSION_INVENTORY_CENTERS_LIST_OF_CENTERS');
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