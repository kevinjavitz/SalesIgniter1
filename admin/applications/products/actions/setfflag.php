<?php
	if ( ($_GET['fflag'] == '0') || ($_GET['fflag'] == '1') ) {
		if (isset($_GET['pID'])) {
			tep_set_product_featured($_GET['pID'], $_GET['fflag']);
			$messageStack->addSession('pageStack', 'Product featured status has been updated.', 'success');
		}

		if (USE_CACHE == 'true') {
			tep_reset_cache_block('categories');
			tep_reset_cache_block('also_purchased');
		}
	}

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'fflag'))), 'redirect');
?>