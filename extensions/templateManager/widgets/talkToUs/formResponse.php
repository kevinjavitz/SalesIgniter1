<?php
    chdir('../../../../');
    require('includes/application_top.php');

    $valid = true;
    if (sysConfig::get('CATPCHA_ENABLED') == 'True'){
        include('captcha/securimage.php');
        $img = new Securimage();
        $valid = $img->check($_POST['code']);
    }
	if (!$valid){
		$messageStack->addSession('pageStack', 'Sorry, the code entered was invalid', 'error');
	}else{

		$error = false;
		$from_email_address = $_POST['email_address'];
		$from_name = $_POST['name'];
		$to_name = sysConfig::get('STORE_OWNER');
		$to_email_address = sysConfig::get('STORE_OWNER_EMAIL_ADDRESS');
		//$to_email_address = 'cristian@itwebexperts.com';
		if (isset($_POST['message']) && !empty($_POST['message'])){
			$message = 'FROM: '. $from_name. '<br/>'.
					   'EMAIL ADDRESS: '. $from_email_address. '<br/>'.
					   'TELEPHONE NUMBER: '. $_POST['phone'] . '<br/>'.
					   'MESSAGE: '.$_POST['message'];
		}

		if (empty($from_name)) {
		  $error = true;
		  $messageStack->addSession('pageStack', 'Name field is empty', 'error');
		}

		if (!$userAccount->validateEmailAddress($from_email_address)) {
		  $error = true;
		  $messageStack->addSession('pageStack', 'Email address is not valid or empty', 'error');
		}

		if (empty($message)) {
		  $error = true;
		  $messageStack->addSession('pageStack', 'Empty message', 'error');
		}

		if ($error == false) {
			$email_event = new emailEvent('talk_to_us', $userAccount->getLanguageId());
			$email_event->setVar('toName', $to_name);
			$email_event->setVar('message', $message);
			$email_event->sendEmail(array(
				'from_email' => $from_email_address,
				'from_name' => $from_name,
				'email' => $to_email_address,
				'name' => $to_name
			));

		  $messageStack->addSession('pageStack', 'Thank you.  We\'ll get back to you as soon as possible.  If you need immediate assistance, please call us at 303-442-9097', 'success');
		}
	}
    tep_redirect($_POST['url']);
 	require('includes/application_bottom.php');
?>