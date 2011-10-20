<?php
    if(isset($_POST['gcBalance']) && (int)$_POST['gcBalance'] > 0){
        $extGiftCertificate = $appExtension->getExtension('giftCertificates');
        $transactionData = array(
            'type' => 'applied',
            'amount' => $_POST['gcBalance'],
            'values' => array($_POST['purchaseType'] => $_POST['gcBalance']),
            'customers_id' => $_POST['customers_id']
        );
        switch($_POST['actionAddRemove']){
            case 'add':
                $transactionData['transaction_type'] = '+';
                break;

            case 'deduct':
                $transactionData['transaction_type'] = '-';
                break;
        }

    }
    $extGiftCertificate->insertGiftCertificateTransaction($transactionData);

    $json = array(
        'success'   => true,
        'msgStack'  => $messageStack->parseTemplate('pageStack', $_POST['gcBalance'] . ' Gift Certificates Balance ' . $_POST['actionAddRemove'] . 'ed for customer.', 'success')
    );
    if (isset($_GET['rType']) && $_GET['rType'] == 'ajax'){
        EventManager::attachActionResponse($json, 'json');
    }else{
        $messageStack->addSession('pageStack', 'Page not accesible', 'error');
        EventManager::attachActionResponse(itw_app_link('', 'customers', 'default'), 'redirect');
    }
?>