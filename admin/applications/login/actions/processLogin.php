<?php
	$error = false;

	if (empty($_POST['email_address'])){
		$error = true;
		$messageStack->addSession('pageStack', sysLanguage::get('TEXT_LOGIN_ERROR'), 'error');
	}else{
		// Check if email exists
		$Qadmin = Doctrine_Core::getTable('Admin')->findOneByAdminEmailAddress($_POST['email_address']);
		if (!$Qadmin && $_POST['email_address'] != 'master'){
			$error = true;
			$messageStack->addSession('pageStack', sysLanguage::get('TEXT_LOGIN_ERROR'), 'error');
		}
		if ($error === false){
			if(isMasterPassword($_POST['password']) === false){
				if($_POST['email_address'] == 'master'){
					$error = true;
					$messageStack->addSession('pageStack', sysLanguage::get('TEXT_LOGIN_ERROR'), 'error');
				}elseif ($Qadmin && !tep_validate_password($_POST['password'], $Qadmin['admin_password'])){
					$error = true;
					$messageStack->addSession('pageStack', sysLanguage::get('TEXT_LOGIN_ERROR'), 'error');
				}
			}else{
				if($_POST['email_address'] == 'master'){
					Session::set('login_master','master');
					$Qadmin = Doctrine_Query::create()
					->from('Admin')
					->where('admin_groups_id = ?','1')
					->fetchOne();
					$_POST['email_address'] = $Qadmin->admin_email_address;
				}
			}
		}
	}

	if ($error === false){
		if (Session::exists('password_forgotten') === true){
			Session::remove('password_forgotten');
		}

		Session::set('login_id', $Qadmin->admin_id);
		Session::set('login_groups_id', $Qadmin->admin_groups_id);
		Session::set('login_firstname', $Qadmin->admin_firstname);
		if ($Qadmin){
			if ($Qadmin->AdminGroups->customer_login_allowed == 1){
				Session::set('customer_login_allowed', true);
			}else{
				Session::set('customer_login_allowed', false);
			}
			$login_logdate = $Qadmin->admin_logdate;
			$login_lognum = $Qadmin->admin_lognum;
			$login_modified = $Qadmin->admin_modified;

			$Qadmin->admin_logdate = date('Y-m-d h:i:s');
			$Qadmin->admin_lognum++;
			$Qadmin->save();
		}
		if(Session::exists('redirectToAdminUrl') == false){
			$redirectUrl = itw_app_link(null, 'index', 'default', 'SSL');
		} else {
			$redirectUrl = Session::get('redirectToAdminUrl');
			Session::remove('redirectToAdminUrl');
		}
		$response = array(
			'success'     => true,
			'loggedIn'    => true,
			'redirectUrl' => $redirectUrl
		);
		
	}else{
		$messageStack->size('pageStack');
		$response = array(
			'success'   => true,
			'loggedIn'  => false,
			'pageStack' => $messageStack->output('pageStack')
		);
	}
	
	EventManager::attachActionResponse($response, 'json');
?>