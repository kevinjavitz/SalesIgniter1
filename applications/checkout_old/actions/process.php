<?php
	$onePageCheckout->onePage['info']['comments'] = $_POST['comments'];
	$onePageCheckout->onePage['info']['telephone'] = (isset($_POST['billing_telephone']) ? $_POST['billing_telephone'] : '');
	$onePageCheckout->onePage['info']['newsletter'] = (isset($_POST['billing_newsletter']) ? $_POST['billing_newsletter'] : '0');
		
	if (isset($_POST['source'])){
		$onePageCheckout->onePage['info']['source'] = $_POST['source'];
		if (isset($_POST['source_other'])){
			$onePageCheckout->onePage['info']['source_other'] = $_POST['source_other'];
		}
	}

	if ($userAccount->isLoggedIn() === true){
		$onePageCheckout->onePage['createAccount'] = 'false';
	}else{
		if (isset($_POST['password']) && !empty($_POST['password'])){
			$onePageCheckout->onePage['createAccount'] = 'true';
			$onePageCheckout->onePage['info']['password'] = $_POST['password'];
		}elseif (ONEPAGE_ACCOUNT_CREATE == 'create'){
			$onePageCheckout->onePage['createAccount'] = 'true';
			$onePageCheckout->onePage['info']['password'] = tep_create_random_value(ENTRY_PASSWORD_MIN_LENGTH);
		}
	}

	$checkPayment = true;
	if (MODULE_ORDER_TOTAL_COUPON_STATUS == 'True'){
		// Start - CREDIT CLASS Gift Voucher Contribution
		unset($_POST['gv_redeem_code']);
		//$orderTotalModules->collect_posts();
		//$orderTotalModules->pre_confirmation_check();
		if (Session::exists('credit_covers') === true) $checkPayment = false;
		// End - CREDIT CLASS Gift Voucher Contribution
	}
	
	EventManager::notify('OnepageCheckoutProcessCheckout', &$this);
		
	$html = '';
	$error = false;
	$infoMsg = 'Please press the continue button to confirm your order.';
	$formUrl = itw_app_link('action=processNormalCheckout', 'checkout', 'default', $request_type);
		
	if ($checkPayment === true){
		$paymentModule = OrderPaymentModules::getModule($onePageCheckout->onePage['info']['payment']['id']);
		if ($paymentModule->hasFormUrl()){
			$formUrl = $paymentModule->getFormUrl();
			$infoMsg = 'Please press the continue button to proceed to the payment processors page.';
		}
		
		$e = $paymentModule->validatePost();
		if ($e === true){
			if ($paymentModule->hasHiddenFields()){
				$hiddenFields = $paymentModule->getHiddenFields();
			}
		}else{
			$error = true;
			$redirectUrl = $e['redirectTo'];
			$messageStack->addSession('pageStack', $e['errorMsg'], 'error');
		}
	}else{
		$hiddenFields = '';
	}
		
	if ($error === false){
		if (empty($hiddenFields)){
			$hiddenFields = $onePageCheckout->drawHiddenFieldsFromArray($_POST);
		}

		$html .= '<form name="redirectForm" action="' . $formUrl . '" method="POST">' . 
			'<noscript>' . 
				$infoMsg .
				htmlBase::newElement('button')->usePreset('continue')->setType('submit')->draw() .
			'</noscript>' .
			htmlBase::newElement('button')->usePreset('continue')->setType('submit')->hide()->draw() .
			$hiddenFields .
			'<script>' . 
				'document.write(\'<div style="width:100%;height:100%;margin-left:auto;margin-top:auto;text-align:center"><img src="' . sysConfig::getDirWsCatalog().'/images/'. 'ajax-loader.gif"><br>Processing Order, Please Wait...</div>\');
	            setTimeout("redirectForm.submit()", 3000);' .
			'</script>' . 
		'</form>';
	
		EventManager::attachActionResponse($html, 'html');
	}else{
		EventManager::attachActionResponse($redirectUrl, 'redirect');
	}
?>