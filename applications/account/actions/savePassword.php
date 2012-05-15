<?php
	$password_current = $_POST['password_current'];
	$password_new = $_POST['password_new'];
	$password_confirmation = $_POST['password_confirmation'];

	$error = false;

	if (strlen($password_current) < sysConfig::get('ENTRY_PASSWORD_MIN_LENGTH')) {
		$error = true;

		$messageStack->add('pageStack', sysLanguage::get('ENTRY_PASSWORD_CURRENT_ERROR'), 'error');
	} elseif (strlen($password_new) < sysConfig::get('ENTRY_PASSWORD_MIN_LENGTH')) {
		$error = true;

		$messageStack->add('pageStack', sysLanguage::get('ENTRY_PASSWORD_NEW_ERROR'), 'error');
	} elseif ($password_new != $password_confirmation) {
		$error = true;

		$messageStack->add('pageStack', sysLanguage::get('ENTRY_PASSWORD_NEW_ERROR_NOT_MATCHING'), 'error');
	}

	if ($error == false) {
		$check_customer_query = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchAssoc("select customers_password from customers where customers_id = '" . (int)$userAccount->getCustomerId() . "'");
		$check_customer = $check_customer_query[0];

		if (tep_validate_password($password_current, $check_customer['customers_password'])) {
			Doctrine_Manager::getInstance()
				->getCurrentConnection()
				->exec("update customers set customers_password = '" . tep_encrypt_password($password_new) . "' where customers_id = '" . (int)$userAccount->getCustomerId() . "'");

			Doctrine_Manager::getInstance()
				->getCurrentConnection()
				->exec("update customers_info set customers_info_date_account_last_modified = now() where customers_info_id = '" . (int)$userAccount->getCustomerId() . "'");

			$messageStack->addSession('pageStack', sysLanguage::get('SUCCESS_PASSWORD_UPDATED'), 'success');

			EventManager::attachActionResponse(itw_app_link(null, 'account', 'default', 'SSL'), 'redirect');
		} else {
			$error = true;

			$messageStack->add('pageStack', sysLanguage::get('ERROR_CURRENT_PASSWORD_NOT_MATCHING'), 'error');
		}
	}
?>