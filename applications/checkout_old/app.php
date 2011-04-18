<?php
	$appPage = $App->getAppPage();
	$appContent = $App->getAppContentFile();

	if ($appPage == 'rental_process'){
	}elseif ($appPage != 'success'){
		if ($appPage !== 'default'){
			$App->addJavascriptFile('applications/checkout/javascript/default.js');
		}
		$App->addJavascriptFile('ext/jQuery/external/ajaxq/jQuery.ajaxq.js');
		$App->addJavascriptFile('ext/jQuery/external/pass_strength/jQuery.pstrength.js');

		require('includes/classes/http_client.php');
		include('includes/functions/crypt.php');

		if (ONEPAGE_LOGIN_REQUIRED == 'true'){
			if ($userAccount->isLoggedIn() === false){
				if (!isset($_GET['checkoutType']) || (isset($_GET['checkoutType']) && $_GET['checkoutType'] != 'rental')){
					$navigation->set_snapshot(array('mode' => 'SSL', 'page' => 'application.php', 'get' => 'app=checkout&appPage=default'));
					tep_redirect(itw_app_link(null, 'account', 'login', 'SSL'));
				}
			}
		}

		if ($action == 'processNormalCheckout'){
			if (!isset($userAccount->plugins['addressBook']->addresses['billing'])){
				tep_redirect(itw_app_link(null, 'checkout', 'default', 'SSL'));
			}

			// avoid hack attempts during the checkout procedure by checking the internal cartID
			if (isset($ShoppingCart->cartID) && Session::exists('cartID') === true){
				if ($ShoppingCart->cartID != Session::get('cartID')) {
					tep_redirect(itw_app_link(null, 'checkout', 'default', 'SSL'));
				}
			}
		}

		if (isset($_GET['rType'])){
			header('content-type: text/html; charset=' . sysLanguage::getCharset());
		}

		if (isset($_POST['updateQuantities_x'])){
			//$action = 'updateQuantities';
		}

		require('includes/classes/onepage_checkout.php');
		$onePageCheckout = new osC_onePageCheckout();
		if (isset($_GET['checkoutType']) && $_GET['checkoutType'] == 'rental'){
			$onePageCheckout->setMode('membership');
		}

		if ($appPage == 'default' && $action != 'processNormalCheckout'){
			if (!isset($_GET['rType']) && !isset($_GET['action']) && !isset($_POST['action'])){
				EventManager::notify('CheckoutPreInit');

				$onePageCheckout->init();

				EventManager::notify('CheckoutPostInit');
			}
			$onePageCheckout->setShippingStatus();
		}

		if ($action == 'setPaymentMethod'){
			$paymentModules = OrderPaymentModules::getModule($_POST['method'], true);
		}elseif (isset($onePageCheckout->onePage['info']['payment']['id'])){
			$paymentModules = OrderPaymentModules::getModule($onePageCheckout->onePage['info']['payment']['id'], true);
		}
		
		if ($action == 'processNormalCheckout'){
			if (Session::exists('credit_covers') === true && Session::get('credit_covers') === true){
				$onePageCheckout->onePage['info']['payment']['id'] = '';
				$paymentModules = null;
			}
		}
/*		
		require(DIR_WS_CLASSES . 'payment.php');
		if (!empty($action) && $action != 'processNormalCheckout'){
			if ($action == 'setPaymentMethod'){
				$paymentModules = new payment($_POST['method']);
			}elseif ($action == 'updatePaymentMethods'){
				$paymentModules = new payment;
			}elseif (isset($onePageCheckout->onePage['info']['payment']['id'])){
				$paymentModules = new payment($onePageCheckout->onePage['info']['payment']['id']);
			}
		}else{
			if ($action == 'processNormalCheckout'){
				// #################### Added CCGV ######################
				if (Session::exists('credit_covers') === true && Session::get('credit_covers') === true){
					$onePageCheckout->onePage['info']['payment']['id'] = '';
				}
				// #################### End Added CGV ######################
				$paymentModules = new payment($onePageCheckout->onePage['info']['payment']['id']);
			}elseif ($appPage == 'default'){
				$paymentModules = new payment;
			}
		}
*/
		if ($appPage == 'default'){
			// register a random ID in the session to check throughout the checkout procedure
			// against alterations in the shopping cart contents			
			if (!isset($ShoppingCart->cartID)){
				$ShoppingCart->cartID = $ShoppingCart->generateCartId();
			}
			Session::set('cartID', $ShoppingCart->cartID);
		
			// if the order contains only virtual products, forward the customer to the billing page as
			// a shipping address is not needed
			if (Session::exists('payment_rental') === false || (Session::exists('payment_rental') === true && Session::get('payment_rental') === false)){
				$total_weight = $ShoppingCart->showWeight();
				$total_count = $ShoppingCart->countContents();

				if (method_exists($ShoppingCart, 'countContentsVirtual')){
					// Start - CREDIT CLASS Gift Voucher Contribution
					$total_count = $ShoppingCart->countContentsVirtual();
					// End - CREDIT CLASS Gift Voucher Contribution
				}
			}else{
				$total_weight = 1;
				$total_count = 1;
			}
		}

		if ($onePageCheckout->onePage['shippingEnabled'] === true){
			if (!empty($action) && $action != 'processNormalCheckout'){
				if ($action == 'setShippingMethod'){
					$mInfo = explode('_', $_POST['method']);
				}elseif (isset($onePageCheckout->onePage['info']['shipping']['id'])){
					$mInfo = explode('_', $onePageCheckout->onePage['info']['shipping']['id']);
				}
			}else{
				if ($action == 'processNormalCheckout'){
					if ($onePageCheckout->onePage['info']['shipping'] !== false){
						$mInfo = explode('_', $onePageCheckout->onePage['info']['shipping']['id']);
					}
				}
			}
				
			if (isset($mInfo) && is_array($mInfo)){
				OrderShippingModules::setSelected($mInfo[0], $mInfo[1]);
			}
		}

		require(DIR_WS_CLASSES . 'order.php');
		$order = new order;

		if ($action == 'processNormalCheckout'){
			$onePageCheckout->loadOrdersVars();
		}
		$onePageCheckout->fixTaxes();

		$orderTotalModules = new OrderTotalModules();
		if ($action == 'processNormalCheckout'){
			$order->newOrder['orderTotals'] = $orderTotalModules->process();
			
			EventManager::notify('CheckoutProcessPreProcess', $order);
		}elseif ($appPage == 'default'){
			$orderTotalModules->process();
		}
		
		/*
		require(DIR_WS_CLASSES . 'order_total.php');
		$orderTotalModules = new order_total;
		if ($action == 'processNormalCheckout'){
			$order->newOrder['orderTotals'] = $orderTotalModules->process();

			EventManager::notify('CheckoutProcessPreProcess', $order);
		}elseif ($appPage == 'default'){
			$orderTotalModules->process();
		}
		*/
		$breadcrumb->add(sysLanguage::get('NAVBAR_TITLE_1'), itw_app_link(null, 'checkout', $appPage, $request_type));
	}else{
		require_once('includes/functions/password_funcs.php');
		require('includes/classes/onepage_checkout.php');
		$onePageCheckout = new osC_onePageCheckout();
		$onePageCheckout->createCustomerAccount();

		if (isset($onePageCheckout->onePage) && $onePageCheckout->onePage['createAccount'] == 'true'){
			$userAccount->processLogIn(
				$onePageCheckout->onePage['info']['email_address'],
				$onePageCheckout->onePage['info']['password']
			);
		}
		$breadcrumb->add(sysLanguage::get('NAVBAR_TITLE_1'));
	}

	function buildInfobox($header, $contents){
		$info_box_contents = array();
		$info_box_contents[] = array('text' => utf8_encode($header));
		new infoBoxHeading($info_box_contents, false, false);

		$info_box_contents = array();
		$info_box_contents[] = array('text' => utf8_encode($contents));
		new infoBox($info_box_contents);
	}

	function fixSeoLink($url){
		return str_replace('&amp;', '&', $url);
	}
?>