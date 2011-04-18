<?php
	if (isset($_POST['newsletter_general']) && is_numeric($_POST['newsletter_general'])) {
		$newsletter_general = tep_db_prepare_input($_POST['newsletter_general']);
	} else {
		$newsletter_general = '0';
	}

	if ($newsletter_general != $newsletter['customers_newsletter']) {
		$newsletter_general = (($newsletter['customers_newsletter'] == '1') ? '0' : '1');

		tep_db_query("update " . TABLE_CUSTOMERS . " set customers_newsletter = '" . (int)$newsletter_general . "' where customers_id = '" . (int)$userAccount->getCustomerId() . "'");
	}

	$messageStack->addSession('pageStack', sysLanguage::get('SUCCESS_NEWSLETTER_UPDATED'), 'success');

	EventManager::attachActionResponse(itw_app_link(null, 'account', 'default', 'SSL'), 'redirect');
?>