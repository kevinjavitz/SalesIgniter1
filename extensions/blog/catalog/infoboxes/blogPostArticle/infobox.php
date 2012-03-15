<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class InfoBoxBlogPostArticle extends InfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('blogPostArticle');

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

		$imageWidth = $boxWidgetProperties->image_width;
		$imageHeight = $boxWidgetProperties->image_height;
		$showImage = (isset($boxWidgetProperties->showImage)&& $boxWidgetProperties->showImage == 'showImage')?true:false;
		$showTitle = (isset($boxWidgetProperties->showTitle)&& $boxWidgetProperties->showTitle == 'showTitle')?true:false;
		$showVideo = (isset($boxWidgetProperties->showVideo)&& $boxWidgetProperties->showVideo == 'showVideo')?true:false;
		$showVideoImage = (isset($boxWidgetProperties->showVideoImage)&& $boxWidgetProperties->showVideoImage == 'showVideoImage')?true:false;
		$showDate = (isset($boxWidgetProperties->showDate) && $boxWidgetProperties->showDate == 'showDate')?true:false;
		$showDesc = (isset($boxWidgetProperties->showDesc)&& $boxWidgetProperties->showDesc=='showDesc')?true:false;
		$imageHasLink = (isset($boxWidgetProperties->imageHasLink)&& $boxWidgetProperties->imageHasLink=='imageHasLink')?true:false;
		$descLength = $boxWidgetProperties->descLength;

		$blog = $appExtension->getExtension('blog');

		$langId = Session::get('languages_id');

		$pageBlog = $blog->getPosts($langId, $_GET['appPage']);
		if(!empty($pageBlog['post_redirect_url'])){
			tep_redirect($pageBlog['post_redirect_url']);
		}
		$contentHeading = $pageBlog['BlogPostsDescription'][$langId]['blog_post_title'];
		$contentHtml = '';

		if(!empty($pageBlog['post_full_featured_image'])){
			$thumbUrl = 'imagick_thumb.php?path=rel&imgSrc=images/'.$pageBlog['post_full_featured_image'];
			$contentHtml .= '<div class="blogpostimage"><img src="'.$thumbUrl.'"/></div>';
		}
		$contentHtml .= '<div class="blogposttextPart">';
		if($showTitle){
			$contentHtml .= "<h1 class='blogposttitle'>" . $pageBlog['BlogPostsDescription'][$langId]['blog_post_title'] . "</h1>";
		}

		$contentHtml .= '<div class="blogpostdescription">'.$pageBlog['BlogPostsDescription'][$langId]['blog_post_text'].'</div>';

		$contentHtml .= '<br style="clear:both;"/>';
		$contentHtml .= '</div>';

		$theComments = '<a name="comments"></a>';
		if(sysConfig::get('EXTENSION_BLOG_ENABLE_COMMENTS') == 'True'){

			foreach ($pageBlog['BlogCommentToPost'] as $comments){
				if($comments['BlogComments']['comment_status'] == 1){
					$theComments .= "Author: <a href='mailto: ".$comments['BlogComments']['comment_email']."'>".$comments['BlogComments']['comment_author']."</a><br/>". $comments['BlogComments']['comment_text']."<br/>";
				}

			}
		}

		$commentForm = htmlBase::newElement('form')
			->attr('name','commentf')
			->attr('id','commentf')
			->attr('method','post')
			->attr('action',itw_app_link('appExt=blog&action=saveComment', 'show_post', 'default'));

		$post_seo = htmlBase::newElement('input')
			->setName('post_seo')
			->setType('hidden')
			->setValue($pageBlog['post_id']);

		$post_name = htmlBase::newElement('input')
			->setName('post_name')
			->setType('hidden')
			->setValue( $pageBlog['BlogPostsDescription'][$langId]['blog_post_seo_url']);

		//if user registered this fields will be hidden and filled with the registered user data

		$comment_author_name = htmlBase::newElement('input')
			->setName('comment_author')
			->addClass('comf')
			->setLabelPosition('before')
			->setLabel('Author');

		$comment_author_email = htmlBase::newElement('input')
			->setName('comment_email')
			->addClass('comf')
			->setLabelPosition('before')
			->setLabel('Email:');

		$comment_text = htmlBase::newElement('textarea')
			->setName('comment_text')
			->addClass('comf')
			->setRows(10)
			->setCols(20)
			->setLabelPosition('before')
			->setLabel('Comment:<br/>')
			->addClass('makeFCK');

		if (sysConfig::get('EXTENSION_BLOG_ENABLE_CAPTCHA') == 'True'){
			$comment_captcha = htmlBase::newElement('input')
				->setName('comment_captcha')
				->addClass('comf')
				->setLabelPosition('before')
				->setLabel(sysLanguage::get('PLEASE_FILL_IN_CAPTCHA'));
			$captcha_img = htmlBase::newElement('image')
				->addClass('captcha_img')
				->setSource(sysConfig::getDirWsCatalog(). 'securimage_show.php');
		}

		$submitButton = htmlBase::newElement('button')
			->setName('submit')
			->setType('submit')
			->setText('Post Comment');

		$commentForm->append($post_seo)
			->append($post_name);
		if(sysConfig::get('EXTENSION_BLOG_ENABLE_COMMENTS') == 'True'){

			$commentForm->append($comment_author_name)
				->append($comment_author_email)
				->append($comment_text);
			if (sysConfig::get('EXTENSION_BLOG_ENABLE_CAPTCHA') == 'True'){
				$commentForm->append($comment_captcha)
					->append($captcha_img);
			}
			$commentForm->append($submitButton);
		}

		$contentHeading = stripslashes($contentHeading);
		if(sysConfig::get('EXTENSION_BLOG_ENABLE_COMMENTS') == 'True'){
			$contentHtml .= "<p>Comments: </p>" . stripslashes($theComments);
			$contentHtml .= '<br/><br style="clear:both;"/><div id="addComment">Add Comment +</div><div id="commentDiv">'. $commentForm->draw().'</div>';
		}


		//$this->setBoxHeading($contentHeading);
		$this->setBoxContent($contentHtml);
		return $this->draw();

	}
}