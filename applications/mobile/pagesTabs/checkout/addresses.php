<?php
$addressBook = $userAccount->plugins['addressBook'];

$billingAddress = $addressBook->getAddress('billing');
$shippingAddress = $addressBook->getAddress('delivery');
$pickupAddress = $addressBook->getAddress('pickup');

$ShippingAddressTable = htmlBase::newElement('newFormTable')
	->loadForm('shipping_address_entry')
	->setFieldKey('shipping')
	->setFieldValues($shippingAddress)
	->setFieldValue('entry_country', sysConfig::get('ONEPAGE_DEFAULT_COUNTRY'));

$BillingAddressTable = htmlBase::newElement('newFormTable')
	->loadForm('billing_address_entry')
	->setFieldKey('billing')
	->setFieldValues($billingAddress)
	->setFieldValue('entry_country', sysConfig::get('ONEPAGE_DEFAULT_COUNTRY'))
	->setFieldValue('entry_telephone', (isset($onePageCheckout->onePage['info']['telephone']) ? $onePageCheckout->onePage['info']['telephone'] : ''))
	->setFieldValue('entry_email_address', (isset($onePageCheckout->onePage['info']['email_address']) ? $onePageCheckout->onePage['info']['email_address'] : ''));

if ($onePageCheckout->isMembershipCheckout() === true){
	if (sysConfig::get('RENTAL_DEFAULT_COUNTRY_ENABLED') == 'true'){
		$BillingAddressTable->setFieldValue('entry_country', sysConfig::get('RENTAL_DEFAULT_COUNTRY'));
		$BillingAddressTable->setFieldReadOnly('entry_country', true);

		$ShippingAddressTable->setFieldValue('entry_country', sysConfig::get('RENTAL_DEFAULT_COUNTRY'));
		$ShippingAddressTable->setFieldReadOnly('entry_country', true);
	}
}

if ($userAccount->isLoggedIn() === true){
	$BillingAddressTable->removeField('entry_email_address');
	$BillingAddressTable->removeField('entry_password');
	$BillingAddressTable->removeField('entry_password_confirm');
}elseif ($onePageCheckout->isMembershipCheckout() || sysConfig::get('ONEPAGE_ACCOUNT_CREATE') == 'create'){
	//Removed and added later in the page manually
	$PasswordField = $BillingAddressTable->getField('entry_password');
	$PasswordConfirmField = $BillingAddressTable->getField('entry_password_confirm');

	$BillingAddressTable->removeField('entry_password');
	$BillingAddressTable->removeField('entry_password_confirm');
}

if (sysConfig::get('ACCOUNT_NEWSLETTER') == 'true') {
	$NewsletterInput = htmlBase::newElement('checkbox')
		->setLabel(sysLanguage::get('ENTRY_NEWSLETTER'))
		->setLabelPosition('before')
		->setName('newsletter')
		->setValue('1')
		/*->checked((isset($onePageCheckout->onePage['info']['newsletter']) && $onePageCheckout->onePage['info']['newsletter'] == '1'))*/;
}

if ($userAccount->isLoggedIn() === false){
	$LoginLink = htmlBase::newElement('a')
		->setHref(itw_app_link('rType=ajax&ui_state=dialog', 'mobile', 'loginDialog', 'SSL'))
		->html(sysLanguage::get('TEXT_BUTTON_LOGIN'))
		->css('margin-left','10px')
		->attr('id','loginButton')
		->attr('data-rel', 'dialog');

	echo '<p>' .
		sysLanguage::get('TEXT_ALREADY_HAVE_ACCOUNT') .
		$LoginLink->draw() .
		'</p>';
}
?>

<input type="hidden" name="currentPage" id="currentPage" value="addresses">
<div id="addressBook" title="Address Book" style="display:none"></div>
<?php
echo '<div data-role="collapsible" data-theme="b" data-content-theme="c" data-collapsed="false">' .
	'<h3>' . sysLanguage::get('TEXT_BILLING_ADDRESS') . '</h3>' .
	'<p>' . $BillingAddressTable->draw() . '</p>' .
	'</div>';

if ($userAccount->isLoggedIn() === true){
	$ChangeBillingButton = htmlBase::newElement('button')
		->attr('id','changeBillingAddress')
		->attr('data-inline', 'true')
		->setText(sysLanguage::get('TEXT_BUTTON_CHANGE_ADDRESS'))
		->setName('changeBillingAddress');

	echo '<div style="text-align:right">' . $ChangeBillingButton->draw() . '</div>';
}

echo '<div data-role="collapsible" data-theme="b" data-content-theme="c">' .
	'<h3>' . sysLanguage::get('TEXT_DIFFERENT_FROM_BILLING') . '</h3>' .
	'<p>' . $ShippingAddressTable->draw() . '</p>' .
	'</div>';

if (sysConfig::get('ONEPAGE_CHECKOUT_PICKUP_ADDRESS') == 'true'){
	$PickupAddressTable = htmlBase::newElement('formTable')
		->loadForm('shipping_address_entry')
		->setFieldKey('shipping')
		->setFieldValues($shippingAddress)
		->setFieldValue('entry_country', sysConfig::get('ONEPAGE_DEFAULT_COUNTRY'));

	echo '<div data-role="collapsible" data-theme="b" data-content-theme="c">' .
		'<h3>' . 'Different From Delivery Address' . '</h3>' .
		'<p>' . $PickupAddressTable->draw() . '</p>' .
		'</div>';
}

if (isset($PasswordField) && is_null($PasswordField) === false){
	echo '<div data-role="collapsible" data-theme="b" data-content-theme="c">' .
		'<h3>' . sysLanguage::get('TEXT_MAKE_ACCOUNT') . '</h3>' .
		'<p>' .
		$PasswordField->getLabel() . $PasswordField->getField()->draw() .
		$PasswordConfirmField->getLabel() . $PasswordConfirmField->getField()->draw() .
		(isset($NewsletterInput) ? $NewsletterInput->draw() : '') .
		'</p>' .
		'</div>';
}
