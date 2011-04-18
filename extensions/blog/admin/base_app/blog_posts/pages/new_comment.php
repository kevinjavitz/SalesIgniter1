<?php
	$Comments = Doctrine_Core::getTable('BlogCommentToPost');
	if (isset($_GET['cID']) && empty($_POST)){
		$Comment = $Comments->findOneByBlogCommentId((int)$_GET['cID']);
	}else{
		$Comment = $Comments->getRecord();
	}

	$saveButton = htmlBase::newElement('button')->setType('submit')->usePreset('save')->setText(sysLanguage::get('TEXT_BUTTON_SAVE'));
	$cancelButton = htmlBase::newElement('button')->usePreset('cancel');

	if (Session::exists('blog_categories_cancel_link') === true){
		$cancelButton->setHref(Session::get('blog_categories_cancel_link'));
	}else{
		$cancelButton->setHref(itw_app_link(tep_get_all_get_params(array('app', 'appName', 'action','cID')) .(isset($_GET['cID']) ? 'cID=' . $_GET['cID'] : ''), null, 'new_post'));
	}

?>
 <form name="new_comment" action="<?php echo itw_app_link(tep_get_all_get_params(array('app', 'appName', 'action')) .'action=saveComment' . ((int)$Comment['blog_comment_id'] > 0 ? '&cID=' . $Comment['blog_comment_id'] : ''));?>" method="post" enctype="multipart/form-data">
 <div style="position:relative;text-align:right;"><?php
 	echo  $saveButton->draw() . $cancelButton->draw();
 	echo '<div class="pageHeading" style="position:absolute;left:0;top:.5em;">' . (isset($_GET['cID']) ? 'Edit Comment' : 'New Comment') . '</div>';
 ?></div>
 <div style="position:relative;">
 <div id="tab_container">
    <ul>

	 <li class="ui-tabs-nav-item"><a href="#page-comments"><span><?php echo 'Comments';?></span></a></li>
    </ul>
    <div id="page-comments"><?php include(sysConfig::getDirFsCatalog() . 'extensions/blog/admin/base_app/blog_posts/pages_tabs/tab_comments_edit.php');?></div>

   </div>
  </div>
   <div style="position:relative;text-align:right;margin-top:.5em;margin-left:250px;"><?php

   echo $saveButton->draw() . $cancelButton->draw();
   ?>
   </div></form>
