<?php
/*
	Blog Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

//if (!isset($_GET['post_id'])){
    $App->setAppPage('default');
//}
	$QCategories = $Query = Doctrine_Query::create()
		->from('BlogPosts p')
		->leftJoin('p.BlogPostsDescription pd')
		->leftJoin('p.BlogPostToCategories c')
		->leftJoin('c.BlogCategories cc')
		->leftJoin('cc.BlogCategoriesDescription cd')
		->where('p.post_status = 1')
		->orderBy('p.post_date desc')
		->andWhere('pd.blog_post_seo_url = ?', $_GET['appPage'])
		->andWhere('pd.language_id = ?', (int) Session::get('languages_id'))
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	if(isset($QCategories[0]['BlogPostToCategories'][0]['BlogCategories']['BlogCategoriesDescription'][0]['blog_categories_seo_url'])){
		$_GET['actualPage'] = 'posts-'.$QCategories[0]['BlogPostToCategories'][0]['BlogCategories']['BlogCategoriesDescription'][0]['blog_categories_seo_url'];
	}

	$appContent = $App->getAppContentFile();

if ($App->getAppPage() == 'default'){
		//$javascriptFiles[] =  'admin/rental_wysiwyg/ckeditor.js';
		//$javascriptFiles[] = 'admin/rental_wysiwyg/adapters/jquery.js';
	}
?>