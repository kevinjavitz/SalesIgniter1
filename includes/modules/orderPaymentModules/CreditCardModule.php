<?php
/*
	Rental Store Version 2

	I.T. Web Experts
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/
	require(sysConfig::getDirFsCatalog() . 'includes/classes/curl/Response.php');
	require(sysConfig::getDirFsCatalog() . 'includes/classes/curl/Request.php');
	
	abstract class CreditCardModule extends PaymentModuleBase {
		public $requireCvv = false;
		public $gatewayResponse = false;
		public $testMode = false;
		public $allowedTypes = array();
		public $acceptedTypes = array();
		
		public $cardTypes = array(
			'Visa' => 'Visa',
			'Mastercard' => 'Mastercard',
			'Discover' => 'Discover',
			'Amex' => 'American Express',
			'Delta' => 'Delta',
			'Switch' => 'Switch',
			'Diners' => 'Diners',
			'Solo' => 'Solo',
			'Laser' => 'Laser'
		);
		
		public function getCardImages(){
			$cc_images = '';
			foreach($this->allowedTypes as $k => $v){
				$cc_images .= tep_image(sysConfig::get('DIR_WS_ICONS') . $k . '.gif', $v);
			}
			return $cc_images;
		}
		
		public function validatePost(){
			global $messageStack, $onePageCheckout;
			$result = self::validateCreditCard($_POST, $this->requireCvv);
			$validator = $result['validator'];

			$onePageCheckout->onePage['info']['payment']['cardDetails'] = array(
				'cardOwner'    => $_POST['cardOwner'],
				'cardNumber'   => $validator->cc_number,
				'cardExpMonth' => $validator->cc_expiry_month,
				'cardExpYear'  => $validator->cc_expiry_year
			);
		
			if (isset($validator->cc_type) && !empty($validator->cc_type)){
				$onePageCheckout->onePage['info']['payment']['cardDetails']['cardType'] = $validator->cc_type;
			}
		
			if ($this->requireCvv === true){
				if (isset($validator->cc_cvv_number)){
					$onePageCheckout->onePage['info']['payment']['cardDetails']['cardCvvNumber'] = $validator->cc_cvv_number;
				}
			}

			if (!empty($result['error'])){
				if (isset($onePageCheckout) && is_object($onePageCheckout)){
					if ($onePageCheckout->isMembershipCheckout() === true){
						$redirectTo = itw_app_link('checkoutType=rental&payment_error=1', 'checkout', 'default', 'SSL');
					}else{
						$redirectTo = itw_app_link('payment_error=1', 'checkout', 'default', 'SSL');
					}
				}else{
					$redirectTo = itw_app_link('payment_error=1', 'checkout', 'default', 'SSL');
				}
				return array(
					'redirectUrl' => $redirectTo,
					'errorMsg'    => $result['error']
				);
			}
			return true;
		}

		public function getCreatorRow($Editor, &$headerPaymentCols){

			$headerPaymentCols[] = '<td class="ui-widget-content ui-state-hover" align="left" style="border-top:none;border-left:none;">'.'&nbsp;'.'</td>';
			$headerPaymentCols[] = '<td class="ui-widget-content ui-state-hover" align="left" style="border-top:none;border-left:none;">'.'&nbsp;'.'</td>';

			$headerPaymentCols[] = '<td class="ui-widget-content ui-state-hover" align="left" style="border-top:none;border-left:none;">'.'<input type="text" class="ui-widget-content" name="payment_amount" size="10">'.'</td>';
			$headerPaymentCols[] = '<td class="ui-widget-content ui-state-hover" align="left" style="border-top:none;border-left:none;">'.'<input type="text" class="ui-widget-content" name="payment_cc_number" size="18" maxlength="16">'.'</td>';
			$headerPaymentCols[] = '<td class="ui-widget-content ui-state-hover" align="left" style="border-top:none;border-left:none;">'.'<input type="text" class="ui-widget-content" name="payment_cc_expires" size="6" maxlength="4">'.'</td>';
			$headerPaymentCols[] = '<td class="ui-widget-content ui-state-hover" align="left" style="border-top:none;border-left:none;">'.'<input type="text" class="ui-widget-content" name="payment_cc_cvv" size="4" maxlength="4">'.'</td>';

			$headerPaymentCols[] = '<td class="ui-widget-content ui-state-hover" align="left" style="border-top:none;border-left:none;">'.($Editor->getOrderId() ? htmlBase::newElement('button')->addClass('paymentProcessButton')->setText('Process')->draw() : 'Will process on save').'</td>';



		}
		
		public function getMonthDropMenuArr(){
			$expires_month = array();
			for ($i = 1; $i < 13; $i++){
				$expires_month[] = array(
					'id'   => sprintf('%02d', $i),
					'text' => strftime('%B', mktime(0, 0, 0, $i, 1, 2000))
				);
			}
			return $expires_month;
		}
	
		public function getYearDropMenuArr(){
			$expires_year = array();
			$today = getdate();
			for ($i = $today['year']; $i < $today['year'] + 10; $i++){
				$expires_year[] = array(
					'id'   => strftime('%y', mktime(0, 0, 0, 1, 1, $i)),
					'text' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i))
				);
			}
			return $expires_year;
		}
	
		public function getCreditCardOwnerField(){
			global $userAccount;
			$input = htmlBase::newElement('input')->setName('cardOwner')->setId('cardOwner');
			
			$userAccount = OrderPaymentModules::getUserAccount();
			$paymentInfo = OrderPaymentModules::getPaymentInfo();
		
			$addressBook =& $userAccount->plugins['addressBook'];
	
			if (isset($paymentInfo['cardDetails']['cardOwner'])){
				$input->val($paymentInfo['cardDetails']['cardOwner']);
			}else{
				$billingAddress = $addressBook->getAddress('billing');
				if ($billingAddress !== false){
					$input->val($billingAddress['entry_firstname'] . ' ' . $billingAddress['entry_lastname']);
				}
			}
			return $input->draw();
		}
	
		public function getCreditCardNumber(){
			$input = htmlBase::newElement('input')->setName('cardNumber')->setId('cardNumber');
		
			$paymentInfo = OrderPaymentModules::getPaymentInfo();
			if (isset($paymentInfo['cardDetails']['cardNumber'])){
				$input->val($paymentInfo['cardDetails']['cardNumber']);
			}
			return $input->draw();
		}
	
		public function getCreditCardExpMonthField(){
			$input = htmlBase::newElement('selectbox')->setName('cardExpMonth')->setId('cardExpMonth');
		
			foreach(self::getMonthDropMenuArr() as $mInfo){
				$input->addOption($mInfo['id'], $mInfo['text']);
			}
		
			$paymentInfo = OrderPaymentModules::getPaymentInfo();
			if (isset($paymentInfo['cardDetails']['cardExpMonth'])){
				$input->selectOptionByValue($paymentInfo['cardDetails']['cardExpMonth']);
			}
			return $input->draw();
		}
	
		public function getCreditCardExpYearField(){
			$input = htmlBase::newElement('selectbox')->setName('cardExpYear')->setId('cardExpYear');
		
			foreach(self::getYearDropMenuArr() as $yInfo){
				$input->addOption($yInfo['id'], $yInfo['text']);
			}
		
			$paymentInfo = OrderPaymentModules::getPaymentInfo();
			if (isset($paymentInfo['cardDetails']['cardExpYear'])){
				$input->selectOptionByValue($paymentInfo['cardDetails']['cardExpYear']);
			}
			return $input->draw();
		}
	
		public function getCreditCardCvvField(){
			$input = htmlBase::newElement('input')->setName('cardCvvNumber')->setId('cardCvvNumber');
		
			$paymentInfo = OrderPaymentModules::getPaymentInfo();
			if (isset($paymentInfo['cardDetails']['cardCvvNumber'])){
				$input->val($paymentInfo['cardDetails']['cardCvvNumber']);
			}
			return $input->attr('size', 5)->attr('maxlength', 4)->draw();
		}

		public function getCreditCardTypeField(){
			$input = htmlBase::newElement('selectbox')->setName('cardType')->setId('cardType');
			foreach($this->allowedTypes as $k => $v){
				$input->addOption($k, $v);
			}
		
			$paymentInfo = OrderPaymentModules::getPaymentInfo();
			if (isset($paymentInfo['cardDetails']['cardType'])){
				$input->selectOptionByValue($paymentInfo['cardDetails']['cardType']);
			}
			return $input->draw();
		}

		public function validateCreditCard($arr, $useCvv = false){
			if(!class_exists('cc_validation')){
				include(sysConfig::getDirFsCatalog() . 'includes/classes/cc_validation.php');
			}
			$validator = new cc_validation();
			if ($useCvv === true){
				$result = $validator->validate(
					$arr['cardNumber'],
					$arr['cardExpMonth'],
					$arr['cardExpYear'],
					$arr['cardCvvNumber'],
					(isset($arr['cardType']) ? $arr['cardType'] : '')
				);
			}else{
				$result = $validator->validate_normal(
					$arr['cardNumber'],
					$arr['cardExpMonth'],
					$arr['cardExpYear']
				);
			}
		
			$error = '';
			if ($result !== true){
				switch ($result) {
					case -1:
						$error = sprintf(sysLanguage::get('TEXT_CCVAL_ERROR_UNKNOWN_CARD'), substr($validator->cc_number, 0, 4));
						break;
					case -2:
					case -3:
					case -4:
						$error = sysLanguage::get('TEXT_CCVAL_ERROR_INVALID_DATE');
						break;
					case -5:
						$error = sysLanguage::get('TEXT_CCVAL_ERROR_CARD_TYPE_MISMATCH');
						break;
					case -6:
						$error = sysLanguage::get('TEXT_CCVAL_ERROR_CVV_LENGTH');
						break;
					case false:
						$error = sysLanguage::get('TEXT_CCVAL_ERROR_INVALID_NUMBER');
						break;
				}
			}
		
			return array(
				'error'     => $error,
				'validator' => $validator
			);
		}
	}
?>