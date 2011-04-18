<?php
/*
	Product Designer Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/


	$Categories = Doctrine_Core::getTable('ProductDesignerClipartCategories');

	if (isset($_GET['cID'])){
		$Category = $Categories->findOneByCategoriesId((int)$_GET['cID']);
	}else{
		$Category = $Categories->create();
		if (isset($_GET['clipart_cPath'])){
			$path = explode('_', $_GET['clipart_cPath']);
			$Category->parent_id = $path[sizeof($path) - 1];
		}
	}

	$Category->sort_order = (int)$_POST['sort_order'];
	if ($categories_image = new upload('categories_image', DIR_FS_CATALOG_IMAGES)){
		if (tep_not_null($categories_image->filename)){
			$Category->categories_image = $categories_image->filename;
		}
	}

	$languages = tep_get_languages();
	$CategoriesDescription = &$Category->ProductDesignerClipartCategoriesDescription;

	for ($i = 0, $n = sizeof($languages); $i < $n; $i++){
		$lID = $languages[$i]['id'];
		$CategoriesDescription[$lID]->language_id = $lID;
		$CategoriesDescription[$lID]->categories_name = $_POST['categories_name'][$lID];
	}

	$Category->save();

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'cID')).'cID='.$Category->categories_id, null, 'default'), 'redirect');
?>
