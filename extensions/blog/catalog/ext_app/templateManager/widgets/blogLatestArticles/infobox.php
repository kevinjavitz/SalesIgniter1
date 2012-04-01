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
		$this->init('blogLatestArticles', __DIR__);

	}

	public function video_image($url){
		$image_url = parse_url($url);
		if($image_url['host'] == 'www.youtube.com' || $image_url['host'] == 'youtube.com'){
			$array = explode("&", $image_url['query']);
			return "http://img.youtube.com/vi/".substr($array[0], 2)."/0.jpg";
		} else if($image_url['host'] == 'www.vimeo.com' || $image_url['host'] == 'vimeo.com'){
			$hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".substr($image_url['path'], 1).".php"));
			return $hash[0]["thumbnail_small"];
		}
	}

	public function video_from_url($url, $imgWidth, $imgHeight){
		parse_str(parse_url($url,PHP_URL_QUERY), $out);
		if($out['host'] == 'www.youtube.com' || $out['host'] == 'youtube.com'){
			$string = '<object width="'.$imgWidth.'" height="'.$imgHeight.'" data="http://www.youtube.com/v/'.$out['v'].'" type="application/x-shockwave-flash"><param name="src" value="http://www.youtube.com/v/'.$out['v'].'" /></object>';
		}else{
			preg_match('#http://(?:\w+.)?vimeo.com/(?:video/|moogaloop\.swf\?clip_id=)(\w+)#i', $url, $match);
			$string = '<object width="'.$imgWidth.'" height="'.$imgHeight.'"><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id='.$match[1].'&server=vimeo.com&show_title=1&show_byline=1&show_portrait=0&color=00adef&fullscreen=1" /><embed src="http://vimeo.com/moogaloop.swf?clip_id='.$match[1].'&server=vimeo.com&show_title=1&show_byline=1&show_portrait=0&color=00adef&fullscreen=1" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="'.$imgWidth.'" height="'.$imgHeight.'"></embed></object>';
		}

		return $string;
	}

	public function show(){
		global $appExtension;
		$boxWidgetProperties = $this->getWidgetProperties();
		$nr_art = $boxWidgetProperties->nr_art;
		$imageWidth = $boxWidgetProperties->image_width;
		$imageHeight = $boxWidgetProperties->image_height;
		$showImage = (isset($boxWidgetProperties->showImage)&& $boxWidgetProperties->showImage == 'showImage')?true:false;
		$showTitle = (isset($boxWidgetProperties->showTitle)&& $boxWidgetProperties->showTitle == 'showTitle')?true:false;
		$showVideo = (isset($boxWidgetProperties->showVideo)&& $boxWidgetProperties->showVideo == 'showVideo')?true:false;
		$showVideoImage = (isset($boxWidgetProperties->showVideoImage)&& $boxWidgetProperties->showVideoImage == 'showVideoImage')?true:false;
		$showDate = (isset($boxWidgetProperties->showDate) && $boxWidgetProperties->showDate == 'showDate')?true:false;
		$showReadMore = (isset($boxWidgetProperties->showReadMore)&& $boxWidgetProperties->showReadMore=='showReadMore')?true:false;
		$showDesc = (isset($boxWidgetProperties->showDesc)&& $boxWidgetProperties->showDesc=='showDesc')?true:false;
		$imageHasLink = (isset($boxWidgetProperties->imageHasLink)&& $boxWidgetProperties->imageHasLink=='imageHasLink')?true:false;
		$descLength = $boxWidgetProperties->descLength;
		$blog = $appExtension->getExtension('blog');

		$app_pg = null;
		$pg = 1;
		$pg_limit  = $nr_art;
		$pagerBar = '';
		$posts = $blog->getCategoriesPosts(null, $app_pg, $pg_limit, $pg, &$pagerBar, (isset($boxWidgetProperties->new_selected_category)?$boxWidgetProperties->new_selected_category:'-1'));

		$contentHtml = '';
		foreach ($posts as $post){
			$contentHtml.= '<div class="blogItem">';
			if($showImage){
				if(empty($post['post_featured_image'])){
					$cimg = preg_match('/<img[^>]+>/i',$post['BlogPostsDescription'][Session::get('languages_id')]['blog_post_text'], $result);
					$img = array();
					foreach( $result as $img_tag){
						preg_match('/(src)=("[^"]*")/i',$img_tag, $img);
						break;
					}
					//print_r($img);
					$src = str_replace('"','',(isset($img[2])?$img[2]:''));

					$thumbUrl = 'imagick_thumb.php?path=rel&imgSrc='.$src.'&width='.$imageWidth.'&height='.$imageHeight;
				}else{
					$thumbUrl = 'imagick_thumb.php?path=rel&imgSrc='.$post['post_featured_image'].'&width='.$imageWidth.'&height='.$imageHeight;
				}
				$contentHtml.= '<div class="pictPart">';
				if($thumbUrl !=''){
					if($imageHasLink){
						$contentHtml.= "<a class='blogimageLink' href='" . itw_app_link('appExt=blog', 'show_post', $post['BlogPostsDescription'][Session::get('languages_id')]['blog_post_seo_url']) . "'>";
					}
					$contentHtml.= '<img src="'.$thumbUrl.'"/>';
					if($imageHasLink){
						$contentHtml.= '</a>';
					}
				}
				$contentHtml.= '</div>';

			}elseif($showVideo){
				if(!empty($post['post_featured_media'])){
					$contentHtml.= '<div class="pictPart">';
					$contentHtml.= $this->video_from_url($post['post_featured_media'], $imageWidth, $imageHeight);
					$contentHtml.= '</div>';
				}
			}

			$contentHtml.= '<div class="textPart">';
			if($showTitle){
				$contentHtml.= "<h2 class='blog_post_title'><a href='" . itw_app_link('appExt=blog', 'show_post', $post['BlogPostsDescription'][Session::get('languages_id')]['blog_post_seo_url']) . "'>" . $post['BlogPostsDescription'][Session::get('languages_id')]['blog_post_title'] . "</a></h2>";
			}
			if($showDate){
				$contentHtml.= "<p class='blog_post_foot'>" . "Posted: " . strftime(sysLanguage::getDateFormat('long'), strtotime($post['post_date'])) . "</p>";
			}
			$contentHtml.= "<div class='blog_post_text'>";
			if($descLength > 0 && $showDesc){
				$contentHtml.= substr(strip_tags($post['BlogPostsDescription'][Session::get('languages_id')]['blog_post_text'],'<a>'),0,$descLength).((strlen(strip_tags($post['BlogPostsDescription'][Session::get('languages_id')]['blog_post_text'],'<a>')) > $descLength)?'... ':'');
			}

			if($showReadMore){
				$contentHtml.= "<a style='color:red;font-weight:bold;' class='blogReadMore' href='" . itw_app_link('appExt=blog', 'show_post', $post['BlogPostsDescription'][Session::get('languages_id')]['blog_post_seo_url']) . "'>". 'go check here >>' . "</a>"; //sysLanguage::get('INFOBOX_BLOGLATESTARTICLES_TEXT_READ_MORE')
			}
			$contentHtml.= "</div>";

			$contentHtml.= '</div>';
			$contentHtml.= '<br style="clear:both"/>';
			$contentHtml.= '</div>';
		}

		$this->setBoxContent($contentHtml);
		return $this->draw();

	}
}