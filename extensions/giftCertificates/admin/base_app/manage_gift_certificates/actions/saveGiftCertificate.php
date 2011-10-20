<?php
    $GiftCertificates = Doctrine_Core::getTable('GiftCertificates');
    if (isset($_GET['gcID'])){
        $GiftCertificates = $GiftCertificates->find((int)$_GET['gcID']);
        $CurrentGiftCertificatesPurchaseTypes = Doctrine_Core::getTable('GiftCertificatesToPurchaseTypes')->findByGiftCertificatesId((int)$_GET['gcID']);
        if($CurrentGiftCertificatesPurchaseTypes !== null) {
            foreach($CurrentGiftCertificatesPurchaseTypes as $CurrentGiftCertificatesPurchaseType){
                $CurrentGiftCertificatesPurchaseType->delete();
            }
        }
    }else{
        $GiftCertificates = $GiftCertificates->create();
    }
    $GiftCertificates->gift_certificates_price = $_POST['gift_certificates_price'];

    foreach(sysLanguage::getLanguages() as $lInfo){
        $GiftCertificates->GiftCertificatesDescription[$lInfo['id']]->language_id = $lInfo['id'];
        $GiftCertificates->GiftCertificatesDescription[$lInfo['id']]->gift_certificates_name = $_POST['gift_certificates_name'][$lInfo['id']];
        $GiftCertificates->GiftCertificatesDescription[$lInfo['id']]->gift_certificates_description = $_POST['gift_certificates_description'][$lInfo['id']];
    }
    $i = 0;
    foreach($purchaseTypeNames as $name => $text){
        if(isset($_POST['gift_certificates_purchase_type'][$name])) {
            $GiftCertificates->GiftCertificatesToPurchaseTypes[$i]->purchase_type = $name;
            $GiftCertificates->GiftCertificatesToPurchaseTypes[$i]->gift_certificates_value = $_POST['gift_certificates_purchase_type_value'][$name];
            $i++;
        }

    }
    EventManager::notify('GiftCertificatesEditBeforeSave', $GiftCertificates);
    $GiftCertificates->save();
    EventManager::attachActionResponse(array(
        'success' => true,
        'gcID'     => $GiftCertificates->gift_certificates_id
    ), 'json');
?>