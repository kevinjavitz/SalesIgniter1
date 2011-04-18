<?php
	$Categories = Doctrine_Core::getTable('BlogCategories')->findOneByBlogCategoriesId((int)$_POST['blog_categories_id']);
	if ($Categories){
		$Categories->delete();
	}

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('cID', 'action'))), 'redirect');
?>