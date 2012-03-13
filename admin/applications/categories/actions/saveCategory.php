<?php
	$Categories = Doctrine_Core::getTable('Categories');

	if (isset($_GET['cID'])){
		$Category = $Categories->findOneByCategoriesId((int)$_GET['cID']);
		$categoryId = $_GET['cID'];
	}else{
		$Category = $Categories->create();
		if (isset($_GET['parent_id'])){
			$Category->parent_id = $_GET['parent_id'];
			$categoryId = $_GET['parent_id'];
		}
	}

	if (isset($_POST['parent_id']) && $_POST['parent_id'] > -1){
		$Category->parent_id = $_POST['parent_id'];
		$categoryId = $_POST['parent_id'];
	}

	$Category->sort_order = (int)$_POST['sort_order'];
	$Category->categories_menu = (isset($_POST['categories_menu']) ? $_POST['categories_menu'] : 'infobox');
	$Category->categories_image = $_POST['categories_image'];


	$languages = tep_get_languages();
	$Category->save();
	if(!isset($categoryId)){
		$categoryId = $Category->categories_id;
	}
	$CategoriesDescription =& $Category->CategoriesDescription;
	for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
		$lID = $languages[$i]['id'];

		$CategoriesDescription[$lID]->language_id = $lID;
		$CategoriesDescription[$lID]->categories_name = $_POST['categories_name'][$lID];
		$CategoriesDescription[$lID]->categories_description = $_POST['categories_description'][$lID];
		if(!empty($_POST['categories_seo_url'][$lID])){
			$CategoriesDescription[$lID]->categories_seo_url = makeUniqueCategory($categoryId, tep_friendly_seo_url($_POST['categories_seo_url'][$lID]), (isset($_GET['cID'])?true:false));
		}else{
			$CategoriesDescription[$lID]->categories_seo_url = makeUniqueCategory($categoryId, tep_friendly_seo_url($_POST['categories_name'][$lID]), false);
		}
	}

	/*
	 * anything additional to handle into $ArticlesDescription ?
	 */
	EventManager::notify('CategoriesDescriptionsBeforeSave', &$CategoriesDescription);

	$Category->save();

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'cID')) . 'cID=' . $Category->categories_id, null, 'default'), 'redirect');
?>
