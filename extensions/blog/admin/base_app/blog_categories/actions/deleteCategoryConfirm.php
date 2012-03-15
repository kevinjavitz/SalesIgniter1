<?php
	$success = false;
	$Categories = Doctrine_Core::getTable('BlogCategories')->findOneByBlogCategoriesId((int)$_POST['blog_categories_id']);
	if ($Categories){
		$Categories->delete();
		$success = true;
	}

	EventManager::attachActionResponse(array(
		'success' => $success
	), 'json');
?>