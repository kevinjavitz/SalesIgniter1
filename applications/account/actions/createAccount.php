<?php
	$process = true;

	$hasError = false;
	$userAccount = new rentalStoreUser();
	$userAccount->loadPlugins();
	$addressBook =& $userAccount->plugins['addressBook'];

	$accountValidation = array(
		'entry_firstname'      => $_POST['firstname'],
		'entry_lastname'       => $_POST['lastname'],
		'entry_street_address' => $_POST['street_address'],
		'entry_postcode'       => $_POST['postcode'],
		'entry_city'           => $_POST['city'],
		'entry_country_id'     => $_POST['country'],
		'entry_state'          => $_POST['state'],
		'email_address'        => $_POST['email_address'],
		'password'             => $_POST['password'],
		'confirmation'         => $_POST['confirmation'],
		'terms'                => (array_key_exists('terms', $_POST) ? $_POST['terms'] : '')
	);
	
	if (array_key_exists('suburb', $_POST)) $accountValidation['entry_suburb'] = $_POST['suburb'];
	if (array_key_exists('fiscal_code', $_POST)) $accountValidation['entry_cif'] = $_POST['fiscal_code'];
	if (array_key_exists('vat_number', $_POST)) $accountValidation['entry_vat'] = $_POST['vat_number'];
	if (array_key_exists('city_birth', $_POST)) $accountValidation['entry_city_birth'] = $_POST['city_birth'];
	if (array_key_exists('company', $_POST)) $accountValidation['entry_company'] = $_POST['company'];
	if (array_key_exists('gender', $_POST)) $accountValidation['entry_gender'] = $_POST['gender'];
	if (array_key_exists('newsletter', $_POST)) $accountValidation['newsletter'] = $_POST['newsletter'];
	if (array_key_exists('telephone', $_POST)) $accountValidation['telephone'] = $_POST['telephone'];
	if (array_key_exists('fax', $_POST)) $accountValidation['fax'] = $_POST['fax'];
	if (array_key_exists('dob', $_POST)) $accountValidation['dob'] = $_POST['dob'];
	
	$hasError = $userAccount->validate($accountValidation);
	if ($hasError === false){
		$userAccount->setFirstName($accountValidation['entry_firstname']);
		$userAccount->setLastName($accountValidation['entry_lastname']);
		$userAccount->setEmailAddress($accountValidation['email_address']);
		$userAccount->setPassword($accountValidation['password']);
		$userAccount->setNewsLetter($accountValidation['newsletter']);
		$userAccount->setLanguageId(Session::get('languages_id'));
		
		if (array_key_exists('telephone', $_POST)) $userAccount->setTelephoneNumber($accountValidation['telephone']);
		if (array_key_exists('fax', $_POST)) $userAccount->setFaxNumber($accountValidation['fax']);
		if (array_key_exists('gender', $_POST)) $userAccount->setGender($accountValidation['entry_gender']);
		if (array_key_exists('dob', $_POST)) $userAccount->setDateOfBirth($accountValidation['dob']);
		if (array_key_exists('city_birth', $_POST)) $userAccount->setCityBirth($accountValidation['entry_city_birth']);
		EventManager::notify('AccountSetupPostFields');
		$customerId = $userAccount->createNewAccount();

		$addressBook->insertAddress($accountValidation, true, true);

		$userAccount->processLogIn(
			$accountValidation['email_address'],
			$accountValidation['password']
		);

		$link = itw_app_link(null, 'account', 'default', 'SSL');

		if (Session::exists('on_create_account_action')){
			$eInfo = Session::get('on_create_account_action');
			if ($eInfo['type'] == 'redirect'){
				$getVars = array();
				if (isset($eInfo['getVars'])){
					foreach($eInfo['getVars'] as $k => $v){
						$getVars[] = $k . '=' . $v;
					}
				}
				$link = itw_app_link(
					implode('&', $getVars),
					(isset($eInfo['app']) ? $eInfo['app'] : null),
					(isset($eInfo['appPage']) ? $eInfo['appPage'] : null)
				);
			}
		}

		EventManager::attachActionResponse($link, 'redirect');
	}
?>