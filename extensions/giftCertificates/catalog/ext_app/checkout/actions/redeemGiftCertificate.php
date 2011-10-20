<?php
    $error = false;
    $success = false;

    if(isset($_POST['apply'])){
        if($_POST['apply'] == 'true')
        {
            if (isset($_POST['gvBalance']) && (int)$_POST['gvBalance'] > 0) {
                Session::set('giftCertificate_balance', array($_POST['purchaseType'] => (float)$_POST['gvBalance']));
            }
        } else {
            Session::remove('giftCertificate_balance');
        }
    }

    if ($onePageCheckout->isMembershipCheckout()) {
        $onePageCheckout->loadMembershipPlan();
    }
    OrderTotalModules::process();
    EventManager::attachActionResponse(array(
                                            'success' => ($error ? false : true),
                                            'orderTotalRows' => OrderTotalModules::output()
                                       ), 'json');
?>