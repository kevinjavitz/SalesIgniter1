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
		if (isset($navigation->snapshot['get']) && sizeof($navigation->snapshot['get']) > 0) {
			if(is_array($navigation->snapshot['get'])){
				$paramsArr = $navigation->snapshot['get'];
				if(isset($navigation->snapshot['get']['app'])){
					$app =$navigation->snapshot['get']['app'];
					unset($navigation->snapshot['get']['app']);
				}else{
					$app = null;
				}

				if(isset($navigation->snapshot['get']['appPage'])){
					$appPage =$navigation->snapshot['get']['appPage'];
					unset($navigation->snapshot['get']['appPage']);
				}else{
					$appPage = null;
				}
				$paramVar = '';
				foreach($navigation->snapshot['get'] as $key => $param){
					$paramVar .= $key. '='. $param . '&';
				}

			}else{
				$paramsArr = explode('&',$navigation->snapshot['get']);
				$paramVar = '';
				foreach($paramsArr as $param){
					$varArr = explode('=', $param);
					if($varArr[0] == 'app'){
						$app = $varArr[1];
					}elseif($varArr[0] == 'appPage'){
						$appPage = $varArr[1];
					}else{
						$paramVar .= $param . '&';
					}
				}
			}

			if(!empty($paramVar)){
				$params = substr($paramVar, 0, strlen($paramVar)-1);
			}else{
				$params = null;
			}
			//$origin_href = itw_app_link($navigation->snapshot['page'], tep_array_to_string($navigation->snapshot['get'], array(Session::getSessionName())), $navigation->snapshot['mode']);
			$origin_href = itw_app_link($params, $app, $appPage, 'SSL');
			//$origin_href = tep_href_link($navigation->snapshot['page'], tep_array_to_string($navigation->snapshot['get'], array(Session::getSessionName())), $navigation->snapshot['mode']);
			$navigation->clear_snapshot();
			$redirectUrl = $origin_href;
		} else {
			$redirectUrl = itw_app_link(null, 'index', 'default', 'SSL');
		}
		$response = array(
			'success'     => true,
			'loggedIn'    => true,
			'redirectUrl' => $redirectUrl
		);
		
		if ($login_lognum == 0 || !($login_logdate) || $login_modified == '0000-00-00 00:00:00'){
			$redirectUrl = itw_app_link(null, 'index', 'default', 'SSL');
		}
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