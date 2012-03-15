<?php
	$Categories = Doctrine_Core::getTable('BlogCategories');
	if (isset($_GET['cID'])){
		$Category = $Categories->findOneByBlogCategoriesId((int)$_GET['cID']);
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
	$Category->categories_image = $_POST['categories_image'];


	$languages = tep_get_languages();
	$Category->save();
	if(!isset($categoryId)){
		$categoryId = $Category->categories_id;
	}
	$CategoriesDescription =& $Category->BlogCategoriesDescription;
	for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
		$lID = $languages[$i]['id'];

		$CategoriesDescription[$lID]->language_id = $lID;
		$CategoriesDescription[$lID]->blog_categories_title = $_POST['blog_categories_title'][$lID];
		$CategoriesDescription[$lID]->blog_categories_description_text = $_POST['blog_categories_description_text'][$lID];

		if (/*EXTENSION_HEADER_TAGS_ENABLED == 'True'*/ true === true){
			$CategoriesDescription[$lID]->blog_categories_seo_url = tep_friendly_seo_url($_POST['blog_categories_title'][$lID]);
			$CategoriesDescription[$lID]->blog_categories_htc_title = $_POST['blog_categories_title'][$lID];
			$CategoriesDescription[$lID]->blog_categories_htc_desc = $_POST['blog_categories_title'][$lID];
			$CategoriesDescription[$lID]->blog_categories_htc_keywords = $_POST['blog_categories_title'][$lID];

			if (!empty($_POST['blog_categories_seo_url'][$lID])){
				$CategoriesDescription[$lID]->blog_categories_seo_url = tep_friendly_seo_url($_POST['blog_categories_seo_url'][$lID]);
			}

			if (!empty($_POST['blog_categories_htc_title'][$lID])){
				$CategoriesDescription[$lID]->blog_categories_htc_title = $_POST['blog_categories_htc_title'][$lID];
			}

			if (!empty($_POST['blog_categories_htc_desc'][$lID])){
				$CategoriesDescription[$lID]->blog_categories_htc_desc = $_POST['blog_categories_htc_desc'][$lID];
			}

			if (!empty($_POST['blog_categories_htc_keywords'][$lID])){
				$CategoriesDescription[$lID]->blog_categories_htc_keywords = $_POST['blog_categories_htc_keywords'][$lID];
			}
		}
	}

	$Category->save();

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'cID')) . 'cID=' . $Category->blog_categories_id, null, 'default'), 'redirect');
?>