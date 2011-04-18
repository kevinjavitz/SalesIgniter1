<?php
	if ( ($_GET['flag'] == '0') || ($_GET['flag'] == '1') ) {
		if (isset($_GET['pID'])) {
			tep_set_product_status($_GET['pID'], $_GET['flag']);
			$messageStack->addSession('pageStack', 'Product status has been updated.', 'success');
		}

		if (USE_CACHE == 'true') {
			tep_reset_cache_block('categories');
			tep_reset_cache_block('also_purchased');
		}
	}
	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'flag'))), 'redirect');
?>