<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class InfoBoxBlogLatestArticles extends InfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('blogLatestArticles');

	}

	public function show(){
		global $appExtension;
		$boxWidgetProperties = $this->getWidgetProperties();
		$nr_art = $boxWidgetProperties->nr_art;
		$showImage = (isset($boxWidgetProperties->showImage)&& $boxWidgetProperties->showImage == 'showImage')?true:false;
		$showDate = (isset($boxWidgetProperties->showDate) && $boxWidgetProperties->showDate == 'showDate')?true:false;
		$showReadMore = (isset($boxWidgetProperties->showReadMore)&& $boxWidgetProperties->showReadMore=='showReadMore')?true:false;
		$descLength = $boxWidgetProperties->descLength;
		$blog = $appExtension->getExtension('blog');

		$app_pg = null;
		$pg = 1;
		$pg_limit  = $nr_art;
		$pagerBar = '';
		$posts = $blog->getCategoriesPosts(null, $app_pg, $pg_limit, $pg, &$pagerBar);

		$contentHtml = '';
		foreach ($posts as $post){
			$contentHtml.= '<div class="blogItem">';
			if($showImage){
				$cimg = preg_match('/<img[^>]+>/i',$post['BlogPostsDescription'][Session::get('languages_id')]['blog_post_text'], $result);
				$img = array();
				foreach( $result as $img_tag){
					preg_match('/(src)=("[^"]*")/i',$img_tag, $img);
					break;
				}
				//print_r($img);
				$src = str_replace('"','',(isset($img[2])?$img[2]:''));

				$thumbUrl = 'imagick_thumb.php?path=rel&imgSrc='.$src.'&width=175&height=175';
				$contentHtml.= '<div class="pictPart">';
				if($src !=''){
					$contentHtml.= '<img src="'.$thumbUrl.'"/>';
				}
				$contentHtml.= '</div>';
			}

			$contentHtml.= '<div class="textPart">';
			$contentHtml.= "<h2 class='blog_post_title'><a href='" . itw_app_link('appExt=blog', 'show_post', $post['BlogPostsDescription'][Session::get('languages_id')]['blog_post_seo_url']) . "'>" . $post['BlogPostsDescription'][Session::get('languages_id')]['blog_post_title'] . "</a></h2>";
			if($showDate){
				$contentHtml.= "<p class='blog_post_foot'>" . "Posted: " . strftime(sysLanguage::getDateFormat('long'), strtotime($post['post_date'])) . "</p>";
			}
			if($descLength > 0){
				$contentHtml.= "<div class='blog_post_text'>" . substr(strip_tags($post['BlogPostsDescription'][Session::get('languages_id')]['blog_post_text']),0,$descLength).'... ';
				if($showReadMore){
					$contentHtml.= "<a style='color:red;font-weight:bold;' href='" . itw_app_link('appExt=blog', 'show_post', $post['BlogPostsDescription'][Session::get('languages_id')]['blog_post_seo_url']) . "'>". 'Read More' . "</a>"; //sysLanguage::get('INFOBOX_BLOGLATESTARTICLES_TEXT_READ_MORE')
				}
				$contentHtml.= "</div>";
			}
			$contentHtml.= '</div>';
			$contentHtml.= '<br style="clear:both"/>';
			$contentHtml.= '</div>';
		}

		$this->setBoxContent($contentHtml);
		return $this->draw();

	}
}