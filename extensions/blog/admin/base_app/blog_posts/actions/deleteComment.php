<?php
	if (isset($_GET['cID'])){
		$Comment = Doctrine_Core::getTable('BlogCommentToPost')->findOneByBlogCommentId($_GET['cID']);
		if ($Comment){
			$Comment->delete();
			$messageStack->addSession('pageStack', 'Comment has been removed', 'success');
		}
	}

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'cID'))), 'redirect');
?>