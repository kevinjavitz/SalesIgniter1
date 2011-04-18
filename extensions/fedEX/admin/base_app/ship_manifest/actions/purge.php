<?php
    if ($_GET['pickup_date']) {
		$pickup_date = $_GET['pickup_date'];
	}else {
		$latest_date = tep_db_query("select pickup_date from " . TABLE_SHIPPING_MANIFEST . " order by pickup_date desc limit 1");
		$pickup_date = tep_db_fetch_array($latest_date);
		$pickup_date = $pickup_date['pickup_date'];
	}

	$purge_manifest_date = tep_db_query("delete from " . TABLE_SHIPPING_MANIFEST . " where pickup_date = '" . $pickup_date . "'");
    EventManager::attachActionResponse(itw_app_link('appExt=fedEX','ship_manifest','default'), 'redirect'); 	

?>