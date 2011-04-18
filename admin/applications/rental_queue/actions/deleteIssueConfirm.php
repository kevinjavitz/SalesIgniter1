<?php
	$_GET['tID'] = tep_db_prepare_input($_GET['tID']);

	tep_db_query("delete from " . TABLE_RENTAL_ISSUES . " where issue_id = '" . (int)$_GET['tID'] . "'");

	EventManager::attachActionResponse(itw_app_link('page=' . $_GET['page'], 'rental_queue', 'issues'));
?>