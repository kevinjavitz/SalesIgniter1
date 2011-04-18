<?php
	if ( ($_GET['flag'] == '0') || ($_GET['flag'] == '1') ) {
		if (isset($_GET['bID'])) {
			tep_set_banners_status($_GET['bID'], $_GET['flag']);
			$messageStack->addSession('pageStack', 'Banner status has been updated.', 'success');
		}
	}
	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'flag'))), 'redirect');
?>