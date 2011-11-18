<?php
	$accountValidation = array(
		'entry_firstname' => $_POST['firstname'],
		'entry_lastname'  => $_POST['lastname']
	);
	
	if ($_POST['email_address'] != $userAccount->getEmailAddress()){
		$accountValidation['email_address'] = $_POST['email_address'];
	}

	if (array_key_exists('gender', $_POST)) $accountValidation['entry_gender'] = $_POST['gender'];
	if (array_key_exists('telephone', $_POST)) $accountValidation['telephone'] = $_POST['telephone'];
	if (array_key_exists('fax', $_POST)) $accountValidation['fax'] = $_POST['fax'];
	if (array_key_exists('dob', $_POST)) $accountValidation['dob'] = $_POST['dob'];
	if (array_key_exists('city_birth', $_POST)) $accountValidation['city_birth'] = $_POST['city_birth'];

	$hasError = $userAccount->validate($accountValidation);
	if ($hasError == false) {
		if ($accountValidation['entry_firstname'] != $userAccount->getFirstName()){
			$userAccount->setFirstName($accountValidation['entry_firstname']);
		}
		if ($accountValidation['entry_lastname'] != $userAccount->getLastName()){
			$userAccount->setLastName($accountValidation['entry_lastname']);
		}
		if (isset($accountValidation['email_address']) && $accountValidation['email_address'] != $userAccount->getEmailAddress()){
			$userAccount->setEmailAddress($accountValidation['email_address']);
		}
		if (isset($accountValidation['telephone']) && $accountValidation['telephone'] != $userAccount->getTelephoneNumber()){
			$userAccount->setTelephoneNumber($accountValidation['telephone']);
		}
		if (isset($accountValidation['fax']) && $accountValidation['fax'] != $userAccount->getFaxNumber()){
			$userAccount->setFaxNumber($accountValidation['fax']);
		}

		if (sysConfig::get('ACCOUNT_GENDER') == 'true'){
			if ($accountValidation['entry_gender'] != $userAccount->getGender()){
				$userAccount->setGender($accountValidation['entry_gender']);
			}
		}

		if (sysConfig::get('ACCOUNT_CITY_BIRTH') == 'true'){
			if ($accountValidation['city_birth'] != $userAccount->getCityBirth()){
				$userAccount->setCityBirth($accountValidation['city_birth']);
			}
		}

		if (sysConfig::get('ACCOUNT_DOB') == 'true'){
			$parsed = strptime($accountValidation['dob'], sysLanguage::getDateFormat('short'));
			$dob = date('Y-m-d',mktime(0,0,0,($parsed['tm_mon']+1),$parsed['tm_mday'],(1900+$parsed['tm_year'])));
			if ($dob != $userAccount->getDateOfBirth()){
				$userAccount->setDateOfBirth($accountValidation['dob']);
			}
		}

		$userAccount->setLanguageId($_POST['language_id']);
		$userAccount->updateCustomerAccount();

		$messageStack->addSession('pageStack', sysLanguage::get('SUCCESS_ACCOUNT_UPDATED'), 'success');
		
		EventManager::attachActionResponse(itw_app_link(null, 'account', 'default', 'SSL'), 'redirect');
	}
?>