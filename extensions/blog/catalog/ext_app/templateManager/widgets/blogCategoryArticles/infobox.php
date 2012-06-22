<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class InfoBoxBlogCategoryArticles extends InfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('blogCategoryArticles', __DIR__);

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
		global $appExtension, $pageContent;
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
		$showParentCat = (isset($boxWidgetProperties->showParentCat)&& $boxWidgetProperties->showParentCat=='showParentCat')?true:false;
		$descLength = $boxWidgetProperties->descLength;
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


		$pg_limit = (int) $nr_art;

		$pagerBar = '';
		$posts = $blog->getPostsWithPaging(null, $app_pg, $pg_limit, $pg, &$pagerBar);

		$contentHtml = '';
		foreach ($posts as $post){
			$contentHtml.= '<div class="blogPostItem">';
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
					$thumbUrl = 'imagick_thumb.php?path=rel&imgSrc='.$blog->getFilesUploadPath('image', 'rel'). $post['post_featured_image'].'&width='.$imageWidth.'&height='.$imageHeight;
				}
				$contentHtml.= '<div class="pictPostPart">';
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
					$contentHtml.= '<div class="pictPostPart">';
					$contentHtml.= $this->video_from_url($post['post_featured_media'], $imageWidth, $imageHeight);
					$contentHtml.= '</div>';
				}
			}

			$contentHtml.= '<div class="textPostPart">';
			if($showTitle){
				$contentHtml.= "<h2 class='blog_post_title'><a href='" . itw_app_link('appExt=blog', 'show_post', $post['BlogPostsDescription'][Session::get('languages_id')]['blog_post_seo_url']) . "'>" . $post['BlogPostsDescription'][Session::get('languages_id')]['blog_post_title'] . "</a></h2>";
			}
			if($showDate){
				$contentHtml.= "<p class='blog_post_foot'>" . "Posted: " . strftime(sysLanguage::getDateFormat('long'), strtotime($post['post_date'])) . "</p>";
			}
			$contentHtml.= "<div class='blog_post_text'>";
			if($descLength > 0 && $showDesc){
				$contentHtml.= substr(strip_tags($post['BlogPostsDescription'][Session::get('languages_id')]['blog_post_text'],''),0,$descLength).((strlen(strip_tags($post['BlogPostsDescription'][Session::get('languages_id')]['blog_post_text'],'')) > $descLength)?'... ':'');
			}
			$contentHtml.= "</div>";
			if($showReadMore){
				$contentHtml.= "<div class='readMoreDiv'><a style='color:red;font-weight:bold;' class='blogReadMore' href='" . itw_app_link('appExt=blog', 'show_post', $post['BlogPostsDescription'][Session::get('languages_id')]['blog_post_seo_url']) . "'>". 'read more >>' . "</a></div>"; //sysLanguage::get('INFOBOX_BLOGLATESTARTICLES_TEXT_READ_MORE')
			}

			$contentHtml.= '</div>';
			$contentHtml.= '<br style="clear:both"/>';
			$contentHtml.= '</div>';
		}


		/*
		$contentHtml = '';
		foreach ($posts as $post){
			$categ = '';
			$postCategories = $blog->getPostCategories($post['post_id']);
			$Qcomments = Doctrine_Query::create()
				->from('BlogCommentToPost c')
				->leftJoin('c.BlogComments pc')
				->where('c.blog_post_id = ?', $post['post_id'])
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			foreach ($postCategories as $cat){
				$categ .= $cat['BlogCategories']['BlogCategoriesDescription'][Session::get('languages_id')]['blog_categories_title'] . ', ';
			}
			$categ = substr($categ, 0, strlen($categ) - 2);

			$contentHtml.= "<h2 class='blog_post_title'><a href='" . itw_app_link('appExt=blog', 'show_post', $post['BlogPostsDescription'][Session::get('languages_id')]['blog_post_seo_url']) . "'>" . $post['BlogPostsDescription'][Session::get('languages_id')]['blog_post_title'] . "</a></h2>";
			$contentHtml.= "<div class='blog_post_text'>" . $post['BlogPostsDescription'][Session::get('languages_id')]['blog_post_text'] . "</div>";
			$contentHtml.= "<p class='blog_post_foot'>" . "Date: " . tep_date_short($post['post_date']) . "<br/>Categories: " . $categ. "<br/>" . count($Qcomments).' Comments( <a href="'.itw_app_link('appExt=blog', 'show_post', $post['BlogPostsDescription'][Session::get('languages_id')]['blog_post_seo_url']).'#comments">click here to post a comment</a>)' . "</p>";
		}
		*/

		if ($app_pg == null){
			$contentHeading = "Blog";
		}else{
			$contentHeading = $blog->getCategoryHeaderTitle($app_pg);
		}

		$contentHtml .= "<br/><br/>".$pagerBar;

		$QCurCategory = Doctrine_Query::create()
		->from('BlogCategories c')
		->leftJoin('c.BlogCategoriesDescription cd')
		->where('cd.blog_categories_seo_url = ?', $app_pg)
		->andWhere('cd.language_id = ?', Session::get('languages_id'))
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		if(isset($QCurCategory[0])){

			$QParentCategory = Doctrine_Query::create()
			->from('BlogCategories c')
			->leftJoin('c.BlogCategoriesDescription cd')
			->where('c.blog_categories_id = ?', $QCurCategory[0]['parent_id'])
			->andWhere('cd.language_id = ?', Session::get('languages_id'))
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		}

		$parentCat = '';
		if(isset($QParentCategory[0])){
			$parentCat = $QParentCategory[0]['BlogCategoriesDescription'][0]['blog_categories_title'];
		}
		if($showParentCat){
			$this->setBoxHeading('<div class="blogCategoryParentCat">'.$parentCat.'</div><div class="blogCategorySubCat">'.$contentHeading.'</div>');
		}else{
			$this->setBoxHeading($contentHeading);
		}
		$this->setBoxContent($contentHtml);
		return $this->draw();

	}
}