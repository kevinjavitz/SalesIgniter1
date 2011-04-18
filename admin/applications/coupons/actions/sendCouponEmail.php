<?php
	$Qmail = Doctrine_Query::create()
	->select('customers_firstname, customers_lastname, customers_email_address')
	->from('Customers');
	
	switch ($_POST['customers_email_address']) {
		case '***':
			$mail_sent_to = sysLanguage::get('TEXT_ALL_CUSTOMERS');
			break;
		case '**D':
			$Qmail->where('customers_newsletter = ?', '1');
			$mail_sent_to = sysLanguage::get('TEXT_NEWSLETTER_CUSTOMERS');
			break;
		default:
			$Qmail->where('customers_email_address = ?', $_POST['customers_email_address']);
			$mail_sent_to = $_POST['customers_email_address'];
			break;
	}
	$Email = $Qmail->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	
	$Qcoupon = Doctrine_Query::create()
	->select('c.coupon_code, cd.coupon_name')
	->from('Coupons c')
	->leftJoin('c.CouponsDescription cd')
	->where('c.coupon_id = ?', (int) $_GET['cID'])
	->andWhere('cd.language_id = ?', Session::get('languages_id'))
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	$from = $_POST['from'];
	$subject = $_POST['subject'];
	foreach($Email as $mInfo) {
		$message = $_POST['message'];
		$message .= "\n\n" . sysLanguage::get('TEXT_TO_REDEEM') . "\n\n";
		$message .= sysLanguage::get('TEXT_VOUCHER_IS') . $Qcoupon[0]['coupon_code'] . "\n\n";
		$message .= sysLanguage::get('TEXT_REMEMBER') . "\n\n";
		$message .= sysLanguage::get('TEXT_VISIT') . "\n\n";

		//Let's build a message object using the email class
		$mimemessage = new email(array('X-Mailer: osCommerce bulk mailer'));
		// add the message to the object
		$mimemessage->add_text($message);
		$mimemessage->build_message();
		$mimemessage->send($mInfo['customers_firstname'] . ' ' . $mInfo['customers_lastname'], $mInfo['customers_email_address'], '', $from, $subject);
	}

	EventManager::attachActionResponse(array(
		'success' => true,
		'sentTo' => sprintf(sysLanguage::get('NOTICE_EMAIL_SENT_TO'), $mail_sent_to)
	), 'json');
?>