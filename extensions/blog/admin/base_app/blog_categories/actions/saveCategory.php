<?php
	$Categories = Doctrine_Core::getTable('BlogCategories');
	if (isset($_GET['cID'])){
		$Category = $Categories->findOneByBlogCategoriesId((int)$_GET['cID']);
	}else{
		$Category = $Categories->create();
		if (isset($_GET['blog_cPath'])){
			$path = explode('_', $_GET['blog_cPath']);
			$Category->parent_id = $path[sizeof($path)-1];
		}
	}
	
	$Category->sort_order = (int)$_POST['sort_order'];


	$languages = tep_get_languages();
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