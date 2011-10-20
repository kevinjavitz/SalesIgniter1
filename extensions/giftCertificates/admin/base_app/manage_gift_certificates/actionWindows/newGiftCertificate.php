<?php
	$GiftCertificates = Doctrine_Core::getTable('GiftCertificates');
    $giftCertificatesPurchaseTypes = Doctrine_Core::getTable('GiftCertificatesToPurchaseTypes');
    $giftCertificatesDescriptions = Doctrine_Core::getTable('GiftCertificatesDescription');
    if (isset($_GET['gcID'])){
        $GiftCertificate = $GiftCertificates->find((int) $_GET['gcID']);
        $boxHeading = sysLanguage::get('TEXT_INFO_HEADING_EDIT_GIFT_CERTIFICATE');
        $boxIntro = sysLanguage::get('TEXT_INFO_EDIT_INTRO');
    }else{
        $GiftCertificate = $GiftCertificates->getRecord();
        $boxHeading = sysLanguage::get('TEXT_INFO_HEADING_NEW_GIFT_CERTIFICATE');
        $boxIntro = sysLanguage::get('TEXT_INFO_INSERT_INTRO');
    }

    $infoBox = htmlBase::newElement('infobox');
    $infoBox->setHeader('<b>' . $boxHeading . '</b>');
    $infoBox->setButtonBarLocation('top');

    $saveButton = htmlBase::newElement('button')->addClass('saveButton')->usePreset('save');
    $cancelButton = htmlBase::newElement('button')->addClass('cancelButton')->usePreset('cancel');

    $infoBox->addButton($saveButton)->addButton($cancelButton);

    $purchaseTypeBoxes = array();

    foreach($purchaseTypeNames as $name => $text){
        if (isset($_GET['gcID'])){
            $giftCertificatesPurchaseType = $giftCertificatesPurchaseTypes->findOneByGiftCertificatesIdAndPurchaseType((int)$_GET['gcID'], $name);
        }
        $checkbox = htmlBase::newElement('input')
                ->setType('checkbox')
                ->setName('gift_certificates_purchase_type['.$name.']')
                ->val($name)
                ->setLabel($text)
                ->setLabelPosition('after')
                ->setChecked(($giftCertificatesPurchaseType !== null && $giftCertificatesPurchaseType->purchase_type == $name))
                ->draw();
        $inputbox = htmlBase::newElement('input')
                ->setType('text')
                ->setName('gift_certificates_purchase_type_value['.$name.']')
                ->setLabel(sprintf(sysLanguage::get('TEXT_GIFT_CERTIFICATES_PURCHASE_TYPE_VALUE'), $text))
                ->setLabelPosition('before')
                ->val(($giftCertificatesPurchaseType !== null ? $giftCertificatesPurchaseType->gift_certificates_value : 0))
                ->draw();

        EventManager::notify('GiftCertificatesEditPurchaseTypeBeforeOutput', &$checkbox, $name, $GiftCertificate);

        $purchaseTypeBoxes[] = $checkbox . $inputbox;
    }

    $infoBox->addContentRow($boxIntro);
    $infoBox->addContentRow(sysLanguage::get('TEXT_INFO_GIFT_CERTIFICATE_STATUS') . '<br>' . tep_draw_radio_field('gift_certificates_status', 'Y', ($GiftCertificate->gift_certificates_status == 'Y')) . '&nbsp;' . sysLanguage::get('TEXT_ENABLED') . '&nbsp;' . tep_draw_radio_field('gift_certificates_status', 'N', ($GiftCertificate->gift_certificates_status == 'N')) . '&nbsp;' . sysLanguage::get('TEXT_DISABLED'));
    $infoBox->addContentRow(sysLanguage::get('TEXT_INFO_GIFT_CERTIFICATE_PRICE') . ' - ' . sysLanguage::get('TEXT_INFO_GIFT_CERTIFICATE_PRICE_HELP') . '<br>' . tep_draw_input_field('gift_certificates_price', $GiftCertificate->gift_certificates_price));
    $infoBox->addContentRow(sysLanguage::get('TEXT_ENTRY_TAX_CLASS') . '<br>' . tep_draw_pull_down_menu('gift_certificates_tax_class_id', $tax_class_array, $GiftCertificate->gift_certificates_tax_class_id));
    $infoBox->addContentRow(sysLanguage::get('TEXT_INFO_GIFT_CERTIFICATE_PURCHASE_TYPE') . ' - ' . sysLanguage::get('TEXT_INFO_GIFT_CERTIFICATE_PURCHASE_TYPE_HELP') . '<br>' . implode('<br>', $purchaseTypeBoxes));
    $infoBox->addContentRow(sysLanguage::get('TEXT_INFO_GIFT_CERTIFICATE_NAME') . ' - ' . sysLanguage::get('TEXT_INFO_GIFT_CERTIFICATE_NAME_HELP'));

    foreach(sysLanguage::getLanguages() as $lInfo){
        if(isset($_GET['gcID'])) {
            $giftCertificatesDescription = $giftCertificatesDescriptions->findOneByGiftCertificatesIdAndLanguageId((int) $_GET['gcID'], (int) $lInfo['id']);
        }
        $infoBox->addContentRow($lInfo['showName']('&nbsp;') . ': ' .  htmlBase::newElement('input')
                                        ->setType('text')
                                        ->setName('gift_certificates_name[' . $lInfo['id'] . ']')
                                        ->val($giftCertificatesDescription->gift_certificates_name)
                                        ->draw());
        $Descriptions[$lInfo['id']] = $lInfo['showName']('&nbsp;') . ': ' . tep_draw_textarea_field('gift_certificates_description[' . $lInfo['id'] . ']','physical','24','3', $giftCertificatesDescription->gift_certificates_description);
    }

    $infoBox->addContentRow(sysLanguage::get('TEXT_INFO_GIFT_CERTIFICATE_DESC') . ' - ' . sysLanguage::get('TEXT_INFO_GIFT_CERTIFICATE_DESC_HELP'));
    foreach($Descriptions as $lID => $text){
        $infoBox->addContentRow($text);
    }

    EventManager::attachActionResponse($infoBox->draw(), 'html');
?>