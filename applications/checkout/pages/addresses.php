<?php

	$addressBook = $userAccount->plugins['addressBook'];

	$billingAddress = $addressBook->getAddress('billing');
	$shippingAddress = $addressBook->getAddress('delivery');
	$pickupAddress = $addressBook->getAddress('pickup');
		
	$Addresses = array(
		'billing' => array(
			'heading' => sysLanguage::get('TEXT_BILLING_ADDRESS'),
			'valueArray' => $billingAddress
		),
		'shipping' => array(
			'heading' => sysLanguage::get('TEXT_DELIVERY_ADDRESS'),
			'valueArray' => $shippingAddress
		),
		'pickup' => array(
			'heading' => sysLanguage::get('TEXT_PICKUP_ADDRESS'),
			'valueArray' => $pickupAddress
		)
	);
	
	foreach($Addresses as $key => $aInfo){
		$values = $aInfo['valueArray'];
		
		$FieldsArray = array(
			'first_name' => array('required' => true, 'value' => $values['entry_firstname']),
			'last_name' => array('required' => true, 'value' => $values['entry_lastname']),
			'country' => array('required' => true, 'value' => sysConfig::get('ONEPAGE_DEFAULT_COUNTRY')),
			'street_address' => array('required' => true, 'value' => $values['entry_street_address']),
			'city' => array('required' => true, 'value' => $values['entry_city']),
			'postcode' => array('required' => true, 'value' => $values['entry_postcode'])
		);
		
		if (isset($values['entry_country_id'])){
			$FieldsArray['country']['value'] = $values['entry_country_id'];
		}
		
		if ($key == 'billing'){
			$FieldsArray['telephone'] = array(
				'required' => ((sysConfig::get('ACCOUNT_TELEPHONE_REQUIRED') == 'true')?true:false),
				'value' => (isset($onePageCheckout->onePage['info']['telephone']) ? $onePageCheckout->onePage['info']['telephone'] : '')
			);

			if ($userAccount->isLoggedIn() === false){
				$FieldsArray['email_address'] = array(
					'required' => true,
					'value' => (isset($onePageCheckout->onePage['info']['email_address']) ? $onePageCheckout->onePage['info']['email_address'] : '')
				);
			}else{
				$FieldsArray['email_address'] = array(
					'required' => false,
					'hidden' => true,
					'value' => (isset($onePageCheckout->onePage['info']['email_address']) ? $onePageCheckout->onePage['info']['email_address'] : '')
				);
			}

	
			if (sysConfig::get('ACCOUNT_GENDER') == 'true'){
				$FieldsArray['gender'] = array('value' => $values['entry_gender']);
			}
	
			if (sysConfig::get('ACCOUNT_DOB') == 'true'){
				$FieldsArray['dob'] = array(
					'value' => (isset($onePageCheckout->onePage['info']['dob']) ? $onePageCheckout->onePage['info']['dob'] : '')
				);
			}
		}
		
		if (sysConfig::get('ACCOUNT_SUBURB') == 'true'){
			$FieldsArray['suburb'] = array('value' => $values['entry_suburb']);
		}
	
		if (sysConfig::get('ACCOUNT_STATE') == 'true'){
			$FieldsArray['state'] = array(
				'required' => true,
				'value' => $values['entry_state']
			);
		}
		
		if (sysConfig::get('ACCOUNT_COMPANY') == 'true'){
			$FieldsArray['company'] = array(
				'required' => false,
				'value' => $values['entry_company']
			);
		}

		if (sysConfig::get('ACCOUNT_FISCAL_CODE_REQUIRED') == 'true'){
			$FieldsArray['fiscal_code'] = array(
				'required' => true,
				'value' => $values['entry_cif']
			);
		}

		if (sysConfig::get('ACCOUNT_VAT_NUMBER_REQUIRED') == 'true'){
			$FieldsArray['vat_number'] = array(
				'required' => true,
				'value' => $values['entry_vat']
			);
		}

		if (sysConfig::get('ACCOUNT_CITY_BIRTH_REQUIRED') == 'true'){
			$FieldsArray['city_birth'] = array(
				'required' => false,
				'value' => $values['entry_city_birth']
			);
		}

		if ($onePageCheckout->isMembershipCheckout() === true){
			if (sysConfig::get('RENTAL_DEFAULT_COUNTRY_ENABLED') == 'true'){
				$FieldsArray['country']['value'] = sysConfig::get('RENTAL_DEFAULT_COUNTRY');

				$FieldsArray['country']['options'] = array(
					array(
						'id' => sysConfig::get('RENTAL_DEFAULT_COUNTRY'),
						'text' => tep_get_country_name(sysConfig::get('RENTAL_DEFAULT_COUNTRY'))
					)
				);
			}
		}

		$Addresses[$key]['Fields'] = $addressBook->createAddressFields(array(
			'name_prefix' => $key,
			'fields' => $FieldsArray
		));
	}
	
	$BillingPasswordInput = htmlBase::newElement('input')
	->setType('password')
	->attr('maxlength', '40')
	->setName('password')
	->setRequired((($onePageCheckout->isMembershipCheckout() || sysConfig::get('ONEPAGE_ACCOUNT_CREATE') == 'required')?true:false));
    if(isset($onePageCheckout->onePage['info']['password'])){
		$BillingPasswordInput->setValue($onePageCheckout->onePage['info']['password']);
	}
	$BillingPasswordConfirmInput = htmlBase::newElement('input')
	->setType('password')
	->attr('maxlength', '40')
	->setName('confirmation')
	->setRequired((($onePageCheckout->isMembershipCheckout() || sysConfig::get('ONEPAGE_ACCOUNT_CREATE') == 'required')?true:false));

	if(isset($onePageCheckout->onePage['info']['confirmation'])){
		$BillingPasswordConfirmInput->setValue($onePageCheckout->onePage['info']['confirmation']);
	}
	$NewsletterInput = htmlBase::newElement('checkbox')
	->setLabel(sysLanguage::get('ENTRY_NEWSLETTER'))
	->setLabelPosition('before')
	->setName('newsletter')
	->setValue('1')
	/*->checked((isset($onePageCheckout->onePage['info']['newsletter']) && $onePageCheckout->onePage['info']['newsletter'] == '1'))*/;

	echo '<table border="0" width="100%" cellspacing="0" cellpadding="2">
         <tr id="logInRow"' . ($userAccount->isLoggedIn() === true ? ' style="display:none"' : '') . '>
          <td class="main">'.sysLanguage::get('TEXT_ALREADY_HAVE_ACCOUNT') .
			htmlBase::newElement('a')
			->setHref(itw_app_link(null, 'account', 'login', 'SSL'))
			->html(sysLanguage::get('TEXT_BUTTON_LOGIN'))
			->css('margin-left','10px')
			->attr('id','loginButton')
			->draw() .
		'</td>
         </tr>
       </table>';
?>

		<input type="hidden" name="currentPage" id="currentPage" value="addresses">
		<div id="addressBook" title="Address Book" style="display:none"></div>
<?php

		foreach($Addresses as $key => $aInfo){
			if ($key != 'pickup' || ($key == 'pickup' && sysConfig::get('ONEPAGE_CHECKOUT_PICKUP_ADDRESS') == 'true')){
				$Fields = $aInfo['Fields'];

				$FormTable = htmlBase::newElement('formTable');

				if (isset($Fields['company'])){
					$FormTable->addRow(sysLanguage::get('ENTRY_COMPANY'));
					$FormTable->addRow($Fields['company']);
				}

				if (isset($Fields['fiscal_code'])){
					$FormTable->addRow(sysLanguage::get('ENTRY_FISCAL_CODE'));
					$FormTable->addRow($Fields['fiscal_code']);
				}

				if (isset($Fields['vat_number'])){
					$FormTable->addRow(sysLanguage::get('ENTRY_VAT_NUMBER'));
					$FormTable->addRow($Fields['vat_number']);
				}

				if (isset($Fields['city_birth'])){
					$FormTable->addRow(sysLanguage::get('ENTRY_CITY_BIRTH'));
					$FormTable->addRow($Fields['city_birth']);
				}

				if ($userAccount->isLoggedIn() === false){
					if (isset($Fields['email_address'])){
						$FormTable->addRow(sysLanguage::get('ENTRY_EMAIL_ADDRESS'));
						$FormTable->addRow($Fields['email_address']);
					}
				}else{
					if (isset($Fields['email_address'])){
						$FormTable->addRow($Fields['email_address']);
					}
				}

				if (isset($Fields['gender'])){
					$FormTable->addRow(sysLanguage::get('ENTRY_GENDER'));
					$FormTable->addRow($Fields['gender']);
				}

				if (isset($Fields['dob'])){
					$FormTable->addRow(sysLanguage::get('ENTRY_DATE_OF_BIRTH'));
					$FormTable->addRow($Fields['dob']);
				}

				$FormTable->addRow(sysLanguage::get('ENTRY_FIRST_NAME'), sysLanguage::get('ENTRY_LAST_NAME'));
				$FormTable->addRow($Fields['first_name'], $Fields['last_name']);

				$FormTable->addRow(sysLanguage::get('ENTRY_STREET_ADDRESS'));
				$FormTable->addRow($Fields['street_address']);

				if (sysConfig::get('ACCOUNT_SUBURB') == 'true'){
					$FormTable->addRow(sysLanguage::get('ENTRY_SUBURB'));
					$FormTable->addRow($Fields['suburb']);
				}

				$FormTable->addRow(sysLanguage::get('ENTRY_CITY'));
				$FormTable->addRow($Fields['city']);

				if (isset($Fields['state'])){
					$FormTable->addRow(sysLanguage::get('ENTRY_STATE'));
					$FormTable->addRow($Fields['state']);
				}

				$FormTable->addRow(sysLanguage::get('ENTRY_POST_CODE'));
				$FormTable->addRow($Fields['postcode']);

				$FormTable->addRow(sysLanguage::get('ENTRY_COUNTRY'));
				$FormTable->addRow($Fields['country']);

				if (isset($Fields['telephone'])){
					$FormTable->addRow(sysLanguage::get('ENTRY_TELEPHONE_NUMBER'));
					$FormTable->addRow($Fields['telephone']);
				}

				$Heading = htmlBase::newElement('div')
				->addClass('ui-widget-header ui-corner-all')
				->html('&nbsp;' . $aInfo['heading']);

				$AddressContainer = htmlBase::newElement('div')
				->addClass('ui-widget ui-widget-content ui-corner-all')
				->append($Heading);

				$AddressDiv = htmlBase::newElement('div')
				->addClass($key . 'Address')
				->css(array(
					'margin' => '.3em'
				));
				$AddressDiv->append($FormTable);
				if ($userAccount->isLoggedIn() === false){
				}else{
					if ($key == 'billing'){
						$changeButton = htmlBase::newElement('div')->html('<table id="changeBillingAddressTable" border="0" width="100%" cellspacing="0" cellpadding="2"'. ($userAccount->isLoggedIn() === true ? '' : ' style="display:none"').'>
										<tr>
										 <td class="main" align="right">'.
											htmlBase::newElement('div')
											->attr('id','changeBillingAddress')
											->html(sysLanguage::get('TEXT_BUTTON_CHANGE_ADDRESS'))
											->setName('changeBillingAddress')
											->draw() .
										'</td>
										</tr>
									   </table>');
						$AddressDiv->append($changeButton);
					}
					if ($key == 'shipping'){
						$changeButton = htmlBase::newElement('div')->html('<table id="changeShippingAddressTable" border="0" width="100%" cellspacing="0" cellpadding="2"'. ($userAccount->isLoggedIn() === true ? '' : ' style="display:none"').'>
										<tr>
										 <td class="main" align="right">'.
											htmlBase::newElement('div')
											->attr('id','changeShippingAddress')
											->html(sysLanguage::get('TEXT_BUTTON_CHANGE_ADDRESS'))
											->setName('changeShippingAddress')
											->draw() .
									'</td>
										</tr>
									   </table>');
						$AddressDiv->append($changeButton);
					}
					if ($key == 'pickup'){
						$changeButton = htmlBase::newElement('div')->html('<table id="changePickupAddressTable" border="0" width="100%" cellspacing="0" cellpadding="2"'. ($userAccount->isLoggedIn() === true ? '' : ' style="display:none"').'>
										<tr>
										 <td class="main" align="right">'.
											htmlBase::newElement('div')
											->attr('id','changePickupAddress')
											->html(sysLanguage::get('TEXT_BUTTON_CHANGE_ADDRESS'))
											->setName('changePickupAddress')
											->draw() .
										'</td>
										</tr>
									   </table>');
						$AddressDiv->append($changeButton);
					}
				}

				if ($key == 'shipping'){
					$AddressDiv->hide();

					$Checkbox = htmlBase::newElement('checkbox')
					->setName('shipping_diff')
					->setLabel(sysLanguage::get('TEXT_DIFFERENT_FROM_BILLING'))
					->setLabelPosition('after')
					->addClass('shippingAddressDiff')
					->css(array(
						'margin' => '.3em'
					));

					$AddressContainer->append($Checkbox);
				}elseif ($key == 'pickup'){
					$AddressDiv->hide();

					$Checkbox = htmlBase::newElement('checkbox')
					->setName('pickup_diff')
					->setLabel('Different From Delivery Address')
					->setLabelPosition('after')
					->addClass('pickupAddressDiff')
					->css(array(
						'margin' => '.3em'
					));

					$AddressContainer->append($Checkbox);
				}

				$AddressContainer->append($AddressDiv);

				echo $AddressContainer->draw() . '<br>';
			}
		}
?>
<?php
	if($userAccount->isLoggedIn() === false){
?>

<div class="ui-widget ui-widget-content ui-corner-all">
	<div class="ui-widget-header ui-corner-all"><?php echo sysLanguage::get('TEXT_ACCOUNT_SETTINGS'); ?></div>
	<?php
	if($onePageCheckout->isNormalCheckout() && (sysConfig::get('ONEPAGE_ACCOUNT_CREATE') == 'create')){
		$Checkbox = htmlBase::newElement('checkbox')
		->setName('create_account')
		->setLabel(sysLanguage::get('TEXT_MAKE_ACCOUNT'))
		->setLabelPosition('after')
		->addClass('createAccountButton')
		->css(array(
			'margin' => '.5em'
		));

		echo $Checkbox->draw();
	}
	?>

	<table class="accountSettings" cellpadding="0" cellspacing="0" border="0" style="<?php echo ($onePageCheckout->isMembershipCheckout() || (sysConfig::get('ONEPAGE_ACCOUNT_CREATE') == 'required') ?'':'display:none;'); ?>">
		<tr>
			<td><?php echo sysLanguage::get('ENTRY_PASSWORD'); ?></td>
			<td><?php echo $BillingPasswordInput->draw(); ?></td>
			<td><div id="pstrength_password"></div></td>
		</tr>
		<tr>
			<td><?php echo sysLanguage::get('ENTRY_PASSWORD_CONFIRMATION'); ?></td>
			<td colspan="2"><?php echo $BillingPasswordConfirmInput->draw(); ?></td>
		</tr>
		<tr>
			<td colspan="3"><?php echo $NewsletterInput->draw(); ?></td>
		</tr>
	</table>
</div>

	<?php
	}
	?>