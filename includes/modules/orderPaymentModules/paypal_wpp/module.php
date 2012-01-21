<?php
/*
	Rental Store Version 2

	I.T. Web Experts
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	class OrderPaymentPaypal_wpp extends CreditCardModule {

		public function __construct(){
			/*
			 * Default title and description for modules that are not yet installed
			 */
			$this->setTitle('Credit Card Via Paypal WPP');
			$this->setDescription('Credit Card Via Paypal WPP');

			$this->init('paypal_wpp');

			if ($this->isEnabled() === true){
				$this->isCron = false;
				$this->removeOrderOnFail = false;
				$this->requireCvv = true;
				$this->testMode = ($this->getConfigData('MODULE_PAYMENT_PAYPALWPP_GATEWAY_SERVER') == 'Test');
				$this->currencyValue = $this->getConfigData('MODULE_PAYMENT_PAYPALWPP_CURRENCY');
				$this->allowedTypes = array();

				// Credit card pulldown list
				$cc_array = explode(',', $this->getConfigData('MODULE_PAYMENT_PAYPALWPP_ACCEPTED_CC'));
				foreach($cc_array as $k => $v){
					$this->allowedTypes[trim($v)] = $this->cardTypes[trim($v)];
				}

				if ($this->testMode === true){
					$subDomain = 'sandbox.';
				}else{
					$subDomain = '';
				}
				$this->gatewayUrl = 'https://api-3t.' . $subDomain . 'paypal.com/nvp';

				/*
				 * Use Authorize.net's param dump to show what they are recieving from the server
				 */
				//$this->gatewayUrl = 'https://developer.authorize.net/param_dump.asp';
			}
		}

		public function onSelect(){
			$fieldsArray = array();

			$fieldsArray[] = array(
				'title' => sysLanguage::get('MODULE_PAYMENT_PAYPALWPP_TEXT_CREDIT_CARD_TYPE'),
				'field' => $this->getCreditCardTypeField()
			);

			$fieldsArray[] = array(
				'title' => sysLanguage::get('MODULE_PAYMENT_PAYPALWPP_TEXT_CREDIT_CARD_OWNER'),
				'field' => $this->getCreditCardOwnerField()
			);

			$fieldsArray[] = array(
				'title' => sysLanguage::get('MODULE_PAYMENT_PAYPALWPP_TEXT_CREDIT_CARD_NUMBER'),
				'field' => $this->getCreditCardNumber()
			);

			$fieldsArray[] = array(
				'title' => sysLanguage::get('MODULE_PAYMENT_PAYPALWPP_TEXT_CREDIT_CARD_EXPIRES'),
				'field' => $this->getCreditCardExpMonthField() . '&nbsp;' . $this->getCreditCardExpYearField()
			);

			if ($this->requireCvv === true){
				$fieldsArray[] = array(
					'title' => 'CVV number ' . ' ' .'<a href="#" onclick="popupWindow(\'' . itw_app_link('rType=ajax&appExt=infoPages&dialog=true', 'show_page', 'cvv_help') . '\', 400, 300);return false">' . '<u><i>' . '(' . sysLanguage::get('MODULE_PAYMENT_PAYPALWPP_TEXT_CVV_LINK') . ')' . '</i></u></a>',
					'field' => $this->getCreditCardCvvField()
				);
			}

			$return = parent::onSelect();
			$return['module'] .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $this->getCardImages();
			$return['fields'] = $fieldsArray;

			return $return;
		}

		public function refundPayment($requestData){
			$dataArray = array(
				'USER'            => $this->getConfigData('MODULE_PAYMENT_PAYPALWPP_API_USERNAME'),
				'PWD'             => $this->getConfigData('MODULE_PAYMENT_PAYPALWPP_API_PASSWORD'),
				'SIGNATURE'       => $this->getConfigData('MODULE_PAYMENT_PAYPALWPP_API_SIGNATURE'),
				'VERSION'         => '64.0',
				'METHOD'          => 'RefundTransaction',
				'PAYMENTACTION'   => $this->getConfigData('MODULE_PAYMENT_PAYPALWPP_TRANSACTION_TYPE')
			);
			$dataArray['TRANSACTIONID'] = $requestData['transactionID'];
			$dataArray['REFUNDTYPE'] = 'Full';
			$dataArray['CURRENCYCODE'] = $this->currencyValue;
			$CurlRequest = new CurlRequest($this->gatewayUrl);
			$CurlRequest->setData($dataArray);
			$CurlResponse = $CurlRequest->execute();
			$response = $CurlResponse->getResponse();

			$httpResponseAr = explode("&", $response);
			$httpParsedResponseAr = array();
			foreach ($httpResponseAr as $i => $value) {
				$tmpAr = explode("=", $value);
				if(sizeof($tmpAr) > 1) {
					$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
				}
			}
			$code = '';
			if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
				$code = 0;
			}
			if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
				$code = 1;
			}
			if($code == 0){
				$this->setErrorMessage('There was an error with your refund transaction');
				return false;
			}else{
				$this->logPayment(array(
					'orderID' => $requestData['orderID'],
					'amount'  => $requestData['amount'],
					'message' => 'Refunded',
					'success' => 1,
					'cardDetails' => array(
						'cardOwner'    => $requestData['cardDetails']['cardOwner'],
						'cardNumber'   => $requestData['cardDetails']['cardNumber'],
						'cardExpMonth' => $requestData['cardDetails']['cardExpMonth'],
						'cardExpYear'  => $requestData['cardDetails']['cardExpYear']
					)
				));
				return true;
			}
		}

		public function processPayment($orderID = null, $amount = null){
			global $order, $onePageCheckout;

			$this->removeOrderOnFail = false;

			$paymentAmount = $order->info['total'];
			$userAccount = OrderPaymentModules::getUserAccount();
			$paymentInfo = OrderPaymentModules::getPaymentInfo();

			$addressBook =& $userAccount->plugins['addressBook'];
			$billingAddress = $addressBook->getAddress('billing');
			$countryInfo = $userAccount->plugins['addressBook']->getCountryInfo($billingAddress['entry_country_id']);

			$xExpDate = $paymentInfo['cardDetails']['cardExpMonth'] . $paymentInfo['cardDetails']['cardExpYear'];
			$state_abbr = tep_get_zone_code($billingAddress['entry_country_id'], $billingAddress['entry_zone_id'], $billingAddress['entry_state']);
			$cardOwner = explode(' ', $paymentInfo['cardDetails']['cardOwner']);

			return $this->sendPaymentRequest(array(
				'amount' => $paymentAmount,
				'currencyCode' => $this->currencyValue,//$order->info['currency'],//here will need a check for currencies accpeted by paypal
				'orderID' => $order->newOrder['orderID'],
				'description' => 'description',
				'cardNum' => $paymentInfo['cardDetails']['cardNumber'],
				'cardType' => $paymentInfo['cardDetails']['cardType'],
				'cardExpDate' => $xExpDate,
				'customerId' => $userAccount->getCustomerId(),
				'customerEmail' => $userAccount->getEmailAddress(),
				'customerIp' => $_SERVER['REMOTE_ADDR'],
				'customerFirstName' => (isset($cardOwner[0])?$cardOwner[0]:''),//$billingAddress['entry_firstname'],
				'customerLastName' => (isset($cardOwner[1])?$cardOwner[1]:''),//$billingAddress['entry_lastname'],
				'customerCompany' => $billingAddress['entry_company'],
				'customerStreetAddress' => $billingAddress['entry_street_address'],
				'customerPostcode' => $billingAddress['entry_postcode'],
				'customerCity' => $billingAddress['entry_city'],
				'customerState' => $billingAddress['entry_state'],
				'customerStateCode' => $state_abbr,
				'customerTelephone' => $userAccount->getTelephoneNumber(),
				'customerFax' => $userAccount->getFaxNumber(),
				'customerCountry' => $countryInfo['countries_name'],
				'customerCountryCode' => $countryInfo['countries_iso_code_2'],
				'cardCvv' => $paymentInfo['cardDetails']['cardCvvNumber']
			));
		}
		
		public function processPaymentCron($orderID){
			$this->removeOrderOnFail = false;

			$Qorder = Doctrine_Query::create()
			->from('Orders o')
			->leftJoin('o.Customers c')
			->leftJoin('o.OrdersAddresses oa')
			->leftJoin('o.OrdersTotal ot')
			->leftJoin('c.CustomersMembership m')
			->where('o.orders_id = ?', $orderID)
			->andWhere('oa.address_type = ?', 'billing')
			->andWhereIn('ot.module_type', array('total', 'ot_total'))
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);


			$xExpDate = cc_decrypt($Qorder[0]['Customers']['CustomersMembership']['exp_date']);
			include(sysConfig::getDirFsCatalog() . 'includes/classes/cc_validation.php');
			$validator = new cc_validation();
			//get state abbreviation from orders addresses data
			$state_abbr = 'CA';
			return $this->sendPaymentRequest(array(
				'amount' => $Qorder[0]['OrdersTotal'][0]['value'],
				'currencyCode' => $this->currencyValue,
				'orderID' => $orderID,
				'description' => sysConfig::get('STORE_NAME') . ' Subscription Payment',
				'cardNum' => cc_decrypt($Qorder[0]['Customers']['CustomersMembership']['card_num']),
				'cardType' => $validator->getCardType($Qorder[0]['Customers']['CustomersMembership']['card_num']),
				'cardExpDate' => $xExpDate,
				'customerId' => $Qorder[0]['customers_id'],
				'customerEmail' => $Qorder[0]['customers_email_address'],
				'customerFirstName' => $Qorder[0]['Customers']['customers_firstname'],
				'customerLastName' => $Qorder[0]['Customers']['customers_lastname'],
				'customerCompany' => $Qorder[0]['OrdersAddresses'][0]['entry_company'],
				'customerStreetAddress' => $Qorder[0]['OrdersAddresses'][0]['entry_street_address'],
				'customerPostcode' => $Qorder[0]['OrdersAddresses'][0]['entry_postcode'],
				'customerCity' => $Qorder[0]['OrdersAddresses'][0]['entry_city'],
				'customerState' => $Qorder[0]['OrdersAddresses'][0]['entry_state'],
				'customerStateCode' => $state_abbr,
				'customerTelephone' => $Qorder[0]['customers_telephone'],
				'customerCountry' => $Qorder[0]['OrdersAddresses'][0]['entry_country'],
				'cardCvv' => cc_decrypt($Qorder[0]['Customers']['CustomersMembership']['card_cvv'])
			));
		}

		public function sendPaymentRequest($requestParams){


			$dataArray = array(
				'USER'            => $this->getConfigData('MODULE_PAYMENT_PAYPALWPP_API_USERNAME'),
				'PWD'             => $this->getConfigData('MODULE_PAYMENT_PAYPALWPP_API_PASSWORD'),
				'SIGNATURE'       => $this->getConfigData('MODULE_PAYMENT_PAYPALWPP_API_SIGNATURE'),
				'VERSION'         => '64.0',
				'METHOD'          => 'DoDirectPayment',
				'PAYMENTACTION'   => $this->getConfigData('MODULE_PAYMENT_PAYPALWPP_TRANSACTION_TYPE')
			);
			
			if (isset($requestParams['orderID'])) $dataArray['INVNUM'] = $requestParams['orderID'];
			if (isset($requestParams['description'])) $dataArray['DESC'] = $requestParams['description'];
			if (isset($requestParams['amount'])) $dataArray['AMT'] = $requestParams['amount'];
			if (isset($requestParams['currencyCode'])) $dataArray['CURRENCYCODE'] = $requestParams['currencyCode'];
			if (isset($requestParams['customerId'])) $dataArray['CUSTOM'] = $requestParams['customerId'];
			if (isset($requestParams['customerIp'])) $dataArray['IPADDRESS'] = $requestParams['customerIp'];
			if (isset($requestParams['customerFirstName'])) $dataArray['FIRSTNAME'] = $requestParams['customerFirstName'];
			if (isset($requestParams['customerLastName'])) $dataArray['LASTNAME'] = $requestParams['customerLastName'];
			if (isset($requestParams['customerStreetAddress'])) $dataArray['STREET'] = $requestParams['customerStreetAddress'];
			if (isset($requestParams['customerPostcode'])) $dataArray['ZIP'] = $requestParams['customerPostcode'];
			if (isset($requestParams['customerCity'])) $dataArray['CITY'] = $requestParams['customerCity'];
			if (isset($requestParams['customerStateCode'])) $dataArray['STATE'] = $requestParams['customerStateCode'];
			if (isset($requestParams['customerCountryCode'])) $dataArray['COUNTRYCODE'] = $requestParams['customerCountryCode'];
			if (isset($requestParams['cardNum'])) $dataArray['ACCT'] = $requestParams['cardNum'];
			if (isset($requestParams['cardType'])) $dataArray['CREDITCARDTYPE'] = $requestParams['cardType'];
			if (isset($requestParams['cardExpDate'])) $dataArray['EXPDATE'] = $requestParams['cardExpDate'];
			if (isset($requestParams['cardCvv'])) $dataArray['CVV2'] = $requestParams['cardCvv'];

			$CurlRequest = new CurlRequest($this->gatewayUrl);
			$CurlRequest->setData($dataArray);
			//echo $CurlRequest->getDataFormatted().'lll';
			$CurlResponse = $CurlRequest->execute();

			return $this->onResponse($CurlResponse);
		}

		private function onResponse($CurlResponse, $isCron = false){
			$response = $CurlResponse->getResponse();

			$httpResponseAr = explode("&", $response);
			$httpParsedResponseAr = array();
			foreach ($httpResponseAr as $i => $value) {
				$tmpAr = explode("=", $value);
				if(sizeof($tmpAr) > 1) {
					$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
				}
			}
			$code = '';
			if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
				$code = '';
			}
			if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
				$code = '1';
			}


			$success = true;
			$errMsg = '';
			if ($code != '1'){
				$success = false;
				switch($code){
					case '':
						$errMsg = 'The server cannot connect to ' . $this->getTitle() . '.  Please check your cURL and server settings.';
						break;

					default:
						$errMsg = 'There was an unspecified error processing your credit card: '. implode(';',$httpParsedResponseAr);
						break;
				}
			}

			if ($isCron === true){
				$this->cronMsg = $errMsg;
			}

			if ($success === true){
				$this->onSuccess(array(
					'curlResponse' => $CurlResponse,
					'message'      => $errMsg
				));
			}else{
				$this->onFail(array(
					'curlResponse' => $CurlResponse,
					'message'      => $errMsg
				));
			}
			return $success;
		}

		private function onSuccess($info){
			global $order;
			$ResponseData = explode('&',$info['curlResponse']->getResponse());
			$httpParsedResponseAr = array();
			foreach ($ResponseData as $i => $value) {
				$tmpAr = explode("=", $value);
				if(sizeof($tmpAr) > 1) {
					$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
				}
			}
			$userAccount = OrderPaymentModules::getUserAccount();
			$paymentInfo = OrderPaymentModules::getPaymentInfo();
			$addressBook =& $userAccount->plugins['addressBook'];
			$billingAddress = $addressBook->getAddress('billing');

			$this->logPayment(array(
				'orderID' => $order->newOrder['orderID'],
				'amount'  => $order->info['total'],
				'message' => $httpParsedResponseAr['TRANSACTIONID'],
				'success' => 1,
				'cardDetails' => array(
					'cardOwner'    => $billingAddress['entry_firstname']. ' '.$billingAddress['entry_lastname'],
					'cardNumber'   => $paymentInfo['cardDetails']['cardNumber'],
					'cardExpMonth' => $paymentInfo['cardDetails']['cardExpMonth'],
					'cardExpYear'  => $paymentInfo['cardDetails']['cardExpYear']
				)
			));
		}

		private function onFail($info){
			global $messageStack,$order;
			$orderId = $order->newOrder['orderID'];
			$this->setErrorMessage($this->getTitle() . ' : ' . $info['message']);
			$messageStack->addSession('pageStack', $info['message'], 'error');
			if ($this->removeOrderOnFail === true){
				$Order = Doctrine_Core::getTable('Orders')->find($orderId);
				if ($Order){
					$Order->delete();//this need revised. For failed transaction Add a button Pay Now in the orders history
				}
				//tep_redirect(itw_app_link('payment_error=1', 'checkout', 'default', 'SSL'));
			}else{
				$userAccount = OrderPaymentModules::getUserAccount();
				$paymentInfo = OrderPaymentModules::getPaymentInfo();
				$addressBook =& $userAccount->plugins['addressBook'];
				$billingAddress = $addressBook->getAddress('billing');

				$this->logPayment(array(
					'orderID' => $order->newOrder['orderID'],
					'amount'  => $order->info['total'],
					'message' => '',
					'success' =>01,
					'cardDetails' => array(
						'cardOwner'    => $billingAddress['entry_firstname']. ' '.$billingAddress['entry_lastname'],
						'cardNumber'   => $paymentInfo['cardDetails']['cardNumber'],
						'cardExpMonth' => $paymentInfo['cardDetails']['cardExpMonth'],
						'cardExpYear'  => $paymentInfo['cardDetails']['cardExpYear']
					)
				));
			}
		}
	}
?>