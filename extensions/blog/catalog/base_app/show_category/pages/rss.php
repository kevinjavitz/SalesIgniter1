<?php
/*
	Blog Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

    $blog = $appExtension->getExtension('blog');

	if($_GET['appPage'] == 'rss'){
		$app_pg = null;
	}else{
		$app_pg = $_GET['appPage'];
	}

	$pg = 1;

	$pg_limit  = 20000;

	$pagerBar = '';
	$posts = $blog->getCategoriesPosts(null, $app_pg, $pg_limit, $pg, &$pagerBar);
    if($app_pg == null){
		$rssHeading = $blog->getCategoryHeaderTitle(sysConfig::get('EXTENSION_BLOG_DEFAULT_CATEGORY'));
	}else{
		$rssHeading = $blog->getCategoryHeaderTitle($app_pg);
	}

	 if($app_pg == null){
		$rssHeadingDesc = $blog->getCategoryHeaderDescription(sysConfig::get('EXTENSION_BLOG_DEFAULT_CATEGORY'));
	}else{
		$rssHeadingDesc = $blog->getCategoryHeaderDescription($app_pg);
	}


 	$items = '<?xml version="1.0" encoding="ISO-8859-1" ?>
					<rss version="2.0">
						<channel>
							<title>' . $rssHeading . '</title>
							<link>' . itw_app_link('appExt=blog&appPage=' . $app_pg, 'show_category', 'default') . '</link>
							<description><![CDATA[' . strip_tags(stripslashes($rssHeadingDesc)) . ']]></description>
							<language>' . Session::get('languages_code') . '</language>
							';

	foreach ($posts as $post){
		$items .= '<item>
						<title>' . $post['BlogPostsDescription'][Session::get('languages_id')]['blog_post_title']. '</title>
						<link>' . itw_app_link('appExt=blog', 'show_post', $post['BlogPostsDescription'][Session::get('languages_id')]['blog_post_seo_url']) . '</link>
						<description><![CDATA[' .  $post['BlogPostsDescription'][Session::get('languages_id')]['blog_post_text'] . ']]></description>';

			if (isset($articlesDateAdded)){
				$items .= '<pubDate>'. strftime(sysLanguage::getDateFormat('long'), strtotime($post['post_date'])) .'</pubDate>';
			}

			$items .= '</item>';
	}

	$items .= '</channel>
				</rss>';

 echo $items;
 itwExit();
	//die($items);

?>