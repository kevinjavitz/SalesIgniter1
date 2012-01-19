<?php
	$log_times = $_POST['log_times']+1;
	Session::set('password_forgotten_tries', $log_times);

	// Check if email exists
	$redirectLink = itw_app_link();
	$Qcheck = Doctrine_Query::create()
	->select('admin_id, admin_firstname, admin_lastname, admin_email_address')
	->from('Admin')
	->where('admin_email_address = ?', $_POST['email_address'])
	->fetchOne();
	if ($Qcheck === false) {
		$messageStack->addSession('pageStack', sysLanguage::get('TEXT_FORGOTTEN_ERROR'), 'error');
	}else{
		if ($Qcheck['admin_firstname'] != $_POST['firstname']){
			$messageStack->addSession('pageStack', sysLanguage::get('TEXT_FORGOTTEN_ERROR'), 'error');
		}else{
			$messageStack->addSession('pageStack', sysLanguage::get('TEXT_FORGOTTEN_SUCCESS'), 'success');

			function randomize() {
				$salt = "ABCDEFGHIJKLMNOPQRSTUVWXWZabchefghjkmnpqrstuvwxyz0123456789";
				srand((double)microtime()*1000000);
				$i = 0;
				
				while ($i <= 7) {
					$num = rand() % 33;
					$tmp = substr($salt, $num, 1);
					$pass = $pass . $tmp;
					$i++;
				}
				return $pass;
			}
			$makePassword = randomize();
			
			tep_mail(
				$Qcheck['admin_firstname'] . ' ' . $Qcheck['admin_lastname'],
				$Qcheck['admin_email_address'],
				sysLanguage::get('ADMIN_EMAIL_SUBJECT'),
				sprintf(
					sysLanguage::get('ADMIN_EMAIL_TEXT'),
					$Qcheck['admin_firstname'],
					sysConfig::get('HTTP_SERVER') . sysConfig::getDirWsAdmin(),
					$Qcheck['admin_email_address'],
					$makePassword,
					sysConfig::get('STORE_OWNER')
				),
				sysConfig::get('STORE_OWNER'),
				sysConfig::get('STORE_OWNER_EMAIL_ADDRESS')
			);
			
			$Admin = Doctrine_Core::getTable('Admin')->findOneByAdminId($Qcheck['admin_id']);
			$Admin->admin_password = tep_encrypt_password($makePassword);
			$Admin->save();
			
			$redirectLink = itw_app_link(null, null, 'default');
		}
	}

	EventManager::attachActionResponse($redirectLink, 'redirect');
?>