<?php
	$appPage = $App->getAppPage();
	$appContent = $App->getAppContentFile();

	$App->addJavascriptFile('ext/jQuery/external/pass_strength/jQuery.pstrength.js');

	require('includes/classes/http_client.php');
	include('includes/functions/crypt.php');
    $navigation->set_snapshot();
	if (sysConfig::get('ONEPAGE_LOGIN_REQUIRED') == 'true'){
		if ($userAccount->isLoggedIn() === false){
			if (!isset($_GET['checkoutType']) || (isset($_GET['checkoutType']) && $_GET['checkoutType'] == 'default')){
				$navigation->set_snapshot(array('mode' => 'SSL', 'page' => 'application.php', 'get' => 'app=checkout&appPage=default'));
				tep_redirect(itw_app_link(null, 'account', 'login', 'SSL'));
			}
		}
	}

	if (isset($_GET['rType'])){
		header('content-type: text/html; charset=' . sysLanguage::getCharset());
	}



	if ($userAccount->isLoggedIn() && $userAccount->plugins['membership']->isRentalMember() && $userAccount->plugins['membership']->isActivated() && isset($_GET['checkoutType']) && $_GET['checkoutType'] == 'rental' && !isset($_GET['isUpgrade'])){
		tep_redirect(itw_app_link(null, 'account', 'default', 'SSL'));	
	}

	EventManager::notify('CheckoutBeforeExecute');

	$isPostPage = (isset($_POST) && !empty($_POST));

	if ($isPostPage === false && $action != 'remotePaymentProcess'){
		if (!isset($ShoppingCart->cartID)){
			$ShoppingCart->cartID = $ShoppingCart->generateCartId();
		}
		Session::set('cartID', $ShoppingCart->cartID);
	}

	require('includes/classes/onepage_checkout.php');
	$onePageCheckout = new osC_onePageCheckout();

    if (isset($_GET['checkoutType'])){
        switch($_GET['checkoutType']){
            case 'rental':
                if ($onePageCheckout->getMode() == ''){
                    $onePageCheckout->setMode('membership');
                }
                break;
            case 'giftCertificate':
                if ($onePageCheckout->getMode() == ''){
                    $onePageCheckout->setMode('giftCertificate');
                }
                break;
            default:
                if ($onePageCheckout->getMode() == ''){
                    $onePageCheckout->setMode('default');
                }
                break;
        }
    }
    /*
	if (isset($_GET['checkoutType']) && $_GET['checkoutType'] == 'rental'){
		if ($onePageCheckout->getMode() == ''){
			$onePageCheckout->setMode('membership');
		}
	}else{
		if ($onePageCheckout->getMode() == ''){
			$onePageCheckout->setMode('default');
		}
	}
    */
	if ($isPostPage === false && $action != 'remotePaymentProcess'){
		EventManager::notify('CheckoutPreInit');

		$onePageCheckout->init();

		EventManager::notify('CheckoutPostInit');

		$onePageCheckout->setShippingStatus();
	}
	
	if ($onePageCheckout->onePage['shippingEnabled'] === true){
		if ($onePageCheckout->isNormalCheckout() === true){
			$total_weight = $ShoppingCart->showWeight();
			$total_count = $ShoppingCart->countContents();
		}else{
			$total_weight = 1;
			$total_count = 1;
		}

		OrderShippingModules::calculateWeight();
	}
    if ($action != 'remotePaymentProcess'){
		if (isset($onePageCheckout->onePage['info']['payment']['id'])){
			OrderPaymentModules::setSelected($onePageCheckout->onePage['info']['payment']['id']);
		}

		if ($onePageCheckout->onePage['info']['shipping'] !== false && !empty($onePageCheckout->onePage['info']['shipping'])){
			$mInfo = explode('_', $onePageCheckout->onePage['info']['shipping']['id']);
			OrderShippingModules::setSelected($mInfo[0], $mInfo[1]);
		}
	}


	require(sysConfig::getDirFsCatalog() . 'includes/classes/order.php');
	$order = new OrderProcessor;
	OrderShippingModules::loadModules();
	OrderPaymentModules::loadModules();
	OrderTotalModules::loadModules();
	if ($onePageCheckout->isMembershipCheckout() === true){
		$onePageCheckout->loadMembershipPlan();
	} else if($onePageCheckout->isGiftCertificateCheckout() === true){
            $onePageCheckout->loadGiftCertificates();
        }
	
	$breadcrumb->add('Checkout');
	$breadcrumb->add('Address Entry');
?>