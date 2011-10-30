<?php
	$hasError = false;
	$userAccount = new rentalStoreUser($_GET['cID']);
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
	if (array_key_exists('entry_vat', $_POST)) $accountValidation['entry_vat'] = $_POST['entry_vat'];
	if (array_key_exists('entry_city_birth', $_POST)) $accountValidation['entry_city_birth'] = $_POST['entry_city_birth'];

	if (array_key_exists('customers_gender', $_POST)) $accountValidation['entry_gender'] = $_POST['customers_gender'];
	if (array_key_exists('customers_newsletter', $_POST)) $accountValidation['newsletter'] = $_POST['customers_newsletter'];
	if (array_key_exists('customers_telephone', $_POST)) $accountValidation['telephone'] = $_POST['customers_telephone'];
	if (array_key_exists('customers_fax', $_POST)) $accountValidation['fax'] = $_POST['customers_fax'];
	if (array_key_exists('customers_dob', $_POST)) $accountValidation['dob'] = $_POST['customers_dob'];
	
	$hasError = $userAccount->validate($accountValidation);
	if ($hasError === false){
		$addressBook->updateAddress((int)$_POST['default_address_id'], $accountValidation);

		$userAccount->setFirstName($accountValidation['entry_firstname']);
		$userAccount->setLastName($accountValidation['entry_lastname']);
		$userAccount->setEmailAddress($accountValidation['email_address']);
		$userAccount->setTelephoneNumber($accountValidation['telephone']);
		$userAccount->setFaxNumber($accountValidation['fax']);
		$userAccount->setNewsLetter($accountValidation['newsletter']);
		if (isset($accountValidation['entry_gender'])){
			$userAccount->setGender($accountValidation['entry_gender']);
		}
		if (isset($accountValidation['dob'])){
			$userAccount->setDateOfBirth(strftime(sysLanguage::getDateFormat('short'),strtotime($accountValidation['dob'])));
		}
		$userAccount->updateCustomerAccount();

		if (array_key_exists('planid', $_POST) || array_key_exists('activate', $_POST) || array_key_exists('make_member', $_POST)){
			if (array_key_exists('activate', $_POST)) $membership->setActivationStatus($_POST['activate']);
			$membership->setPlanId($_POST['planid']);
			$membership->setPaymentMethod($_POST['payment_method']);
			
			if (array_key_exists('cc_number', $_POST)){
				$membership->setCreditCardNumber($_POST['cc_number']);
				$membership->setCreditCardExpirationDate($_POST['cc_expires_month'] . $_POST['cc_expires_year']);
				$membership->setCreditCardCvvNumber($_POST['cc_cvv']);
			}

			$planInfo = $membership->getPlanInfo($_POST['planid']);
			
			if (isset($_POST['member']) && $_POST['member'] == 'Y' && $_POST['payment_method'] != 'paypal_ipn'){
				$next_bill_date = mktime(0,0,0,
				$_POST['next_billing_month'],
				$_POST['next_billing_day'],
				$_POST['next_billing_year']
				);
			}else{
				$next_bill_date = mktime(0,0,0,
				date('m'),
				date('d') + $planInfo['membership_days'],
				date('Y')
				);
			}
			$membership->setNextBillDate($next_bill_date);
			
			/*if ($_POST['activate'] == 'N'){
				$membership->setMembershipStatus('U');
			}else{
				$membership->setMembershipStatus('M');
			}*/

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
				$currentPlan = Doctrine_Core::getTable('Membership')->findOneByPlanId((int)$_POST['planid'])->toArray();

				$emailEvent->setVars(array(
					'customerFirstName' => $userAccount->getFirstName(),
					'customerLastName' => $userAccount->getLastName(),
					'currentPlanPackageName' => $currentPlan['MembershipPlanDescription'][0]['name'],
					'currentPlanMembershipDays' => $currentPlan['membership_days'],
					'currentPlanNumberOfTitles' => $currentPlan['no_of_titles'],
					'currentPlanFreeTrial' => $currentPlan['free_trial'],
					'currentPlanPrice' => $currentPlan['price']
				));

				if (isset($_POST['prev_plan_id']) && !empty($_POST['prev_plan_id']) && $_POST['planid'] != $_POST['prev_plan_id']){
					$previousPlan = Doctrine_Core::getTable('Membership')->findOneByPlanId((int)$_POST['prev_plan_id'])->toArray();

					$emailEvent->setVars(array(
						'previousPlanPackageName' => $previousPlan['MembershipPlanDescription'][0]['name'],
						'previousPlanMembershipDays' => $previousPlan['membership_days'],
						'previousPlanNumberOfTitles' => $previousPlan['no_of_titles'],
						'previousPlanFreeTrial' => $previousPlan['free_trial'],
						'previousPlanPrice' => $previousPlan['price']
					));
				}
				$emailEvent->sendEmail(array(
					'email' => $userAccount->getEmailAddress(),
					'name'  => $userAccount->getFullName()
				));
			}
			/* Send email based on certian conditions - END */
		}

		EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('cID', 'action')) . 'cID=' . $userAccount->getCustomerId(), null, 'default'), 'redirect');
	}elseif ($error == true){
		$cInfo = new objectInfo($_POST);
		$noExit = true;
	}
?>