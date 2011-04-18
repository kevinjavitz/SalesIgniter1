<?php
	if ( ($_GET['flag'] == '0') || ($_GET['flag'] == '1') ) {
		if (isset($_GET['aID'])) {
			tep_set_article_status($_GET['aID'], $_GET['flag']);
		}
	}

	EventManager::attachActionResponse(itw_app_link('appExt=articleManager&aID=' . $_GET['aID'], 'articles', 'default'), 'redirect');
?>