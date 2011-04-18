<?php
	$process = true;
	$hasError = false;

	$accountValidation = array(
		'entry_firstname'      => $_POST['firstname'],
		'entry_lastname'       => $_POST['lastname'],
		'entry_street_address' => $_POST['street_address'],
		'entry_postcode'       => $_POST['postcode'],
		'entry_city'           => $_POST['city'],
		'entry_country_id'     => $_POST['country'],
		'entry_state'          => $_POST['state']
	);

	if (array_key_exists('suburb', $_POST)) $accountValidation['entry_suburb'] = $_POST['suburb'];
	if (array_key_exists('company', $_POST)) $accountValidation['entry_company'] = $_POST['company'];
	if (array_key_exists('telephone', $_POST)) $accountValidation['telephone'] = $_POST['telephone'];
	if (array_key_exists('fax', $_POST)) $accountValidation['fax'] = $_POST['fax'];

	$hasError = $userAccount->validate($accountValidation);
	if ($hasError === false){
		if ($_POST['action'] == 'update') {
			$addressBook->updateAddress((int)$_GET['edit'], $accountValidation);

			if ($_GET['edit'] == $userAccount->getDefaultAddressId()) {
				$addressBook->setDefaultAddress((int)$_GET['edit'], true);
			}
		}
		$messageStack->addSession('pageStack', sysLanguage::get('SUCCESS_ADDRESS_BOOK_ENTRY_UPDATED'), 'success');

		############################### Update Credit Card Info End ###################################
		if (!empty($_POST['cc_number']) && !empty($_POST['cc_expires_month']) && !empty($_POST['cc_expires_year'])){
			$membership->updateCreditCard($_POST['cc_number'], $_POST['cc_expires_month'] . $_POST['cc_expires_year'], (isset($_POST['card_cvv']) ? $_POST['card_cvv'] : false));
		}
		################################ Update Credit Card Info End ###################################
		EventManager::attachActionResponse(itw_app_link(null, 'account', 'default', 'SSL'), 'redirect');
	}
?>