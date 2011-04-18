<?php
	$valid = true;
	if (sysConfig::get('CATPCHA_ENABLED') == 'True'){
		include('captcha/securimage.php');
		$img = new Securimage();
		$valid = $img->check($_POST['code']);
	}
	if (!$valid){
		$messageStack->add('pageStack', 'Sorry, the code entered was invalid', 'error');
	}else{
		if (isset($_GET['action']) && ($_GET['action'] == 'send') && isset($_POST['name'])&& isset($_POST['email'])&& isset($_POST['enquiry'])) {
			$name = $_POST['name'];
			$email_address = $_POST['email'];
			$enquiry = $_POST['enquiry'];
			if ($userAccount->validateEmailAddress($email_address) && !empty($name) && !empty($enquiry)) {
				tep_mail(sysConfig::get('STORE_OWNER'), sysConfig::get('STORE_OWNER_EMAIL_ADDRESS'), sprintf(sysLanguage::get('EMAIL_SUBJECT'),$name), $enquiry, $name, $email_address);

				EventManager::attachActionResponse(itw_app_link(null, 'contact_us', 'success'), 'redirect');
			} else {
				$error = true;

				$messageStack->add('pageStack', sysLanguage::get('ENTRY_EMAIL_CHECK_ERROR'), 'error');
			}
		}
	}
?>