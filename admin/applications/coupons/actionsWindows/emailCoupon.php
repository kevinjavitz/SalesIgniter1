<?php
	$Coupons = Doctrine_Core::getTable('Coupons');
	$Coupon = $Coupons->find((int) $_GET['cID']);
	$boxHeading = sysLanguage::get('TEXT_INFO_HEADING_EMAIL_COUPON');

	$infoBox = htmlBase::newElement('infobox');
	$infoBox->setHeader('<b>' . $boxHeading . '</b>');
	$infoBox->setButtonBarLocation('top');

	$sendButton = htmlBase::newElement('button')->addClass('sendButton')->usePreset('email')->setText(sysLanguage::get('TEXT_BUTTON_SEND_EMAIL'));
	$cancelButton = htmlBase::newElement('button')->addClass('cancelButton')->usePreset('cancel');

	$infoBox->addButton($sendButton)->addButton($cancelButton);

	$customerDrop = htmlBase::newElement('selectbox')
	->setName('customers_email_address');
	
	$customerDrop->addOption('', sysLanguage::get('TEXT_SELECT_CUSTOMER'));
	$customerDrop->addOption('***', sysLanguage::get('TEXT_ALL_CUSTOMERS'));
	$customerDrop->addOption('**D', sysLanguage::get('TEXT_NEWSLETTER_CUSTOMERS'));
	
	$Qmail = Doctrine_Query::create()
	->select('customers_email_address, customers_firstname, customers_lastname')
	->from('Customers')
	->orderBy('customers_lastname')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if ($Qmail){
		foreach($Qmail as $mInfo){
			$customerDrop->addOption(
				$mInfo['customers_email_address'],
				$mInfo['customers_lastname'] . ', ' . $mInfo['customers_firstname'] . ' (' . $mInfo['customers_email_address'] . ')'
			);
		}
	}
	
	$infoBox->addContentRow('<table cellpadding="3" cellspacing="0" border="0">' . 
		'<tr>' . 
			'<td>' . sysLanguage::get('TEXT_INFO_COUPON') . '</td>' . 
			'<td>' . $Coupon->CouponsDescription[0]->coupon_name . '</td>' . 
		'</tr>' . 
		'<tr>' . 
			'<td>' . sysLanguage::get('ENTRY_EMAIL_CUSTOMER') . '</td>' . 
			'<td>' . $customerDrop->draw() . '</td>' . 
		'</tr>' . 
		'<tr>' . 
			'<td>' . sysLanguage::get('ENTRY_EMAIL_FROM') . '</td>' . 
			'<td>' . tep_draw_input_field('from', sysConfig::get('EMAIL_FROM')) . '</td>' . 
		'</tr>' . 
		'<tr>' . 
			'<td>' . sysLanguage::get('ENTRY_EMAIL_SUBJECT') . '</td>' . 
			'<td>' . tep_draw_input_field('subject') . '</td>' . 
		'</tr>' . 
		'<tr>' . 
			'<td valign="top">' . sysLanguage::get('ENTRY_EMAIL_MESSAGE') . '</td>' . 
			'<td>' . tep_draw_textarea_field('message', 'soft', '60', '15') . '</td>' . 
		'</tr>' . 
	'</table>');
	
	EventManager::attachActionResponse($infoBox->draw(), 'html');
?>