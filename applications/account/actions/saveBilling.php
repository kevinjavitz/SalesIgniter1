<?php
	$process = true;
	$hasError = false;

	$addressValidationArray = array(
		'entry_firstname'      => $_POST['firstname'],
		'entry_lastname'       => $_POST['lastname'],
		'entry_street_address' => $_POST['street_address'],
		'entry_suburb'         => null,
		'entry_postcode'       => $_POST['postcode'],
		'entry_city'           => $_POST['city'],
		'entry_country_id'     => $_POST['country'],
		'entry_company'        => null,
		'entry_state'          => $_POST['state']
	);
	if (array_key_exists('suburb', $_POST)) $addressValidationArray['entry_suburb'] = $_POST['suburb'];
	if (array_key_exists('fiscal_code', $_POST)) $addressValidationArray['entry_cif'] = $_POST['fiscal_code'];
	if (array_key_exists('vat_number', $_POST)) $addressValidationArray['entry_vat'] = $_POST['vat_number'];
	if (array_key_exists('city_birth', $_POST)) $addressValidationArray['entry_city_birth'] = $_POST['city_birth'];
	if (array_key_exists('company', $_POST)) $addressValidationArray['entry_company'] = $_POST['company'];
	//$hasError = $addressBook->validate($addressValidationArray);
	$link = itw_app_link(null, 'account', 'address_book', 'SSL');
	if ($hasError === false){
		if (isset($_GET['edit'])) {
			$addressBook->updateAddress((int)$_GET['edit'], $addressValidationArray);

			if (isset($_POST['primary']) || $_GET['edit'] == $addressBook->getDefaultAddressId()) {
				$addressBook->setDefaultAddress((int)$_GET['edit'], true);
			}
			if (!empty($_POST['cc_number']) && !empty($_POST['cc_expires_month']) && !empty($_POST['cc_expires_year'])){
				$membership =& $userAccount->plugins['membership'];
				$membership->updateCreditCard($_POST['cc_number'], $_POST['cc_expires_month'] . $_POST['cc_expires_year'], (isset($_POST['cc_cvv']) ? $_POST['cc_cvv'] : false));
				$_GET['custID'] = $userAccount->getCustomerId();
				require('cron/membership_update.php');
				unset($_GET['custID']);
				$membership =& $userAccount->plugins['membership'];
				$membership->loadMembershipInfo();
				$membership->loadPlanInfo();
				$link = itw_app_link('edit='.$membership->getRentalAddressId(), 'account', 'billing_address_book', 'SSL');
			}
		}
		$messageStack->addSession('pageStack', sysLanguage::get('SUCCESS_ADDRESS_BOOK_ENTRY_UPDATED'), 'success');
		EventManager::attachActionResponse($link, 'redirect');
	}
?>