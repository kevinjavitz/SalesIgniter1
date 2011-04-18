<?php
/*
	Rental Store Version 2

	I.T. Web Experts
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	class OrderPaymentAuthorizenet extends CreditCardModule {

		public function __construct(){
			/*
			 * Default title and description for modules that are not yet installed
			 */
			$this->setTitle('Credit Card Via Authorize.net');
			$this->setDescription('Credit Card Via Authorize.net');

			$this->init('authorizenet');

			if ($this->isEnabled() === true){
				$this->isCron = false;
				$this->removeOrderOnFail = false;
				$this->requireCvv = true;
				$this->testMode = ($this->getConfigData('MODULE_PAYMENT_AUTHORIZENET_TESTMODE') == 'Test' || $this->getConfigData('MODULE_PAYMENT_AUTHORIZENET_TESTMODE') == 'Test And Debug');
				$this->curlCompiled = ($this->getConfigData('MODULE_PAYMENT_AUTHORIZENET_CURL') != 'Not Compiled');

				$this->allowedTypes = array();

				// Credit card pulldown list
				$cc_array = explode(',', $this->getConfigData('MODULE_PAYMENT_AUTHORIZENET_ACCEPTED_CC'));
				foreach($cc_array as $k => $v){
					$this->allowedTypes[trim($v)] = $this->cardTypes[trim($v)];
				}

				if ($this->testMode === true){
					$subDomain = 'test';
				}else{
					$subDomain = 'secure';
				}
				$this->gatewayUrl = 'https://' . $subDomain . '.authorize.net/gateway/transact.dll';

				/*
				 * Use Authorize.net's param dump to show what they are recieving from the server
				 */
				//$this->gatewayUrl = 'https://developer.authorize.net/param_dump.asp';
			}
		}

		public function onSelect(){
			$fieldsArray = array();

			$fieldsArray[] = array(
				'title' => sysLanguage::get('MODULE_PAYMENT_AUTHORIZENET_TEXT_CREDIT_CARD_TYPE'),
				'field' => $this->getCreditCardTypeField()
			);

			$fieldsArray[] = array(
				'title' => sysLanguage::get('MODULE_PAYMENT_AUTHORIZENET_TEXT_CREDIT_CARD_OWNER'),
				'field' => $this->getCreditCardOwnerField()
			);

			$fieldsArray[] = array(
				'title' => sysLanguage::get('MODULE_PAYMENT_AUTHORIZENET_TEXT_CREDIT_CARD_NUMBER'),
				'field' => $this->getCreditCardNumber()
			);

			$fieldsArray[] = array(
				'title' => sysLanguage::get('MODULE_PAYMENT_AUTHORIZENET_TEXT_CREDIT_CARD_EXPIRES'),
				'field' => $this->getCreditCardExpMonthField() . '&nbsp;' . $this->getCreditCardExpYearField()
			);

			if ($this->requireCvv === true){
				$fieldsArray[] = array(
					'title' => 'CVV number ' . ' ' .'<a href="#" onclick="popupWindow(\'' . itw_app_link('rType=ajax&appExt=infoPages&dialog=true', 'show_page', 'cvv_help') . '\', 400, 300);return false">' . '<u><i>' . '(' . sysLanguage::get('MODULE_PAYMENT_AUTHORIZENET_TEXT_CVV_LINK') . ')' . '</i></u></a>',
					'field' => $this->getCreditCardCvvField()
				);
			}

			$return = parent::onSelect();
			$return['module'] .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $this->getCardImages();
			$return['fields'] = $fieldsArray;

			return $return;
		}

		public function processPayment(){
			global $order, $onePageCheckout;

			$this->removeOrderOnFail = true;

			$paymentAmount = $order->info['total'];
			if ($this->getConfigData('MODULE_PAYMENT_AUTHORIZENET_CCMODE') == 'AUTH_ONLY'){
				$paymentAmount -= $order->info['subtotal'];
				$prods = $order->products;
				for($i=0; $i<sizeof($prods); $i++){
					if ($prods[$i]['auth_method'] == 'auth'){
						if ($prods[$i]['auth_charge'] > 0){
							$paymentAmount += $prods[$i]['auth_charge'] * $prods[$i]['quantity'];
						}else{
							$paymentAmount += $prods[$i]['final_price'] * $prods[$i]['quantity'];
						}
					}else{
						$paymentAmount += $prods[$i]['final_price'] * $prods[$i]['quantity'];
					}
				}
			}

			$userAccount = OrderPaymentModules::getUserAccount();
			$paymentInfo = OrderPaymentModules::getPaymentInfo();

			$addressBook =& $userAccount->plugins['addressBook'];
			$billingAddress = $addressBook->getAddress('billing');
			$countryInfo = $userAccount->plugins['addressBook']->getCountryInfo($billingAddress['entry_country_id']);

			$xExpDate = $paymentInfo['cardDetails']['cardExpMonth'] . $paymentInfo['cardDetails']['cardExpYear'];

			return $this->sendPaymentRequest(array(
				'amount' => $paymentAmount,
				'currencyCode' => $order->info['currency'],
				'orderID' => $order->newOrder['orderID'],
				'description' => 'description',
				'cardNum' => $paymentInfo['cardDetails']['cardNumber'],
				'cardExpDate' => $xExpDate,
				'customerId' => $userAccount->getCustomerId(),
				'customerEmail' => $userAccount->getEmailAddress(),
				'customerIp' => $_SERVER['REMOTE_ADDR'],
				'customerFirstName' => $billingAddress['entry_firstname'],
				'customerLastName' => $billingAddress['entry_lastname'],
				'customerCompany' => $billingAddress['entry_company'],
				'customerStreetAddress' => $billingAddress['entry_street_address'],
				'customerPostcode' => $billingAddress['entry_postcode'],
				'customerCity' => $billingAddress['entry_city'],
				'customerState' => $billingAddress['entry_state'],
				'customerTelephone' => $userAccount->getTelephoneNumber(),
				'customerFax' => $userAccount->getFaxNumber(),
				'customerCountry' => $countryInfo['countries_name'],
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

			$xType = $this->getConfigData('MODULE_PAYMENT_AUTHORIZENET_CCMODE') == 'Authorize Only' ? 'AUTH_ONLY' : 'AUTH_CAPTURE';
			$xMethod = $this->getConfigData('MODULE_PAYMENT_AUTHORIZENET_METHOD') == 'Credit Card' ? 'CC' : 'ECHECK';
			$xEmailCustomer = $this->getConfigData('MODULE_PAYMENT_AUTHORIZENET_EMAIL_CUSTOMER') == 'True' ? 'TRUE': 'FALSE';
			$xEmailMerchant = $this->getConfigData('MODULE_PAYMENT_AUTHORIZENET_EMAIL_MERCHANT') == 'True' ? 'TRUE': 'FALSE';
			$xExpDate = cc_decrypt($Qorder[0]['Customers']['CustomersMembership']['exp_date']);

			return $this->sendPaymentRequest(array(
				'amount' => $Qorder[0]['OrdersTotal'][0]['value'],
				'currencyCode' => $Qorder[0]['currency'],
				'orderID' => $orderID,
				'description' => sysConfig::get('STORE_NAME') . ' Subscription Payment',
				'cardNum' => cc_decrypt($Qorder[0]['Customers']['CustomersMembership']['card_num']),
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
				'customerTelephone' => $Qorder[0]['customers_telephone'],
				'customerCountry' => $Qorder[0]['OrdersAddresses'][0]['entry_country'],
				'cardCvv' => cc_decrypt($Qorder[0]['Customers']['CustomersMembership']['card_cvv'])
			));
		}

		public function sendPaymentRequest($requestParams){
			$xType = $this->getConfigData('MODULE_PAYMENT_AUTHORIZENET_CCMODE') == 'Authorize Only' ? 'AUTH_ONLY' : 'AUTH_CAPTURE';
			$xMethod = $this->getConfigData('MODULE_PAYMENT_AUTHORIZENET_METHOD') == 'Credit Card' ? 'CC' : 'ECHECK';
			$xEmailCustomer = $this->getConfigData('MODULE_PAYMENT_AUTHORIZENET_EMAIL_CUSTOMER') == 'True' ? 'TRUE': 'FALSE';
			$xEmailMerchant = $this->getConfigData('MODULE_PAYMENT_AUTHORIZENET_EMAIL_MERCHANT') == 'True' ? 'TRUE': 'FALSE';

			$dataArray = array(
				'x_login'          => $this->getConfigData('MODULE_PAYMENT_AUTHORIZENET_LOGIN'),
				'x_tran_key'       => $this->getConfigData('MODULE_PAYMENT_AUTHORIZENET_TRANSKEY'),
				'x_relay_response' => 'FALSE',
				'x_delim_data'     => 'TRUE',
				'x_version'        => '3.1',
				'x_type'           => $xType,
				'x_method'         => $xMethod,
				'x_email_customer' => $xEmailCustomer,
				'x_email_merchant' => 'FALSE'
			);

			$dataArray[Session::getSessionName()] = Session::getSessionId();

			if ($this->testMode === true){
				$dataArray['x_test_request'] = 'TRUE';
			}
			
			if (isset($requestParams['orderID'])) $dataArray['x_invoice_num'] = $requestParams['orderID'];
			if (isset($requestParams['description'])) $dataArray['x_description'] = $requestParams['description'];
			if (isset($requestParams['amount'])) $dataArray['x_amount'] = $requestParams['amount'];
			if (isset($requestParams['currencyCode'])) $dataArray['x_currency_code'] = $requestParams['currencyCode'];
			if (isset($requestParams['customerId'])) $dataArray['x_cust_id'] = $requestParams['customerId'];
			if (isset($requestParams['customerEmail'])) $dataArray['x_email'] = $requestParams['customerEmail'];
			if (isset($requestParams['customerIp'])) $dataArray['x_customer_ip'] = $requestParams['customerIp'];
			if (isset($requestParams['customerFirstName'])) $dataArray['x_first_name'] = $requestParams['customerFirstName'];
			if (isset($requestParams['customerLastName'])) $dataArray['x_last_name'] = $requestParams['customerLastName'];
			if (isset($requestParams['customerCompany'])) $dataArray['x_company'] = $requestParams['customerCompany'];
			if (isset($requestParams['customerStreetAddress'])) $dataArray['x_address'] = $requestParams['customerStreetAddress'];
			if (isset($requestParams['customerPostcode'])) $dataArray['x_zip'] = $requestParams['customerPostcode'];
			if (isset($requestParams['customerCity'])) $dataArray['x_city'] = $requestParams['customerCity'];
			if (isset($requestParams['customerState'])) $dataArray['x_state'] = $requestParams['customerState'];
			if (isset($requestParams['customerTelephone'])) $dataArray['x_phone'] = $requestParams['customerTelephone'];
			if (isset($requestParams['customerFax'])) $dataArray['x_fax'] = $requestParams['customerFax'];
			if (isset($requestParams['customerCountry'])) $dataArray['x_country'] = $requestParams['customerCountry'];
			if (isset($requestParams['cardNum'])) $dataArray['x_card_num'] = $requestParams['cardNum'];
			if (isset($requestParams['cardExpDate'])) $dataArray['x_exp_date'] = $requestParams['cardExpDate'];
			if (isset($requestParams['cardCvv'])) $dataArray['x_card_code'] = $requestParams['cardCvv'];

			$CurlRequest = new CurlRequest($this->gatewayUrl);
			$CurlRequest->setData($dataArray);
			$CurlResponse = $CurlRequest->execute();

			return $this->onResponse($CurlResponse);
		}

		private function onResponse($CurlResponse, $isCron = false){
			$response = $CurlResponse->getResponse();

			$response = explode(',', $response);

			$code = $response[0];
			$subCode = $response[1];
			$reasonCode = $response[2];
			$reasonText = $response[3];

			$success = true;
			$errMsg = $reasonText;
			if ($code != 1){
				$success = false;
				switch($code){
					case '':
						$errMsg = 'The server cannot connect to ' . $this->getTitle() . '.  Please check your cURL and server settings.';
						break;
					case '2':
						$errMsg = 'Your credit card was declined ( ' . $code .'-' .$reasonCode . ' ):' . $reasonText;
						break;
					case '3':
						$errMsg = 'There was an error processing your credit card ( ' . $code.'-'.$reasonCode . ' ):' . $reasonText;
						break;
					default:
						$errMsg = 'There was an unspecified error processing your credit card ( ' . $code.'-'.$reasonCode . ' ):' . $reasonText;
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
			$RequestData = $info['curlResponse']->getDataRaw();
			$orderId = $RequestData['x_invoice_num'];
			$this->logPayment(array(
				'orderID' => $orderId,
				'amount'  => $RequestData['x_amount'],
				'message' => $info['message'],
				'success' => 1,
				'cardDetails' => array(
					'cardOwner'    => $RequestData['x_first_name'] . ' ' . $RequestData['x_last_name'],
					'cardNumber'   => $RequestData['x_card_num'],
					'cardExpMonth' => substr($RequestData['x_exp_date'], 0, 2),
					'cardExpYear'  => substr($RequestData['x_exp_date'], 2)
				)
			));
		}

		private function onFail($info){
			global $messageStack;
			$RequestData = $info['curlResponse']->getDataRaw();
			$orderId = $RequestData['x_invoice_num'];
			$this->setErrorMessage($this->getTitle() . ' : ' . $info['message']);
			if ($this->removeOrderOnFail === true){
				$Order = Doctrine_Core::getTable('Orders')->find($orderId);
				if ($Order){
					$Order->delete();
				}

				$messageStack->addSession('pageStack', $info['message'], 'error');
				//tep_redirect(itw_app_link('payment_error=1', 'checkout', 'default', 'SSL'));
			}else{
				$this->logPayment(array(
					'orderID' => $orderId,
					'amount'  => $RequestData['x_amount'],
					'message' => $info['message'],
					'success' => 0,
					'CardDetails' => array(
						'cardOwner'    => $RequestData['x_first_name'] . ' ' . $RequestData['x_last_name'],
						'cardNumber'   => $RequestData['x_card_num'],
						'cardExpMonth' => $RequestData['x_exp_date'],
						'cardExpYear'  => $RequestData['x_exp_date']
					)
				));
			}
		}
	}
?>