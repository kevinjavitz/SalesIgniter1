<?php
	$link = itw_app_link(null, 'index', 'default');

	$banner_query = tep_db_query("select banners_url from " . TABLE_BANNERS . " where banners_id = '" . (int)$_GET['goto'] . "'");
	if (tep_db_num_rows($banner_query)) {
		$banner = tep_db_fetch_array($banner_query);
		tep_update_banner_click_count($_GET['goto']);

		$link = $banner['banners_url'];
	}
	
	EventManager::attachActionResponse($link, 'redirect');
?>