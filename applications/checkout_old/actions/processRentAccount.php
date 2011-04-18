<?php
	$onePageCheckout->onePage['info']['comments'] = $_POST['comments'];
	$onePageCheckout->onePage['info']['telephone'] = (isset($_POST['billing_telephone']) ? $_POST['billing_telephone'] : '');
	$onePageCheckout->onePage['info']['newsletter'] = (isset($_POST['billing_newsletter']) ? $_POST['billing_newsletter'] : '0');
	$onePageCheckout->onePage['info']['account_action'] = 'new';
		
	Session::set('payment_rental', true);
	if (isset($_POST['source'])){
		$onePageCheckout->onePage['info']['source'] = $_POST['source'];
		if (isset($_POST['source_other'])){
			$onePageCheckout->onePage['info']['source_other'] = $_POST['source_other'];
		}
	}

	if ($userAccount->isLoggedIn() === true){
		$onePageCheckout->onePage['createAccount'] = 'false';
	}else{
		$onePageCheckout->onePage['createAccount'] = 'true';
		if (isset($_POST['password']) && !empty($_POST['password'])){
			$onePageCheckout->onePage['info']['password'] = $_POST['password'];
		}else{
			$onePageCheckout->onePage['info']['password'] = tep_create_random_value(ENTRY_PASSWORD_MIN_LENGTH);
		}
	}

	// Start - CREDIT CLASS Gift Voucher Contribution
	if (Session::exists('credit_covers') === true) $onePageCheckout->onePage['info']['payment']['id'] = '';
	// End - CREDIT CLASS Gift Voucher Contribution

	$html = '';
	$infoMsg = 'Please press the continue button to confirm your order.';
	$formUrl = itw_app_link(null, 'checkout', 'rental_process.php', $request_type);

	if (!empty($onePageCheckout->onePage['info']['payment']['id'])){
		$paymentModule = $paymentModules->getModule($onePageCheckout->onePage['info']['payment']['id']);
		if (isset($paymentModule->hasFormUrl()) && !empty($paymentModule->getFormUrl())){
			$formUrl = $paymentModule->getFormUrl();
			$infoMsg = 'Please press the continue button to proceed to the payment processors page.';
		}

		$paymentModule->onSelect();
		if (!tep_not_null($hiddenFields)){
			foreach($_POST as $varName => $val){
				if (is_array($_POST[$varName])){
					foreach($_POST[$varName] as $varName2 => $val2){
						$hiddenFields .= tep_draw_hidden_field($varName2, $val2) . "\n";
					}
				}else{
					$hiddenFields .= tep_draw_hidden_field($varName, $val) . "\n";
				}
			}
		}
	}

	$html .= '<form name="redirectForm" action="' . $formUrl . '" method="POST">' . 
		'<noscript>' . 
			$infoMsg .
			htmlBase::newElement('button')->usePreset('continue')->setType('submit')->draw() .
		'</noscript>' .
		htmlBase::newElement('button')->usePreset('continue')->setType('submit')->hide()->draw() .
		$hiddenFields .
		'<script>
			document.write(\'<div style="width:100%;height:100%;margin-left:auto;margin-top:auto;text-align:center"><img src="' . DIR_WS_HTTP_CATALOG . DIR_WS_IMAGES . 'ajax-loader.gif"><br>Processing Order, Please Wait...</div>\');
			setTimeout("redirectForm.submit()", 3000);
		</script>' .
	'</form>';
		
	EventManager::attachActionResponse($html, 'html');
?>