<?php
/*
	Blog Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/
	$blog = $appExtension->getExtension('blog');


	if ($_GET['appPage'] == 'default'){
		$app_pg = null;
	}else{
		$app_pg = $_GET['appPage'];
	}

	if(!isset($_GET['page'])){
		$pg = 1;
	}else{
		$pg = $_GET['page'];
	}


	$pg_limit  = (int) sysConfig::get('EXTENSION_BLOG_POST_PER_PAGE');

	$pagerBar = '';
    if(!is_null($app_pg)){
		$month_year = explode('-', $app_pg );

		$time = strptime($month_year[0], '%B');
		$month = $time['tm_mon']+1;
		$year = $month_year[1];
    }else{
	    $month = null;
	    $year = null;
    }
	$posts = $blog->getArchivesPosts(null, $month, $year, $pg_limit, $pg, &$pagerBar);

	$contentHtml = '';
	foreach ($posts as $post){
		$categ = '';
		foreach ($post['BlogPostToCategories'] as $cat){
			$categ .= $cat['BlogCategories']['BlogCategoriesDescription'][1]['blog_categories_title'] . ', ';
		}
		$categ = substr($categ, 0, strlen($categ) - 2);


		$contentHtml .= "<h2 class='blog_post_title'><a href='". itw_app_link('appExt=blog', 'show_post', $post['BlogPostsDescription'][Session::get('languages_id')]['blog_post_seo_url']) . "'>" . $post['BlogPostsDescription'][1]['blog_post_title'] . "</a></h2>";
		$contentHtml .= "<div class='blog_post_text'>" . $post['BlogPostsDescription'][Session::get('languages_id')]['blog_post_text'] . "</div>";
		$contentHtml .= "<p class='blog_post_foot'>" . "Date: " . tep_date_short($post['post_date']) . "<br/>Categories: " . $categ . "</p>";
	}

	if ($app_pg == null){
		$contentHeading = "Blog Archives";
	}else{
		$contentHeading = $blog->getArchiveHeaderTitle($app_pg);
	}

	$contentHtml .= "<br/><br/>" . $pagerBar;

	$pageTitle = stripslashes($contentHeading);
	$pageContents = stripslashes($contentHtml);

	$pageButtons = htmlBase::newElement('button')
	->usePreset('continue')
	->setHref(itw_app_link(null, 'index', 'default'))
	->draw();

	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
