<?php
	if (!isset($_POST['back_x'])){
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
	
		$Result = $Qmail->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		//Let's build a message object using the email class
		$mimemessage = new email(array('X-Mailer: osCommerce'));
		// add the message to the object
		$mimemessage->add_text($_POST['message']);
		$mimemessage->build_message();
		foreach($Result as $mInfo){
			$mimemessage->send(
				$mInfo['customers_firstname'] . ' ' . $mInfo['customers_lastname'],
				$mInfo['customers_email_address'],
				'',
				$_POST['from'],
				$_POST['subject']
			);
		}

		EventManager::attachActionResponse(
			itw_app_link('mail_sent_to=' . urlencode($mail_sent_to), 'mail', 'default'),
			'redirect'
		);
	}
?>