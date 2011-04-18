<?php

	$pageKey = $_POST['page_key'];
	$pageTitle = $_POST['page_title'];
	$categories = implode(',',$_POST['categories']);

	$categoriesPages = Doctrine_Core::getTable('CategoriesPages');
	if (isset($_GET['cID'])){
		$categoriesPages = $categoriesPages->find((int)$_GET['cID']);
	}else{
		$categoriesPages = new CategoriesPages();
	}
	

	$categoriesPages->page_key = $pageKey;
	$categoriesPages->page_title = $pageTitle;
	$categoriesPages->categories = $categories;
	$categoriesPages->save();

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'cID')) . 'cID=' . $categoriesPages->categories_pages_id, null, 'default'), 'redirect');
?>