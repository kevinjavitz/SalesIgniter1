<?php

$valid = true;
if(sysConfig::get('EXTENSION_BLOG_ENABLE_CAPTCHA') == 'True'){
		  include('captcha/securimage.php');
          $img = new Securimage();
          $valid = $img->check($_POST['comment_captcha']);
}
if($valid){
	$comment_date = date('Y-m-d');
	$Comments = Doctrine_Core::getTable('BlogCommentToPost');
	$Comment = $Comments->create();


	$Comment->blog_post_id = (int) $_POST['post_seo'];
	$Commentt = & $Comment->BlogComments;
	$Commentt->comment_date = $comment_date;
	$Commentt->comment_status = 0;
	$Commentt->comment_text = $_POST['comment_text'];
	$Commentt->comment_author = $_POST['comment_author'];
	$Commentt->comment_email = $_POST['comment_email'];
	$Comment->save();
	$emailEvent = new emailEvent('blog_comment', Session::get('languages_id'));
	$link = itw_admin_app_link('appExt=blog&pID='.$_POST['post_seo'].'&cID='.$Commentt->comment_id,'blog_posts','new_post').'#page-comments';
	$emailEvent->setVars(array(
			'link' => $link,
		));

	$emailEvent->sendEmail(array(
			'email' => sysConfig::get('STORE_OWNER_EMAIL_ADDRESS'),
			'name' => sysConfig::get('STORE_OWNER')
		));
	$messageStack->addSession('pageStack', 'Thank you, if approved your comment will be posted', 'success');
}else{
	$messageStack->addSession('pageStack', 'Sorry, the code entered was invalid', 'error');
}

	$link = itw_app_link("appExt=blog", 'show_post', $_POST['post_name']);
	EventManager::attachActionResponse($link, 'redirect');
?>