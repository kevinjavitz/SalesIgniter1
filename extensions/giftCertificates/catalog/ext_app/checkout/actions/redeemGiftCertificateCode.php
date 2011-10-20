<?php
    $error = false;
    $message = '';
    $extGiftCertificate = $appExtension->getExtension('giftCertificates');
    if (isset($_POST['gvCode']) && !empty($_POST['gvCode'])) {

        $redeemResult = $extGiftCertificate->redeemGiftVoucher($_POST['gvCode']);
        if(isset($redeemResult['errorMessage'])){
            $message = $redeemResult['errorMessage'];
            $error = true;
        } else {
            $message = $currencies->format($redeemResult['amount']) . ' redeemed. Please click the checkbox below to apply this amount to this order';
        }
    }else{
        $message = 'Please enter a valid code';
    }

    if ($onePageCheckout->isMembershipCheckout()) {
        $onePageCheckout->loadMembershipPlan();
    }
    OrderTotalModules::process();
    EventManager::attachActionResponse(array(
        'success' => ($error ? false : true),
        'message' => $message,
        'orderTotalRows' => OrderTotalModules::output(),
        'giftCertificatesTable' => $extGiftCertificate->CheckoutAddBlockAfterCart(true)
    ), 'json');
?>