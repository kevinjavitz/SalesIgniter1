<?php
	$link = itw_app_link(null, 'index', 'default');
	if (isset($_GET['manufacturers_id']) && tep_not_null($_GET['manufacturers_id'])) {
		$manufacturer_query = tep_db_query("select manufacturers_url from " . TABLE_MANUFACTURERS_INFO . " where manufacturers_id = '" . (int)$_GET['manufacturers_id'] . "' and languages_id = '" . (int)Session::get('languages_id') . "'");
		if (tep_db_num_rows($manufacturer_query)) {
			// url exists in selected language
			$manufacturer = tep_db_fetch_array($manufacturer_query);

			if (tep_not_null($manufacturer['manufacturers_url'])) {
				tep_db_query("update " . TABLE_MANUFACTURERS_INFO . " set url_clicked = url_clicked+1, date_last_click = now() where manufacturers_id = '" . (int)$_GET['manufacturers_id'] . "' and languages_id = '" . (int)Session::get('languages_id') . "'");

				$link = $manufacturer['manufacturers_url'];
			}
		} else {
			// no url exists for the selected language, lets use the default language then
			$manufacturer_query = tep_db_query("select mi.languages_id, mi.manufacturers_url from " . TABLE_MANUFACTURERS_INFO . " mi, " . TABLE_LANGUAGES . " l where mi.manufacturers_id = '" . (int)$_GET['manufacturers_id'] . "' and mi.languages_id = l.languages_id and l.code = '" . DEFAULT_LANGUAGE . "'");
			if (tep_db_num_rows($manufacturer_query)) {
				$manufacturer = tep_db_fetch_array($manufacturer_query);

				if (tep_not_null($manufacturer['manufacturers_url'])) {
					tep_db_query("update " . TABLE_MANUFACTURERS_INFO . " set url_clicked = url_clicked+1, date_last_click = now() where manufacturers_id = '" . (int)$_GET['manufacturers_id'] . "' and languages_id = '" . (int)$manufacturer['languages_id'] . "'");

					$link = $manufacturer['manufacturers_url'];
				}
			}
		}
	}
	
	EventManager::attachActionResponse($link, 'redirect');
?>