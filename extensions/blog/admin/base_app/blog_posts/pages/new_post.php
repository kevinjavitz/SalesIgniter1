<?php
	$Posts = Doctrine_Core::getTable('BlogPosts');
	if (isset($_GET['pID']) && empty($_POST)){
		$Post = $Posts->findOneByPostId((int)$_GET['pID']);
	}else{
		$Post = $Posts->getRecord();
	}


	$languages = tep_get_languages();

	if (!isset($Post['post_status'])) $Post['post_status'] = '1';
	switch ($Post['post_status']) {
		case '0': $in_status = false; $out_status = true; break;
		case '1':
		default: $in_status = true; $out_status = false;
	}

	$saveButton = htmlBase::newElement('button')->setType('submit')->usePreset('save')->setText(sysLanguage::get('TEXT_BUTTON_SAVE'));
	$cancelButton = htmlBase::newElement('button')->usePreset('cancel');

	if (Session::exists('blog_categories_cancel_link') === true){
		$cancelButton->setHref(Session::get('blog_categories_cancel_link'));
	}else{
		$cancelButton->setHref(itw_app_link(tep_get_all_get_params(array('app', 'appName', 'action','pID')) .(isset($_GET['pID']) ? 'pID=' . $_GET['pID'] : ''), null, 'default'));
	}

?>
 <form name="new_post" action="<?php echo itw_app_link(tep_get_all_get_params(array('app', 'appName', 'action')) .'action=savePost' . ((int)$Post['post_id'] > 0 ? '&pID=' . $Post['post_id'] : ''));?>" method="post" enctype="multipart/form-data">
 <div style="position:relative;text-align:right;"><?php
 	echo  $saveButton->draw() . $cancelButton->draw();
 	echo '<div class="pageHeading" style="position:absolute;left:0;top:.5em;">' . (isset($_GET['pID']) ? 'Edit Post' : 'New Post') . '</div>';
 ?></div>
 <div style="position:relative;">
 <div id="tab_container">
    <ul>
     <li class="ui-tabs-nav-item"><a href="#page-2"><span><?php echo sysLanguage::get('TAB_DESCRIPTION');?></span></a></li>
     <li class="ui-tabs-nav-item"><a href="#page-categories"><span><?php echo 'Categories';?></span></a></li>
	 <li class="ui-tabs-nav-item"><a href="#page-comments"><span><?php echo 'Comments';?></span></a></li>
<?php
	$contents = EventManager::notifyWithReturn('NewPostTabHeader');
	if (!empty($contents)){
		foreach($contents as $content){
			echo $content;
		}
	}
?>
    </ul>
    <div id="page-2"><?php include(sysConfig::getDirFsCatalog(). 'extensions/blog/admin/base_app/blog_posts/pages_tabs/tab_description.php');?></div>
    <div id="page-categories"><?php include(sysConfig::getDirFsCatalog() . 'extensions/blog/admin/base_app/blog_posts/pages_tabs/tab_categories.php');?></div>
    <div id="page-comments"><?php include(sysConfig::getDirFsCatalog() . 'extensions/blog/admin/base_app/blog_posts/pages_tabs/tab_comments.php');?></div>
<?php
	$contents = EventManager::notifyWithReturn('NewPostTabBody', &$Post);
	if (!empty($contents)){
		foreach($contents as $content){
			echo $content;
		}
	}
?>
   </div>
  </div>
   <div style="position:relative;text-align:right;margin-top:.5em;margin-left:250px;"><?php
   if (Session::exists('blog_categories_cancel_link') === true){
   	echo tep_draw_hidden_field('blog_categories_save_redirect', Session::get('blog_categories_save_redirect'));
   }

   echo $saveButton->draw() . $cancelButton->draw();
   ?>
   </div></form>
