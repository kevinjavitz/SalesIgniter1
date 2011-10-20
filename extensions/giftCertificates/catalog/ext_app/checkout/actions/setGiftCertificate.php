<?php
	$giftCertificatesId = $_POST['gcID'];
    Session::set('giftCertificatesId', $giftCertificatesId);

    $giftCertificates = Doctrine_Query::create()
            ->from('GiftCertificates gc')
            ->leftJoin('gc.GiftCertificatesDescription gcd')
            ->leftJoin('gc.TaxRates tt')
            ->where('gc.gift_certificates_id=?', $giftCertificatesId)
            ->andWhere('gcd.language_id=?', Session::get('languages_id'))
            ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
    $onePageCheckout->onePage['giftCertificate'] = array(
        'gift_certificates_id'                => $giftCertificatesId,
        'name'              => $giftCertificates[0]['GiftCertificatesDescription'][0]['gift_certificates_name'],
        'price'             => $giftCertificates[0]['gift_certificates_price'],
        'tax_class'         => $giftCertificates[0]['gift_certificates_tax_class_id'],
        'tax_rate'          => (float)$giftCertificates[0]['TaxRates']['tax_rate']
    );

    $onePageCheckout->loadGiftCertificates();

    OrderTotalModules::process();

    EventManager::attachActionResponse(array(
        'success' => true,
        'orderTotalRows' => OrderTotalModules::output()
    ), 'json');
?>