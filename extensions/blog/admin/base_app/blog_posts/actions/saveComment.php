<?php

	$comment_date = tep_db_prepare_input($_POST['comment_date']);
	$Comments = Doctrine_Core::getTable('BlogCommentToPost');

	if (isset($_GET['cID'])){
		$Comment = $Comments->findOneByBlogCommentId((int)$_GET['cID']);
	}else{
		$Comment = $Comments->create();
		$Comment->BlogComments['blog_post_id'] = $_GET['pID'];
	}

	$Comment->BlogComments['comment_date'] = $comment_date;
	$Comment->BlogComments['comment_status'] = $_POST['comment_status'];
	$Comment->BlogComments['comment_author'] = $_POST['comment_author'];
	$Comment->BlogComments['comment_email'] = $_POST['comment_email'];
	$Comment->BlogComments['comment_text'] = $_POST['comment_text'];
	$Comment->save();


	$link = itw_app_link(tep_get_all_get_params(array('action')) , null, 'new_post');
	EventManager::attachActionResponse($link, 'redirect');
?>