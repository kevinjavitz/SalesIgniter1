<?php
/*
	Blog Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/
	$blog = $appExtension->getExtension('blog');
	$langId = Session::get('languages_id');

	$pageBlog = $blog->getPosts($langId, $_GET['appPage']);
	$contentHeading = $pageBlog['BlogPostsDescription'][$langId]['blog_post_title'];
	$contentHtml = $pageBlog['BlogPostsDescription'][$langId]['blog_post_text'];

	$theComments = '';
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
		->setSource(tep_href_link('securimage_show.php', session_name() . '=' . session_id()));
	}

	$submitButton = htmlBase::newElement('button')
	->setName('submit')
	->setType('submit')
	->setText('Post Comment');

	$commentForm->append($post_seo)
	->append($post_name)
	->append($comment_author_name)
	->append($comment_author_email)
	->append($comment_text)
	->append($comment_captcha)
	->append($captcha_img)
	->append($submitButton);

	$contentHeading = stripslashes($contentHeading);
	$contentHtml = stripslashes($contentHtml);
	$contentHtml .= '<br/><br/>'. $commentForm->draw();
	$contentHtml .= "<p>Comments: </p>" . stripslashes($theComments);

	$pageTitle = stripslashes($contentHeading);
	$pageContents = $contentHtml;

	$pageButtons = htmlBase::newElement('button')
	->usePreset('continue')
	->setHref(itw_app_link(null, 'index', 'default'))
	->draw();

	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
