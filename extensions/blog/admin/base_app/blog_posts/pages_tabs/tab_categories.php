<?php
	$checkedCats = array();
	if ($Post['post_id'] > 0){
		$QcurCategories = Doctrine_Query::create()
		->select('blog_categories_id')
		->from('BlogPostToCategories')
		->where('blog_post_id = ?', $Post['post_id'])
		->execute();
		if ($QcurCategories->count() > 0){
			foreach($QcurCategories->toArray() as $category){
				$checkedCats[] = $category['blog_categories_id'];
			}
			unset($category);
		}
		$QcurCategories->free();
		unset($QcurCategories);
	}
	echo tep_get_blog_category_tree_list('0', $checkedCats);
	unset($checkedCats);
?>