<?php
	$hasError = false;
	$userAccount = new rentalStoreUser((isset($_GET['cID']) ? $_GET['cID'] : false));
	$userAccount->loadPlugins();
	$addressBook =& $userAccount->plugins['addressBook'];
	$membership =& $userAccount->plugins['membership'];

	$accountValidation = array(
		'entry_firstname'      => $_POST['customers_firstname'],
		'entry_lastname'       => $_POST['customers_lastname'],
		'entry_street_address' => $_POST['entry_street_address'],
		'entry_postcode'       => $_POST['entry_postcode'],
		'entry_city'           => $_POST['entry_city'],
		'entry_country_id'     => $_POST['country'],
		'entry_state'          => (isset($_POST['entry_state'])?$_POST['entry_state']:'none'),
		'email_address'        => $_POST['customers_email_address']
	);
	
	if (array_key_exists('entry_suburb', $_POST)) $accountValidation['entry_suburb'] = $_POST['entry_suburb'];
	if (array_key_exists('entry_company', $_POST)) $accountValidation['entry_company'] = $_POST['entry_company'];

	if (array_key_exists('entry_cif', $_POST)) $accountValidation['entry_cif'] = $_POST['entry_cif'];

	if (array_key_exists('customers_password', $_POST) && !empty($_POST['customers_password'])){
		$accountValidation['password'] = $_POST['customers_password'];
		$accountValidation['confirmation'] = $_POST['customers_password'];
	}

	if (array_key_exists('entry_vat', $_POST)) $accountValidation['entry_vat'] = $_POST['entry_vat'];
	if (array_key_exists('customers_city_birth', $_POST)) $accountValidation['city_birth'] = $_POST['customers_city_birth'];

	if (array_key_exists('customers_gender', $_POST)) $accountValidation['entry_gender'] = $_POST['customers_gender'];
	if (array_key_exists('customers_newsletter', $_POST)) $accountValidation['newsletter'] = $_POST['customers_newsletter'];
	if (array_key_exists('customers_telephone', $_POST)) $accountValidation['telephone'] = $_POST['customers_telephone'];
	if (array_key_exists('customers_notes', $_POST)) $accountValidation['notes'] = $_POST['customers_notes'];
	if (array_key_exists('customers_fax', $_POST)) $accountValidation['fax'] = $_POST['customers_fax'];
	if (array_key_exists('customers_dob', $_POST)) $accountValidation['dob'] = $_POST['customers_dob'];
	
	$hasError = $userAccount->validate($accountValidation);
	if ($hasError === false){
		if (isset($_GET['cID'])){
			$addressBook->updateAddress((int)$_POST['default_address_id'], $accountValidation);
		}
		$userAccount->setFirstName($accountValidation['entry_firstname']);
		$userAccount->setLastName($accountValidation['entry_lastname']);
		$userAccount->setEmailAddress($accountValidation['email_address']);
		$userAccount->setPassword($accountValidation['password']);
		$userAccount->setTelephoneNumber($accountValidation['telephone']);
		$userAccount->setNotes($accountValidation['notes']);
		$userAccount->setFaxNumber($accountValidation['fax']);
		$userAccount->setNewsLetter($accountValidation['newsletter']);
		if (isset($accountValidation['entry_gender'])){
			$userAccount->setGender($accountValidation['entry_gender']);
		}
		if (isset($accountValidation['dob'])){
			$userAccount->setDateOfBirth(strftime(sysLanguage::getDateFormat('short'),strtotime($accountValidation['dob'])));
		}
		$userAccount->setMemberNumber((!empty($_POST['customers_number']) ? $_POST['customers_number'] : tep_create_random_value(8)));
		$userAccount->setAccountFrozen((isset($_POST['customers_account_frozen'])));

		if (isset($accountValidation['city_birth'])){
			$userAccount->setCityBirth($accountValidation['city_birth']);
		}
		if (isset($_GET['cID'])){
			$userAccount->updateCustomerAccount();
			$addressBook->updateAddress((int)$_POST['default_address_id'], $accountValidation);
		}else{
			$userAccount->createNewAccount();
			$addressBook->insertAddress($accountValidation, true);
		}

        EventManager::notify('NewCustomerAccountBeforeExecute', $_GET['cID']);

		if (array_key_exists('planid', $_POST) || array_key_exists('activate', $_POST) || array_key_exists('make_member', $_POST)){
			if (array_key_exists('activate', $_POST)){
				$membership->setActivationStatus($_POST['activate']);
			}
			if(isset($_POST['planid'])){
				$membership->setPlanId($_POST['planid']);
			}
			if(isset($_POST['payment_method'])){
				$membership->setPaymentMethod($_POST['payment_method']);
			}

			if (array_key_exists('cc_number', $_POST)){
				$membership->setCreditCardNumber($_POST['cc_number']);
				$membership->setCreditCardExpirationDate($_POST['cc_expires_month'] . $_POST['cc_expires_year']);
				$membership->setCreditCardCvvNumber($_POST['cc_cvv']);
			}

			if(isset($_POST['planid'])){
				$planInfo = $membership->getPlanInfo($_POST['planid']);
			}
			if (array_key_exists('activate', $_POST)){

				if(isset($_POST['next_billing_month']) && isset($_POST['next_billing_day']) && isset($_POST['next_billing_year'])){
					$next_bill_date = mktime(0,0,0,
						$_POST['next_billing_month'],
						$_POST['next_billing_day'],
						$_POST['next_billing_year']
					);
					$membership->setNextBillDate($next_bill_date);
				}
			}
            if (array_key_exists('auto_billing', $_POST)){
                $membership->setAutoBilling(1);
            }else{
                $membership->setAutoBilling(0);
            }

			if (array_key_exists('make_member', $_POST)){
				$membership->createNewMembership();
			}else{
				$membership->updateMembership();
			}

		
			/* Send email based on certian conditions - BEGIN */


			$emailEventName = false;
			if ($_POST['activate'] == 'Y'){
				if (array_key_exists('make_member', $_POST)){
					$emailEventName = 'membership_activated_admin';
				}elseif (tep_not_null($_POST['prev_acti_status']) && $_POST['prev_acti_status'] == 'N'){
					$emailEventName = 'membership_activated_admin';
				}elseif ($_POST['prev_plan_id'] != "" && $_POST['planid'] != $_POST['prev_plan_id']){
					$emailEventName = 'membership_upgraded_admin';
				}
			}elseif ($_POST['prev_acti_status'] == 'N' && $_POST['prev_acti_status'] == 'Y'){
				$emailEventName = 'membership_canceled_admin';
			}
		
			if ($emailEventName !== false){
				$emailEvent = new emailEvent($emailEventName, $userAccount->getLanguageId());
				$QcurrentPlan = Doctrine_Query::create()
				->from('Membership m')
				->leftJoin('m.MembershipPlanDescription mpd')
				->where('m.plan_id = ?', (int)$_POST['planid'])
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

				$emailEvent->setVars(array(
					'customerFirstName' => $userAccount->getFirstName(),
					'customerLastName' => $userAccount->getLastName(),
					'currentPlanPackageName' => $QcurrentPlan[0]['MembershipPlanDescription'][0]['name'],
					'currentPlanMembershipDays' => $QcurrentPlan[0]['membership_days'],
					'currentPlanNumberOfTitles' => $QcurrentPlan[0]['no_of_titles'],
					'currentPlanFreeTrial' => $QcurrentPlan[0]['free_trial'],
					'currentPlanPrice' => $QcurrentPlan[0]['price']
				));

				if (isset($_POST['prev_plan_id']) && !empty($_POST['prev_plan_id']) && $_POST['planid'] != $_POST['prev_plan_id']){
					//$previousPlan = Doctrine_Core::getTable('Membership')->findOneByPlanId((int)$_POST['prev_plan_id'])->toArray();
					$QprevPlan = Doctrine_Query::create()
						->from('Membership m')
						->leftJoin('m.MembershipPlanDescription mpd')
						->where('m.plan_id = ?', (int)$_POST['prev_plan_id'])
						->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

					$emailEvent->setVars(array(
						'previousPlanPackageName' => $QprevPlan[0]['MembershipPlanDescription'][0]['name'],
						'previousPlanMembershipDays' =>  $QprevPlan[0]['membership_days'],
						'previousPlanNumberOfTitles' =>  $QprevPlan[0]['no_of_titles'],
						'previousPlanFreeTrial' =>  $QprevPlan[0]['free_trial'],
						'previousPlanPrice' =>  $QprevPlan[0]['price']
					));
				}
				if(isset($_POST['sendEmail'])){
					$emailEvent->sendEmail(array(
						'email' => $userAccount->getEmailAddress(),
						'name'  => $userAccount->getFullName()
					));
				}
			}
			/* Send email based on certian conditions - END */
		}

		EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('cID', 'action')) . 'cID=' . $userAccount->getCustomerId(), null, 'default'), 'redirect');
	}elseif ($error == true){
		$cInfo = new objectInfo($_POST);
		$noExit = true;
	}
?>