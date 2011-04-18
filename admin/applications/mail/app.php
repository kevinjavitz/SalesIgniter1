<?php
	$appContent = $App->getAppContentFile();

	if ($App->getPageName() == 'preview' && !isset($_POST['customers_email_address'])){
	    $messageStack->add('pageStack', sysLanguage::get('ERROR_NO_CUSTOMER_SELECTED'), 'error');
	}
	
	if (isset($_GET['mail_sent_to'])){
		$messageStack->add('pageStack', sprintf(sysLanguage::get('NOTICE_EMAIL_SENT_TO'), $_GET['mail_sent_to']), 'success');
	}
?>