<?php
	$Admin = Doctrine_Core::getTable('Admin')->findOneByAdminId($_POST['id_info']);
	if ($Admin){
		$Qcheck = Doctrine_Query::create()
		->select('admin_email_address')
		->from('Admin')
		->where('admin_email_address = ?', $_POST['admin_email_address'])
		->andWhere('admin_id != ?', $_POST['id_info'])
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Qcheck){
			$messageStack->addSession('pageStack', sysLanguage::get('TEXT_INFO_ERROR'), 'error');
			$link = itw_app_link('action=edit_process');
		}else{
			$Admin->admin_firstname = $_POST['admin_firstname'];
			$Admin->admin_lastname = $_POST['admin_lastname'];
			$Admin->admin_email_address = $_POST['admin_email_address'];
			if ($_POST['admin_password'] != '' && $_POST['admin_password'] == $_POST['admin_password_confirm']){
				$Admin->admin_password = tep_encrypt_password($_POST['admin_password']);
			}
			$Admin->save();
			
			tep_mail(
				$Admin->admin_firstname . ' ' . $Admin->admin_lastname,
				$Admin->admin_email_address,
				sysLanguage::get('ADMIN_EMAIL_SUBJECT'),
				sprintf(
					sysLanguage::get('ADMIN_EMAIL_TEXT'),
					$Admin->admin_firstname,
					sysConfig::get('HTTP_SERVER') . sysConfig::getDirWsAdmin(),
					$Admin->admin_email_address,
					$hiddenPassword,
					sysConfig::get('STORE_OWNER')
				),
				sysConfig::get('STORE_OWNER'),
				sysConfig::get('STORE_OWNER_EMAIL_ADDRESS')
			);
			
			$link = itw_app_link(tep_get_all_get_params(array('action', 'mID')) . 'mID=' . $Admin->admin_id);
		}

		EventManager::attachActionResponse($link, 'redirect');
	}
?>