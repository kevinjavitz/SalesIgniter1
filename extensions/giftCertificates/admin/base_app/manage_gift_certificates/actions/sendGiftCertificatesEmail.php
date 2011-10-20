<?php
	$Qmail = Doctrine_Query::create()
        ->select('concat(customers_firstname, " ", customers_lastname) as customers_name, customers_email_address')
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
    if(!$Email || count($Email) <= 0){
        $Email = false;
        if(strstr($_POST['customers_email_address'],',')){
            $emailList = explode(',', $_POST['customers_email_address']);
            foreach($emailList as $emailListData){
                if(strstr($emailListData,'|')){
                    $emailInfo = explode('|', $emailListData);
                    $Email[] = array('customers_name' => $emailInfo[0], 'customers_email_address' => $emailInfo[1]);
                } else {
                    $Email[] = array('customers_name' => '', 'customers_email_address' => $emailListData);
                }
            }
        } else {
            if(strstr($_POST['customers_email_address'],'|')){
                $emailInfo = explode('|', $_POST['customers_email_address']);
                $Email[] = array('customers_name' => $emailInfo[0], 'customers_email_address' => $emailInfo[1]);
            } else {
                $Email[] = array('customers_name' => '', 'customers_email_address' => $_POST['customers_email_address']);
            }
        }

    }

    $from = $_POST['from'];
    $subject = $_POST['subject'];
    foreach($Email as $mInfo) {
        $extGiftCertificate = $appExtension->getExtension('giftCertificates');
        $data = $_POST;
        $data['email'] = $mInfo['customers_email_address'];
        $data['customers_firstname'] = '';
        $data['customers_lastname'] = '';
        $data['customers_id'] = 0;
        $giftCertificates = $extGiftCertificate->sendGiftCertificatesBalanceByEmail($data);
        $message = $_POST['message'];
        $message .= "\n\n" . sysLanguage::get('TEXT_TO_REDEEM') . "\n\n";
        $message .= sysLanguage::get('TEXT_VOUCHER_IS') . $giftCertificates->gift_certificates_code . "\n\n";
        $message .= sysLanguage::get('TEXT_REMEMBER') . "\n\n";
        $message .= sysLanguage::get('TEXT_VISIT') . "\n\n";

        //Let's build a message object using the email class
        $mimemessage = new email(array('X-Mailer: osCommerce bulk mailer'));
        // add the message to the object
        $mimemessage->add_text($message);
        $mimemessage->build_message();
        $mimemessage->send($mInfo['customers_name'], $mInfo['customers_email_address'], '', $from, $subject);
    }

    EventManager::attachActionResponse(array(
                                            'success' => true,
                                            'sentTo' => sprintf(sysLanguage::get('NOTICE_EMAIL_SENT_TO'), $mail_sent_to)
                                       ), 'json');
?>