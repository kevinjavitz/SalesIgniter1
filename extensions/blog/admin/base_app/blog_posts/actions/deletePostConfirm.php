<?php
	if (isset($_GET['post_id'])){
		$Posts = Doctrine_Core::getTable('BlogPosts')->findOneByPostId($_GET['post_id']);
		if ($Posts){
			$Posts->delete();
			$messageStack->addSession('pageStack', 'Post has been removed', 'success');
		}
	}



	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'post_id'))), 'redirect');
?>