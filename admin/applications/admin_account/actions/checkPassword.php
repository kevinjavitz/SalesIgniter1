<?php
	$Qcheck = Doctrine_Query::create()
	->select('admin_password')
	->from('Admin')
	->where('admin_id = ?', $_POST['id_info']);

	$Admin = $Qcheck->fetchOne();

	// Check that password is good
	if (!tep_validate_password($_POST['password_confirmation'], $Admin['admin_password'])) {
		$messageStack->addSession('pageStack', sysLanguage::get('TEXT_INFO_INTRO_CONFIRM_PASSWORD_ERROR'), 'error');
		$redirectAppPage = 'default';
	} else {
		Session::set('confirm_account', true);
		$redirectAppPage = 'editAccount';
	}

	EventManager::attachActionResponse(itw_app_link(null, null, $redirectAppPage, 'SSL'), 'redirect');
?>