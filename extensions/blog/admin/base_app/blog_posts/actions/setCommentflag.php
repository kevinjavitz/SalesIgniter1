<?php
	if ( ($_GET['flag'] == '0') || ($_GET['flag'] == '1') ) {
		if (isset($_GET['cID'])) {
			tep_set_comment_status($_GET['cID'], $_GET['flag']);
			$messageStack->addSession('pageStack', 'Comment status has been updated.', 'success');
		}

	}
	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'flag'))).'#page-comments', 'redirect');
?>