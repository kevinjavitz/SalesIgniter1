<?php
/*
	Rental Store Version 2

	I.T. Web Experts
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	class OrderPaymentAuthorizenet extends CreditCardModule {
		var $isCron;
		var $removeOrderOnFail;
		var $requireCvv;
		var $testMode;
		var $cim_mode;
		var $curlCompiled;
		var $can_reuse;
		var $startNumbersRejected;
		var $allowedTypes;
		var $gatewayUrl;
		var $login;
		var $transkey;
		var $urlCIM;
		var $params;
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
				$this->cim_mode = ($this->getConfigData('MODULE_PAYMENT_AUTHORIZENET_CIM') == 'True');
				$this->curlCompiled = ($this->getConfigData('MODULE_PAYMENT_AUTHORIZENET_CURL') != 'Not Compiled');
				$this->can_reuse = $this->getReuses();

				$this->startNumbersRejected = explode(',', $this->getConfigData('MODULE_PAYMENT_AUTHORIZENET_REJECTED_CC'));
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
				$this->login    = trim($this->getConfigData('MODULE_PAYMENT_AUTHORIZENET_LOGIN'));//'55tZyWtL9629';
				$this->transkey = trim($this->getConfigData('MODULE_PAYMENT_AUTHORIZENET_TRANSKEY'));//'2h6t5Vh8n8Xd5EVZ';

				$subdomain  = ($this->testMode) ? 'apitest' : 'api';
				$this->urlCIM = 'https://' . $subdomain . '.authorize.net/xml/v1/request.api';

				$this->params['customerType']     = 'individual';
				$this->params['validationMode']   = 'liveMode';
				$this->params['taxExempt']        = 'false';
				$this->params['recurringBilling'] = 'false';
				/*
				 * Use Authorize.net's param dump to show what they are recieving from the server
				 */
				//$this->gatewayUrl = 'https://developer.authorize.net/param_dump.asp';
			}
		}

		public function getReuses(){

			$userAccount = OrderPaymentModules::getUserAccount();
			if(is_object($userAccount)){
				$Qhistory = Doctrine_Query::create()
				->from('Orders o')
				->leftJoin('o.OrdersPaymentsHistory oph')
				->where('o.customers_id=?', $userAccount->getCustomerId())
				->orderBy('payment_history_id DESC')
				->limit(1)
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
				if($Qhistory[0]['OrdersPaymentsHistory'][0]['can_reuse'] == 1){
					return true;
				}
			}
			return false;
		}

		public function getCreatorRow($Editor, &$headerPaymentCols){
			parent::getCreatorRow($Editor, &$headerPaymentCols);

			$paymentCards = array();
			$paymentProfiles = array();
			if($this->cim_mode ===true){
				$this->setParameter('email', $Editor->getEmailAddress());
				$this->setParameter('description', 'description'); // Optional
				$this->setParameter('merchantCustomerId', $Editor->getCustomerId());
				$this->createCustomerProfile();
				$profileID = '';
				if(strpos($this->getResponse(), 'A duplicate record with ID') !== false){
					$profileID = substr($this->getResponse(),strlen('A duplicate record with ID '), strpos($this->getResponse(),' already') - strlen('A duplicate record with ID '));
					$this->profileId = $profileID;
					$this->setParameter('customerProfileId', $profileID);
				}
				if($profileID != ''){
					$this->getCustomerProfile();
					if(isset($this->paymentProfiles)){
						foreach($this->paymentProfiles as $profile){
	                    	$paymentCards[] = (string)$profile->payment->creditCard->cardNumber;
							$paymentProfiles[] = (string)$profile->customerPaymentProfileId;
						}
					}
				}
			}

			if($this->cim_mode === true && count($paymentCards) > 0){
				$htmlSelect = htmlBase::newElement('selectbox')
				->setName('payment_profile');
				$htmlSelect->addOption('-1', sysLanguage::get('TEXT_PLEASE_SELECT_PAYMENT_PROFILE'));
				foreach($paymentCards as $key => $card){
					$htmlSelect->addOption($paymentProfiles[$key], $paymentCards[$key]);
				}
				$headerPaymentCols[count($headerPaymentCols)-4] = '<td class="ui-widget-content ui-state-hover" align="left" style="border-top:none;border-left:none;"><table><tr>'.$headerPaymentCols[count($headerPaymentCols)-4].'<td>'.$htmlSelect->draw().'</td></tr></table></td>';
			}

		}

		private function getCustomerProfileId(){
			$userAccount = OrderPaymentModules::getUserAccount();
			$this->setParameter('email', $userAccount->getEmailAddress());
			$this->setParameter('description', 'description'); // Optional
			$this->setParameter('merchantCustomerId', $userAccount->getCustomerId());
			$this->createCustomerProfile();
			if(strpos($this->getResponse(), 'A duplicate record with ID') !== false){
				$profile_id = substr($this->getResponse(),strlen('A duplicate record with ID '), strpos($this->getResponse(),' already') - strlen('A duplicate record with ID '));
				$this->profileId = $profile_id;
				$this->setParameter('customerProfileId', $profile_id);
				return $profile_id;
			}else{

			}
			return '';
		}
		public function validatePost(){
			/* if(isset($_POST['cardNumber'])){
				foreach($this->startNumbersRejected as $rejected){
					if(strpos($_POST['cardNumber'], $rejected) == 0){
						$redirectTo = itw_app_link('payment_error=1', 'checkout', 'default', 'SSL');

						return array(
							'redirectUrl' => $redirectTo,
							'errorMsg'    => sprintf(sysLanguage::get('ERROR_AUTHORIZENET_CC_REJECTED_START_NUMBER'), $rejected)
						);
					}
				}
			} */
			if(!isset($_POST['payment_profile']) || $_POST['payment_profile'] == -1){
				return parent::validatePost();
			}
			return true;
		}

		public function onSelect(){
			global $onePageCheckout;
			$fieldsArray = array();
			$paymentCards = array();
			$paymentProfiles = array();
			if($this->cim_mode ===true){
				//$this->getCustomerProfileIds();//this is not very efficient because can be hundreds of clients and is better to keep localy the ids
				$profileID = $this->getCustomerProfileId();
				if($profileID != ''){
					$this->getCustomerProfile();
					if(isset($this->paymentProfiles)){
						foreach($this->paymentProfiles as $profile){
	                    	$paymentCards[] = (string)$profile->payment->creditCard->cardNumber;
							$paymentProfiles[] = (string)$profile->customerPaymentProfileId;
						}
					}
				}
			}

			if($this->cim_mode === true && count($paymentCards) > 0 && $this->can_reuse === true){
				$htmlSelect = htmlBase::newElement('selectbox')
				->setName('payment_profile');
				$htmlSelect->addOption('-1', sysLanguage::get('TEXT_PLEASE_SELECT_PAYMENT_PROFILE'));
				foreach($paymentCards as $key => $card){
					$htmlSelect->addOption($paymentProfiles[$key], $paymentCards[$key]);
				}

				$fieldsArray[] = array(
					'title' => sysLanguage::get('MODULE_PAYMENT_AUTHORIZENET_SELECT_PAYMENT_PROFILE'),
					'field' => $htmlSelect->draw()
				);
				$fieldsArray[] = array(
					'title' => sysLanguage::get('MODULE_PAYMENT_AUTHORIZENET_CREATE_NEW_PROFILE'),
					'field' => ''
				);
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
				//get all payment profiles for customer based on the profile id retrivid from cim only if cim is enabled.
			}else{
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

				if($this->cim_mode === true){
					$htmlCheck = htmlBase::newElement('checkbox')
					->setName('canReuse');
					if(isset($onePageCheckout->onePage['info']['payment']['cardDetails']['canReuse'])){
						$htmlCheck->setChecked(true);
					}
					$fieldsArray[] = array(
						'title' => sysLanguage::get('MODULE_PAYMENT_AUTHORIZENET_CAN_REUSE'),
						'field' => $htmlCheck->draw()
					);

				}
			}

			$return = parent::onSelect();
			$return['module'] .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $this->getCardImages();
			$return['fields'] = $fieldsArray;

			return $return;
		}

		public function refundPayment($requestData){
			if($this->cim_mode == false){
				$dataArray = array(
					'x_login'          => $this->login,
					'x_tran_key'       => $this->transkey,
					'x_relay_response' => 'FALSE',
					'x_delim_data'     => 'TRUE',
					'x_version'        => '3.1',
					'x_type'           => 'CREDIT',
					'x_card_num'       => trim($requestData['cardDetails']['cardNumber']),
					'x_amount'         => number_format($requestData['amount'], 2),
					'x_trans_id'       => trim($requestData['cardDetails']['transId'])
				);
				$CurlRequest = new CurlRequest($this->gatewayUrl);
				$CurlRequest->setData($dataArray);
				$CurlResponse = $CurlRequest->execute();

				return $this->onResponse($CurlResponse);
			}else{

				$this->refundTransaction($requestData);
				$message = $this->getResponse();
				if($message == 'Successful.'){
						$info['message'] = 'Refunded Transaction success. Amount';
						return $this->CIMSuccess($info);
				}else{
						$info['message'] = 'Refunded Transaction Fail. Response Code:'.$this->getResponse();
						return $this->CIMFail($info);
				}
			}
		}

		public function processPayment($orderID = null, $amount = null){
			global $order, $onePageCheckout, $ShoppingCart, $Editor;

			if(isset($_POST['canReuse'])){
				$onePageCheckout->onePage['info']['payment']['cardDetails']['canReuse'] = true;
			}

			if($this->getConfigData('MODULE_PAYMENT_AUTHORIZENET_CCMODE') == 'Authorize Only'){
				$xType = 'AUTH_ONLY';
			}elseif($this->getConfigData('MODULE_PAYMENT_AUTHORIZENET_CCMODE') == 'Authorize And Capture'){
				$xType = 'AUTH_CAPTURE';
			}else{
				$xType = 'PRIOR_AUTH_CAPTURE';
			}

			$paymentInfo = OrderPaymentModules::getPaymentInfo();
			$xExpDate = $paymentInfo['cardDetails']['cardExpMonth'] . $paymentInfo['cardDetails']['cardExpYear'];
			$expirationDate = $paymentInfo['cardDetails']['cardExpYear'].'-'. $paymentInfo['cardDetails']['cardExpMonth'];

			/*For each product i calculate deposit amount and make the sum for auth type and then substract for auth and sale*/
			/*if on editor i get all product from editor too*/
			$totalDeposit = 0;
			if(isset($ShoppingCart) && !is_null($ShoppingCart) && is_object($ShoppingCart)){
				foreach($ShoppingCart->getProducts() as $iProduct){
					$resInfo = $iProduct->getInfo();
					if(isset($resInfo['reservationInfo']['deposit_amount'])){
						$totalDeposit += $resInfo['reservationInfo']['deposit_amount'];
					}
					//can be for insurance too
				}
			}elseif(isset($Editor)){
				foreach($Editor->ProductManager->getContents() as $iProduct){
					$resInfo = $iProduct->getPInfo();
					if(isset($resInfo['reservationInfo']['deposit_amount'])){
						$totalDeposit += $resInfo['reservationInfo']['deposit_amount'];
					}
				}
			}

			if(is_null($orderID)){
				$userAccount = OrderPaymentModules::getUserAccount();

				$addressBook =& $userAccount->plugins['addressBook'];
				$billingAddress = $addressBook->getAddress('billing');
				$countryInfo = $userAccount->plugins['addressBook']->getCountryInfo($billingAddress['entry_country_id']);

				$total = $order->info['total'];
				$dataArray = array(
					'currencyCode' => $order->info['currency'],
					'orderID' => $order->newOrder['orderID'],
					'description' => 'description',
					'cardNum' => $paymentInfo['cardDetails']['cardNumber'],
					'cardExpDate' => $xExpDate,
					'cardExpDateCIM' => $expirationDate,
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
				);
			}else{
				$Qorder = Doctrine_Query::create()
					->from('Orders o')
					->leftJoin('o.OrdersPaymentsHistory oph')
					->leftJoin('o.Customers c')
					->leftJoin('o.OrdersAddresses oa')
					->leftJoin('o.OrdersTotal ot')
					->where('o.orders_id = ?', $orderID)
					->andWhere('oa.address_type = ?', 'billing')
					->andWhereIn('ot.module_type', array('total', 'ot_total'))
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

				if(!is_null($amount)){
					$total = $amount;
				}else{
					$total = $Qorder[0]['OrdersTotal'][0]['value'];
				}
				$cardDetails = unserialize(cc_decrypt($Qorder[0]['OrdersPaymentsHistory'][0]['card_details']));
				$xExpDate = $cardDetails['cardExpMonth']. $cardDetails['cardExpYear'];
				$dataArray = array(
					'currencyCode' => $Qorder[0]['currency'],
					'orderID' => $orderID,
					'description' => sysConfig::get('STORE_NAME') . ' Subscription Payment',
					'cardNum' => $cardDetails['cardNumber'],
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
					'cardCvv' => $cardDetails['cardCvvNumber']
				);

			}

			if($totalDeposit > 0){
				$xType = 'AUTH_ONLY';
				$dataArray['amount'] = $totalDeposit;
				$dataArray['xType'] = $xType;


				$f = $this->sendPaymentRequest($dataArray);
				if($f){
					$restAmount = $total - $totalDeposit;

					$xType = 'AUTH_CAPTURE';
					$dataArray['amount'] = $restAmount;
					$dataArray['xType'] = $xType;

					$f = $this->sendPaymentRequest($dataArray);
				}
				return $f;
			}else{
				$dataArray['amount'] =  $total;
				$dataArray['xType'] = $xType;
				return $this->sendPaymentRequest($dataArray);
			}

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

			if($this->getConfigData('MODULE_PAYMENT_AUTHORIZENET_CCMODE') == 'Authorize Only'){
				$xType = 'AUTH_ONLY';
			}elseif($this->getConfigData('MODULE_PAYMENT_AUTHORIZENET_CCMODE') == 'Authorize And Capture'){
				$xType = 'AUTH_CAPTURE';
			}else{
				$xType = 'PRIOR_AUTH_CAPTURE';
			}

			$dataArray = array(
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
				'cardCvv' => cc_decrypt($Qorder[0]['Customers']['CustomersMembership']['card_cvv']),
				'xType' => $xType
			);

			return $this->sendPaymentRequest($dataArray, true);
		}

		public function sendPaymentRequest($requestParams, $isCron = false){
			global $messageStack;

			$xMethod = $this->getConfigData('MODULE_PAYMENT_AUTHORIZENET_METHOD') == 'Credit Card' ? 'CC' : 'ECHECK';
			$xEmailCustomer = $this->getConfigData('MODULE_PAYMENT_AUTHORIZENET_EMAIL_CUSTOMER') == 'True' ? 'TRUE': 'FALSE';
			$xEmailMerchant = $this->getConfigData('MODULE_PAYMENT_AUTHORIZENET_EMAIL_MERCHANT') == 'True' ? 'TRUE': 'FALSE';
			$xType = $requestParams['xType'];

			$dataArray = array(
				'x_login'          => $this->login,
				'x_tran_key'       => $this->transkey,
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
			
			if (isset($requestParams['orderID'])) {
				$dataArray['x_invoice_num'] = $requestParams['orderID'];
				$this->setParameter('orderInvoiceNumber', $requestParams['orderID']);
			}
			if (isset($requestParams['description'])) {
				$dataArray['x_description'] = $requestParams['description'];
				$this->setParameter('description', 'description');
			}
			if (isset($requestParams['amount'])) {
				$dataArray['x_amount'] = $requestParams['amount'];
				$this->setParameter('amount', $requestParams['amount']);
			}
			if (isset($requestParams['currencyCode'])) {
				$dataArray['x_currency_code'] = $requestParams['currencyCode'];
			}
			if (isset($requestParams['customerId'])) {
				$dataArray['x_cust_id'] = $requestParams['customerId'];
				$this->setParameter('merchantCustomerId', $requestParams['customerId']);
			}
			if (isset($requestParams['customerEmail'])) {
				$dataArray['x_email'] = $requestParams['customerEmail'];
				$this->setParameter('email', $requestParams['customerEmail']);
			}
			if (isset($requestParams['customerIp'])){
				$dataArray['x_customer_ip'] = $requestParams['customerIp'];
			}
			if (isset($requestParams['customerFirstName'])){
				$dataArray['x_first_name'] = $requestParams['customerFirstName'];
				$this->setParameter('billToFirstName', $requestParams['customerFirstName']);
			}
			if (isset($requestParams['customerLastName'])){
				$dataArray['x_last_name'] = $requestParams['customerLastName'];
				$this->setParameter('billToLastName', $requestParams['customerLastName']);
			}

			if (isset($requestParams['customerCompany'])){
				$dataArray['x_company'] = $requestParams['customerCompany'];
			}
			if (isset($requestParams['customerStreetAddress'])){
				$dataArray['x_address'] = $requestParams['customerStreetAddress'];
				$this->setParameter('billToAddress', $requestParams['customerStreetAddress']);
			}
			if (isset($requestParams['customerPostcode'])){
				$dataArray['x_zip'] = $requestParams['customerPostcode'];
				$this->setParameter('billToZip', $requestParams['customerPostcode']);
			}
			if (isset($requestParams['customerCity'])){
				$dataArray['x_city'] = $requestParams['customerCity'];
				$this->setParameter('billToCity', $requestParams['customerCity']);
			}
			if (isset($requestParams['customerState'])){
				$dataArray['x_state'] = $requestParams['customerState'];
				$this->setParameter('billToState', $requestParams['customerState']);
			}
			if (isset($requestParams['customerTelephone'])){
				$dataArray['x_phone'] = $requestParams['customerTelephone'];
				$this->setParameter('billToPhoneNumber', $requestParams['customerTelephone']);
			}
			if (isset($requestParams['customerFax'])){
				$dataArray['x_fax'] = $requestParams['customerFax'];
				$this->setParameter('billToFaxNumber', $requestParams['customerFax']);
			}
			if (isset($requestParams['customerCountry'])){
				$dataArray['x_country'] = $requestParams['customerCountry'];
				$this->setParameter('billToCountry', $requestParams['customerCountry']);
			}
			if (isset($requestParams['cardNum'])){
				$dataArray['x_card_num'] = $requestParams['cardNum'];
				$this->setParameter('cardNumber', $requestParams['cardNum']);
			}
			if (isset($requestParams['cardExpDate'])){
				$dataArray['x_exp_date'] = $requestParams['cardExpDate'];
				$this->setParameter('expirationDate', $requestParams['cardExpDateCIM']);
			}
			if (isset($requestParams['cardCvv'])){
				$dataArray['x_card_code'] = $requestParams['cardCvv'];
				$this->setParameter('cardCode', $requestParams['cardCvv']);
			}

			if($this->cim_mode == true ){
				if(!isset($_POST['payment_profile']) || $_POST['payment_profile'] == -1){

					$this->createCustomerProfile();
					if(strpos($this->getResponse(), 'A duplicate record with ID') !== false){
						$profile_id = substr($this->getResponse(),strlen('A duplicate record with ID '), strpos($this->getResponse(),' already') - strlen('A duplicate record with ID '));
						if(!isset($this->profileId) && !empty($profile_id)){
							$this->profileId = $profile_id;
							$this->setParameter('customerProfileId', $profile_id);
						}else{
							$this->createCustomerProfile();
							$profile_id = substr($this->getResponse(),strlen('A duplicate record with ID '), strpos($this->getResponse(),' already') - strlen('A duplicate record with ID '));
							$this->setParameter('customerProfileId', $profile_id);
						}
					}else{
						$this->createCustomerProfile();
						$profile_id = substr($this->getResponse(),strlen('A duplicate record with ID '), strpos($this->getResponse(),' already') - strlen('A duplicate record with ID '));
						$this->setParameter('customerProfileId', $profile_id);
					}

					$this->createCustomerPaymentProfile();
					if(strpos($this->getResponse(), 'A duplicate customer payment profile already exists') !== false){
						$this->createCustomerProfile();
						$profileID = '';
						if(strpos($this->getResponse(), 'A duplicate record with ID') !== false){
							$profileID = substr($this->getResponse(),strlen('A duplicate record with ID '), strpos($this->getResponse(),' already') - strlen('A duplicate record with ID '));
							$this->profileId = $profileID;
							$this->setParameter('customerProfileId', $profileID);
						}
						$paymentCardsArr = array();
						$paymentProfilesArr = array();
						if($profileID != ''){
							$this->getCustomerProfile();
							if(isset($this->paymentProfiles)){
								foreach($this->paymentProfiles as $profile){
									$paymentCardsArr[] = (string)$profile->payment->creditCard->cardNumber;
									$paymentProfilesArr[] = (string)$profile->customerPaymentProfileId;
								}
							}
						}

						$payment_profile_id = -1;
						foreach($paymentCardsArr as $key => $card){
							if(substr($paymentCardsArr[$key], -4, 4) == substr($requestParams['cardNum'], -4, 4)){
								$payment_profile_id = $paymentProfilesArr[$key];
								break;
							}

						}
						if($payment_profile_id == -1){
							$this->setErrorMessage('Payment Profile already exists');
							$messageStack->addSession('pageStack', 'Payment Profile already exists', 'error');
							return false;
						}

					}else{
						$payment_profile_id = $this->getPaymentProfileId();
					}

					$this->setParameter('customerPaymentProfileId', $payment_profile_id);
					//$this->setParameter('customerShippingAddressId', $shipping_profile_id);
					$this->createCustomerProfileTransaction((($xType == 'AUTH_ONLY')?'profileTransAuthOnly':'profileTransAuthCapture'));
					$message = $this->getResponse();
					//echo 'gs'.$message;
					if(isset($this->params['orderInvoiceNumber'])){
						$info['orderID'] = $this->params['orderInvoiceNumber'];
					}else{
						$info['orderID'] = '';
					}

					if(isset($this->params['customerProfileId'])){
						$info['profileID'] = $this->params['customerProfileId'];
					}else{
						$info['profileID'] = '';
					}

					if(isset($this->params['customerPaymentProfileId'])){
						$info['paymentProfile'] = $this->params['customerPaymentProfileId'];
					}else{
						$info['paymentProfile'] = '';
					}

					if(isset($this->params['amount'])){
						$info['amount'] = $this->params['amount'];
					}else{
						$info['amount'] = '';
					}

					if($message == 'Successful.' || (isset($order) && sysConfig::get('EXTENSION_PAY_PER_RENTALS_PROCESS_SEND') == 'True' && $order->info['total'] == 0)){
						if(isset($order) &&sysConfig::get('EXTENSION_PAY_PER_RENTALS_PROCESS_SEND') == 'True' && $order->info['total'] == 0){
							$info['message'] = 'Payment on hold';
						}else{
							$info['message'] = 'Approval Code:'.$this->getAuthCode();
						}
						$info['transId'] = (isset($this->transactionId)?$this->transactionId:'');
						return $this->CIMSuccess($info, $isCron);
					}else{
						$info['message'] = $this->getResponse();
						return $this->CIMFail($info, $isCron);
					}

				}else{
					$profile_id = '';
					$this->createCustomerProfile();
					if(strpos($this->getResponse(), 'A duplicate record with ID') !== false){
						$profile_id = substr($this->getResponse(),strlen('A duplicate record with ID '), strpos($this->getResponse(),' already') - strlen('A duplicate record with ID '));
						$this->profileId = $profile_id;
						$this->setParameter('customerProfileId', $profile_id);
					}
					$payment_profile_id = $_POST['payment_profile'];
					$this->setParameter('customerProfileId', $profile_id);
					$this->setParameter('customerPaymentProfileId', $payment_profile_id);
					//$this->setParameter('customerShippingAddressId', $shipping_profile_id);
					$this->createCustomerProfileTransaction((($xType == 'AUTH_ONLY')?'profileTransAuthOnly':'profileTransAuthCapture'));
					$message = $this->getResponse();

					if(isset($this->params['orderInvoiceNumber'])){
						$info['orderID'] = $this->params['orderInvoiceNumber'];
					}else{
						$info['orderID'] = '';
					}

					if(isset($this->params['customerProfileId'])){
						$info['profileID'] = $this->params['customerProfileId'];
					}else{
						$info['profileID'] = '';
					}

					if(isset($this->params['customerPaymentProfileId'])){
						$info['paymentProfile'] = $this->params['customerPaymentProfileId'];
					}else{
						$info['paymentProfile'] = '';
					}

					if(isset($this->params['amount'])){
						$info['amount'] = $this->params['amount'];
					}else{
						$info['amount'] = '';
					}

					if($message == 'Successful.'){
						$info['message'] = 'Approval Code:'.$this->getAuthCode();
						$info['transId'] = (isset($this->transactionId)?$this->transactionId:'');
						return $this->CIMSuccess($info, $isCron);
					}else{
						$info['message'] = $this->getResponse();
						return $this->CIMFail($info, $isCron);
					}
				}
			}else{
				$CurlRequest = new CurlRequest($this->gatewayUrl);
				$CurlRequest->setData($dataArray);
				$CurlResponse = $CurlRequest->execute();

				return $this->onResponse($CurlResponse, $isCron);
			}
		}

		private function onResponse($CurlResponse, $isCron = false){
			global $order;
			$response = $CurlResponse->getResponse();
			$response = explode(',', $response);

			$code = $response[0];
			$subCode = $response[1];
			$reasonCode = $response[2];
			$reasonText = $response[3];

			$this->transactionId = $response[6];
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

			if ($success === true || (isset($order) && sysConfig::get('EXTENSION_PAY_PER_RENTALS_PROCESS_SEND') == 'True' && $order->info['total'] == 0)){
				if(isset($order) && sysConfig::get('EXTENSION_PAY_PER_RENTALS_PROCESS_SEND') == 'True' && $order->info['total'] == 0){
					$errMsg = 'Payment on hold';
				}
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

		private function CIMSuccess($info, $isCron = false){
			$cardDetails = array(
					'customerProfile'    => $info['profileID'],
					'paymentProfile'     => $info['paymentProfile'],
					'transId'            => $info['transId']
			);

			if ($isCron === true){
				$this->cronMsg = 'Successful Transaction';
			}

			$this->logPayment(array(
				'orderID' => $info['orderID'],
				'amount'  => $info['amount'],
				'message' => $info['message'],
				'success' => 1,
				'can_reuse' => (isset($_POST['canReuse'])?1:0),
				'cardDetails' => $cardDetails
			));

			return true;
		}

		private function CIMFail($info, $isCron = false){
			global $messageStack;
			$this->setErrorMessage($this->getTitle() . ' : ' . $info['message']);
			if ($isCron === true){
				$this->cronMsg = $this->getTitle() . ' : ' . $info['message'];
			}
			$orderId = $info['orderID'];
			if ($this->removeOrderOnFail === true){
				$Order = Doctrine_Core::getTable('Orders')->find($orderId);
				if ($Order){
					$Order->delete();
				}

				$messageStack->addSession('pageStack', $info['message'], 'error');
			}else{
				$this->logPayment(array(
					'orderID' => $orderId,
					'amount'  => $info['amount'],
					'message' => $info['message'],
					'success' => 0,
					'cardDetails' => array(
						'customerProfile'    => $info['profileID'],
						'paymentProfile'     => $info['paymentProfile']
					)
				));
			}

			return false;
		}

		private function onSuccess($info){
			$RequestData = $info['curlResponse']->getDataRaw();
			$orderId = $RequestData['x_invoice_num'];

			$cardDetails = array(
					'cardOwner'    => $RequestData['x_first_name'] . ' ' . $RequestData['x_last_name'],
					'cardNumber'   => $RequestData['x_card_num'],
					'cardExpMonth' => substr($RequestData['x_exp_date'], 0, 2),
					'cardExpYear'  => substr($RequestData['x_exp_date'], 2),
					'cardCvvNumber'  => $RequestData['x_card_code'],
					'transId'      => (isset($this->transactionId)?$this->transactionId:'')
			);

			$this->logPayment(array(
				'orderID' => $orderId,
				'amount'  => $RequestData['x_amount'],
				'message' => $info['message'],
				'success' => 1,
				'can_reuse' => (isset($_POST['canReuse'])?1:0),
				'cardDetails' => $cardDetails
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
			}else{
				$this->logPayment(array(
					'orderID' => $orderId,
					'amount'  => $RequestData['x_amount'],
					'message' => $info['message'],
					'success' => 0,
					'cardDetails' => array(
						'cardOwner'    => $RequestData['x_first_name'] . ' ' . $RequestData['x_last_name'],
						'cardNumber'   => $RequestData['x_card_num'],
						'cardExpMonth' => $RequestData['x_exp_date'],
						'cardExpYear'  => $RequestData['x_exp_date']
					)
				));
			}
		}
	private function processCIM(){
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_URL, $this->urlCIM);
    	curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($this->ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
    	curl_setopt($this->ch, CURLOPT_HEADER, 0);
    	curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->xml);
    	curl_setopt($this->ch, CURLOPT_POST, 1);
    	curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
        $this->response = curl_exec($this->ch);
        if($this->response){
//	        echo $this->response.'----';
            $this->parseResults();
            if ($this->resultCode === 'Ok'){
                $this->success = true;
                $this->error   = false;
            }
            else{
                $this->success = false;
                $this->error   = true;
            }
            curl_close($this->ch);
            unset($this->ch);
        }
        else{
            throw new AuthnetCIMException('Connection error: ' . curl_error($this->ch) . ' (' . curl_errno($this->ch) . ')', self::EXCEPTION_CURL);
        }
    }

    public function createCustomerProfile($use_profiles = false, $type = 'credit'){
	    //$this->getCustomerProfileIds();

        $this->xml = '<?xml version="1.0" encoding="utf-8"?>
                      <createCustomerProfileRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
                          <merchantAuthentication>
                              <name>' . $this->login . '</name>
                              <transactionKey>' . $this->transkey . '</transactionKey>
                          </merchantAuthentication>';
        if (isset($this->params['refId']) && !empty($this->params['refId'])){
            $this->xml .= '
                          <refId>'. $this->params['refId'] .'</refId>';
        }
            $this->xml .= '
                          <profile>
                              <merchantCustomerId>'. $this->params['merchantCustomerId'].'</merchantCustomerId>
                              <description>'. $this->params['description'].'</description>
                              <email>'. $this->params['email'] .'</email>';

        if ($use_profiles == true){
            $this->xml .= '
                              <paymentProfiles>
                                  <customerType>'. $this->params['customerType'].'</customerType>
                                  <billTo>
                                      <firstName>'. $this->params['billToFirstName'].'</firstName>
                                      <lastName>'.$this->params['billToLastName'].'</lastName>
                                      <company>'. (isset($this->params['billToCompany'])?$this->params['billToCompany']:'') .'</company>
                                      <address>'. $this->params['billToAddress'] .'</address>
                                      <city>'. $this->params['billToCity'] .'</city>
                                      <state>'. $this->params['billToState'] .'</state>
                                      <zip>'. $this->params['billToZip'] .'</zip>
                                      <country>'. $this->params['billToCountry'] .'</country>
                                      <phoneNumber>'. (isset($this->params['billToPhoneNumber'])?$this->params['billToPhoneNumber']:'').'</phoneNumber>
                                      <faxNumber>'. (isset($this->params['billToFaxNumber'])?$this->params['billToFaxNumber']:'').'</faxNumber>
                                  </billTo>
                                  <payment>';
            if ($type === 'credit'){
                $this->xml .= '
                                      <creditCard>
                                          <cardNumber>'. $this->params['cardNumber'].'</cardNumber>
                                          <expirationDate>'.$this->params['expirationDate'].'</expirationDate>
                                      </creditCard>';
            }
            else if ($type === 'check'){
                $this->xml .= '
                                      <bankAccount>
                                          <accountType>'.$this->params['accountType'].'</accountType>
                                          <nameOnAccount>'.$this->params['nameOnAccount'].'</nameOnAccount>
                                          <echeckType>'. $this->params['echeckType'].'</echeckType>
                                          <bankName>'. $this->params['bankName'].'</bankName>
                                          <routingNumber>'.$this->params['routingNumber'].'</routingNumber>
                                          <accountNumber>'.$this->params['accountNumber'].'</accountNumber>
                                      </bankAccount>
                                      <driversLicense>
                                          <dlState>'. $this->params['dlState'].'</dlState>
                                          <dlNumber>'. $this->params['dlNumber'].'</dlNumber>
                                          <dlDateOfBirth>'.$this->params['dlDateOfBirth'].'</dlDateOfBirth>
                                      </driversLicense>';
            }
            $this->xml .= '
                                  </payment>
                              </paymentProfiles>
                              <shipToList>
                                  <firstName>'. $this->params['shipToFirstName'].'</firstName>
                                  <lastName>'. $this->params['shipToLastName'].'</lastName>
                                  <company>'. $this->params['shipToCompany'] .'</company>
                                  <address>'. $this->params['shipToAddress'] .'</address>
                                  <city>'. $this->params['shipToCity'] .'</city>
                                  <state>'. $this->params['shipToState'] .'</state>
                                  <zip>'. $this->params['shipToZip'] .'</zip>
                                  <country>'. $this->params['shipToCountry'] .'</country>
                                  <phoneNumber>'. $this->params['shipToPhoneNumber'].'</phoneNumber>
                                  <faxNumber>'. $this->params['shipToFaxNumber'].'</faxNumber>
                              </shipToList>';
        }
            $this->xml .= '
                          </profile>
                      </createCustomerProfileRequest>';

        $this->processCIM();
    }

    public function createCustomerPaymentProfile($type = 'credit'){
        $this->xml = '<?xml version="1.0" encoding="utf-8"?>
                      <createCustomerPaymentProfileRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
                          <merchantAuthentication>
                              <name>' . $this->login . '</name>
                              <transactionKey>' . $this->transkey . '</transactionKey>
                          </merchantAuthentication>
                          <refId>'. (isset($this->params['refId'])?$this->params['refId']:'') .'</refId>
                          <customerProfileId>'. $this->params['customerProfileId'].'</customerProfileId>
                          <paymentProfile>
                              <customerType>'. $this->params['customerType'].'</customerType>
                              <billTo>
                                  <firstName>'. $this->params['billToFirstName'].'</firstName>
                                  <lastName>'. $this->params['billToLastName'].'</lastName>
                                  <company>'. (isset($this->params['billToCompany'])?$this->params['billToCompany']:'') .'</company>
                                  <address>'. $this->params['billToAddress'] .'</address>
                                  <city>'. $this->params['billToCity'] .'</city>
                                  <state>'. $this->params['billToState'] .'</state>
                                  <zip>'. $this->params['billToZip'] .'</zip>
                                  <country>'. $this->params['billToCountry'] .'</country>
                                  <phoneNumber>'. (isset($this->params['billToPhoneNumber'])?$this->params['billToPhoneNumber']:'').'</phoneNumber>
                                  <faxNumber>'. (isset($this->params['billToFaxNumber'])?$this->params['billToFaxNumber']:'').'</faxNumber>
                              </billTo>
                              <payment>';
        if ($type === 'credit'){
            $this->xml .= '
                                  <creditCard>
                                      <cardNumber>'. $this->params['cardNumber'].'</cardNumber>
                                      <expirationDate>'.$this->params['expirationDate'].'</expirationDate>
                                  </creditCard>';
        }
        else if ($type === 'check'){
            $this->xml .= '
                                  <bankAccount>
                                      <accountType>'. $this->params['accountType'].'</accountType>
                                      <nameOnAccount>'.$this->params['nameOnAccount'].'</nameOnAccount>
                                      <echeckType>'. $this->params['echeckType'].'</echeckType>
                                      <bankName>'. $this->params['bankName'].'</bankName>
                                      <routingNumber>'.$this->params['routingNumber'].'</routingNumber>
                                      <accountNumber>'.$this->params['accountNumber'].'</accountNumber>
                                  </bankAccount>
                                  <driversLicense>
                                      <dlState>'. $this->params['dlState'] .'</dlState>
                                      <dlNumber>'. $this->params['dlNumber'].'</dlNumber>
                                      <dlDateOfBirth>'.$this->params['dlDateOfBirth'].'</dlDateOfBirth>
                                  </driversLicense>';
        }
        $this->xml .= '
                              </payment>
                          </paymentProfile>
                          <validationMode>'. $this->params['validationMode'].'</validationMode>
                      </createCustomerPaymentProfileRequest>';
        $this->processCIM();
    }

    public function createCustomerShippingAddress(){
        $this->xml = '<?xml version="1.0" encoding="utf-8"?>
                      <createCustomerShippingAddressRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
                          <merchantAuthentication>
                              <name>' . $this->login . '</name>
                              <transactionKey>' . $this->transkey . '</transactionKey>
                          </merchantAuthentication>
                          <refId>'. $this->params['refId'] .'</refId>
                          <customerProfileId>'. $this->params['customerProfileId'].'</customerProfileId>
                          <address>
                              <firstName>'. $this->params['shipToFirstName'].'</firstName>
                              <lastName>'. $this->params['shipToLastName'].'</lastName>
                              <company>'. $this->params['shipToCompany'] .'</company>
                              <address>'. $this->params['shipToAddress'] .'</address>
                              <city>'. $this->params['shipToCity'] .'</city>
                              <state>'. $this->params['shipToState'] .'</state>
                              <zip>'. $this->params['shipToZip'] .'</zip>
                              <country>'. $this->params['shipToCountry'] .'</country>
                              <phoneNumber>'. $this->params['shipToPhoneNumber'].'</phoneNumber>
                              <faxNumber>'. $this->params['shipToFaxNumber'].'</faxNumber>
                          </address>
                      </createCustomerShippingAddressRequest>';
        $this->processCIM();
    }

    public function createCustomerProfileTransaction($type = 'profileTransAuthCapture'){
        $this->xml = '<?xml version="1.0" encoding="utf-8"?>
                      <createCustomerProfileTransactionRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
                          <merchantAuthentication>
                              <name>' . $this->login . '</name>
                              <transactionKey>' . $this->transkey . '</transactionKey>
                          </merchantAuthentication>
                          <refId>'. (isset($this->params['refId'])?$this->params['refId']:'') .'</refId>
                          <transaction>
                              <' . $type . '>
                                  <amount>'. number_format($this->params['amount'],2) .'</amount>';
        if (isset($this->params['taxAmount'])){
            $this->xml .= '
                                  <tax>
                                       <amount>'. $this->params['taxAmount'].'</amount>
                                       <name>'. $this->params['taxName'] .'</name>
                                       <description>'.$this->params['taxDescription'].'</description>
                                  </tax>';
        }
        if (isset($this->params['shipAmount'])){
            $this->xml .= '
                                  <shipping>
                                       <amount>'. $this->params['shipAmount'].'</amount>
                                       <name>'. $this->params['shipName'] .'</name>
                                       <description>'.$this->params['shipDescription'].'</description>
                                  </shipping>';
        }
        if (isset($this->params['dutyAmount'])){
            $this->xml .= '
                                  <duty>
                                       <amount>'. $this->params['dutyAmount'].'</amount>
                                       <name>'. $this->params['dutyName'] .'</name>
                                       <description>'.$this->params['dutyDescription'].'</description>
                                  </duty>';
        }
        $this->xml .= '

                                  <customerProfileId>'.$this->params['customerProfileId'].'</customerProfileId>
                                  <customerPaymentProfileId>'.$this->params['customerPaymentProfileId'].'</customerPaymentProfileId> ';

        if (isset($this->params['orderInvoiceNumber'])){
            $this->xml .= '
                                  <order>
                                       <invoiceNumber>'.$this->params['orderInvoiceNumber'].'</invoiceNumber>
                                  </order>';
        }
        $this->xml .= '
                                  <taxExempt>'. $this->params['taxExempt'].'</taxExempt>
                                  <recurringBilling>'.$this->params['recurringBilling'].'</recurringBilling>';
	    if(isset($this->params['cardCode'])){
            $this->xml .= '<cardCode>'. $this->params['cardCode'].'</cardCode>';
        }
       /* if (isset($this->params['orderInvoiceNumber'])){
            $this->xml .= '
                                  <approvalCode>'. $this->params['approvalCode'].'</approvalCode>';
        }*/
        $this->xml .= '
                              </' . $type . '>
                          </transaction>
                      </createCustomerProfileTransactionRequest>';
        $this->processCIM();
    }

    public function deleteCustomerProfile(){
        $this->xml = '<?xml version="1.0" encoding="utf-8"?>
                      <deleteCustomerProfileRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
                          <merchantAuthentication>
                              <name>' . $this->login . '</name>
                              <transactionKey>' . $this->transkey . '</transactionKey>
                          </merchantAuthentication>
                          <refId>'. $this->params['refId'] .'</refId>
                          <customerProfileId>'. $this->params['customerProfileId'].'</customerProfileId>
                      </deleteCustomerProfileRequest>';
        $this->processCIM();
    }

    public function deleteCustomerPaymentProfile(){
        $this->xml = '<?xml version="1.0" encoding="utf-8"?>
                      <deleteCustomerPaymentProfileRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
                          <merchantAuthentication>
                              <name>' . $this->login . '</name>
                              <transactionKey>' . $this->transkey . '</transactionKey>
                          </merchantAuthentication>
                          <refId>'. $this->params['refId'] .'</refId>
                          <customerProfileId>'. $this->params['customerProfileId'].'</customerProfileId>
                          <customerPaymentProfileId>'.$this->params['customerPaymentProfileId'].'</customerPaymentProfileId>
                      </deleteCustomerPaymentProfileRequest>';
        $this->processCIM();
    }

    public function deleteCustomerShippingAddress(){
        $this->xml = '<?xml version="1.0" encoding="utf-8"?>
                      <deleteCustomerShippingAddressRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
                          <merchantAuthentication>
                              <name>' . $this->login . '</name>
                              <transactionKey>' . $this->transkey . '</transactionKey>
                          </merchantAuthentication>
                          <refId>'. $this->params['refId'] .'</refId>
                          <customerProfileId>'. $this->params['customerProfileId'].'</customerProfileId>
                          <customerAddressId>'. $this->params['customerAddressId'].'</customerAddressId>
                      </deleteCustomerShippingAddressRequest>';
        $this->processCIM();
    }

	public function getCustomerProfileIds(){
		    $this->xml = '<?xml version="1.0" encoding="utf-8"?>
		                  <getCustomerProfileIdsRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
		                      <merchantAuthentication>
		                          <name>' . $this->login . '</name>
		                          <transactionKey>' . $this->transkey . '</transactionKey>
		                      </merchantAuthentication>
		                  </getCustomerProfileIdsRequest>';
		    $this->processCIM();
	}


    public function getCustomerProfile(){
        $this->xml = '<?xml version="1.0" encoding="utf-8"?>
                      <getCustomerProfileRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
                          <merchantAuthentication>
                              <name>' . $this->login . '</name>
                              <transactionKey>' . $this->transkey . '</transactionKey>
                          </merchantAuthentication>
                          <customerProfileId>'. $this->params['customerProfileId'].'</customerProfileId>
                      </getCustomerProfileRequest>';
        $this->processCIM();
    }

    public function getCustomerPaymentProfile(){
        $this->xml = '<?xml version="1.0" encoding="utf-8"?>
                      <getCustomerPaymentProfileRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
                          <merchantAuthentication>
                              <name>' . $this->login . '</name>
                              <transactionKey>' . $this->transkey . '</transactionKey>
                          </merchantAuthentication>
                          <customerProfileId>'. $this->params['customerProfileId'].'</customerProfileId>
                          <customerPaymentProfileId>'.$this->params['customerPaymentProfileId'].'</customerPaymentProfileId>
                      </getCustomerPaymentProfileRequest>';
        $this->processCIM();
    }

    public function getCustomerShippingAddress(){
        $this->xml = '<?xml version="1.0" encoding="utf-8"?>
                      <getCustomerShippingAddressRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
                          <merchantAuthentication>
                              <name>' . $this->login . '</name>
                              <transactionKey>' . $this->transkey . '</transactionKey>
                          </merchantAuthentication>
                              <customerProfileId>'.$this->params['customerProfileId'].'</customerProfileId>
                              <customerAddressId>'.$this->params['customerAddressId'].'</customerAddressId>
                      </getCustomerShippingAddressRequest>';
        $this->processCIM();
    }

    public function updateCustomerProfile(){
        $this->xml = '<?xml version="1.0" encoding="utf-8"?>
                      <updateCustomerProfileRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
                          <merchantAuthentication>
                              <name>' . $this->login . '</name>
                              <transactionKey>' . $this->transkey . '</transactionKey>
                          </merchantAuthentication>
                          <refId>'. $this->params['refId'] .'</refId>
                          <profile>
                              <merchantCustomerId>'.$this->params['merchantCustomerId'].'</merchantCustomerId>
                              <description>'. $this->params['description'].'</description>
                              <email>'. $this->params['email'] .'</email>
                              <customerProfileId>'.$this->params['customerProfileId'].'</customerProfileId>
                          </profile>
                      </updateCustomerProfileRequest>';
        $this->processCIM();
    }

    public function updateCustomerPaymentProfile($type = 'credit'){
        $this->xml = '<?xml version="1.0" encoding="utf-8"?>
                      <updateCustomerPaymentProfileRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
                          <merchantAuthentication>
                              <name>' . $this->login . '</name>
                              <transactionKey>' . $this->transkey . '</transactionKey>
                          </merchantAuthentication>
                          <refId>'. $this->params['refId'] .'</refId>
                          <customerProfileId>'. $this->params['customerProfileId'].'</customerProfileId>
                          <paymentProfile>
                              <customerType>'. $this->params['customerType'].'</customerType>
                              <billTo>
                                  <firstName>'. $this->params['firstName'].'</firstName>
                                  <lastName>'. $this->params['lastName'] .'</lastName>
                                  <company>'. $this->params['company'] .'</company>
                                  <address>'. $this->params['address'] .'</address>
                                  <city>'. $this->params['city'] .'</city>
                                  <state>'. $this->params['state'] .'</state>
                                  <zip>'. $this->params['zip'] .'</zip>
                                  <country>'. $this->params['country'] .'</country>
                                  <phoneNumber>'. $this->params['phoneNumber'].'</phoneNumber>
                                  <faxNumber>'. $this->params['faxNumber'].'</faxNumber>
                              </billTo>
                              <payment>';
        if ($type === 'credit'){
            $this->xml .= '
                                  <creditCard>
                                      <cardNumber>'. $this->params['cardNumber'].'</cardNumber>
                                      <expirationDate>'.$this->params['expirationDate'].'</expirationDate>
                                  </creditCard>';
        }
        else if ($type === 'check'){
            $this->xml .= '
                                  <bankAccount>
                                      <accountType>'.$this->params['accountType'].'</accountType>
                                      <nameOnAccount>'.$this->params['nameOnAccount'].'</nameOnAccount>
                                      <echeckType>'. $this->params['echeckType'].'</echeckType>
                                      <bankName>'. $this->params['bankName'].'</bankName>
                                      <routingNumber>'.$this->params['routingNumber'].'</routingNumber>
                                      <accountNumber>'.$this->params['accountNumber'].'</accountNumber>
                                  </bankAccount>
                                  <driversLicense>
                                      <dlState>'. $this->params['dlState'].'</dlState>
                                      <dlNumber>'. $this->params['dlNumber'].'</dlNumber>
                                      <dlDateOfBirth>'.$this->params['dlDateOfBirth'].'</dlDateOfBirth>
                                  </driversLicense>';
        }
        $this->xml .= '
                              </payment>
                              <customerPaymentProfileId>'.$this->params['customerPaymentProfileId'].'</customerPaymentProfileId>
                          </paymentProfile>
                      </updateCustomerPaymentProfileRequest>';
        $this->processCIM();
    }

    public function updateCustomerShippingAddress(){
        $this->xml = '<?xml version="1.0" encoding="utf-8"?>
                      <updateCustomerShippingAddressRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
                          <merchantAuthentication>
                              <name>' . $this->login . '</name>
                              <transactionKey>' . $this->transkey . '</transactionKey>
                          </merchantAuthentication>
                          <refId>'. $this->params['refId'] .'</refId>
                          <customerProfileId>'. $this->params['customerProfileId'].'</customerProfileId>
                          <address>
                              <firstName>'. $this->params['firstName'] .'</firstName>
                              <lastName>'. $this->params['lastName'] .'</lastName>
                              <company>'. $this->params['company'] .'</company>
                              <address>'. $this->params['address'] .'</address>
                              <city>'. $this->params['city'] .'</city>
                              <state>'. $this->params['state'] .'</state>
                              <zip>'. $this->params['zip'] .'</zip>
                              <country>'. $this->params['country'] .'</country>
                              <phoneNumber>'. $this->params['phoneNumber'].'</phoneNumber>
                              <faxNumber>'. $this->params['faxNumber'] .'</faxNumber>
                              <customerAddressId>'.$this->params['customerAddressId'].'</customerAddressId>
                          </address>
                      </updateCustomerShippingAddressRequest>';
        $this->processCIM();
    }

    public function validateCustomerPaymentProfile(){
        $this->xml = '<?xml version="1.0" encoding="utf-8"?>
                      <validateCustomerPaymentProfileRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
                          <merchantAuthentication>
                              <name>' . $this->login . '</name>
                              <transactionKey>' . $this->transkey . '</transactionKey>
                          </merchantAuthentication>
                          <customerProfileId>'. $this->params['customerProfileId'].'</customerProfileId>
                          <customerPaymentProfileId>'.$this->params['customerPaymentProfileId'].'</customerPaymentProfileId>
                          <customerAddressId>'. $this->params['customerAddressId'].'</customerAddressId>
                          <validationMode>'. $this->params['validationMode'].'</validationMode>
                      </validateCustomerPaymentProfileRequest>';
        $this->processCIM();
    }

	public function refundTransaction($requestData){
		$this->xml ='<?xml version="1.0" encoding="utf-8"?>
                      <createCustomerProfileTransactionRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
                          <merchantAuthentication>
                              <name>' . $this->login . '</name>
                              <transactionKey>' . $this->transkey . '</transactionKey>
                          </merchantAuthentication>
                          <transaction>
                              <profileTransRefund>
								  <amount>'. number_format($requestData['amount'],2).'</amount>
								  <customerProfileId>'.$requestData['cardDetails']['customerProfile'].'</customerProfileId>
								  <customerPaymentProfileId>'.$requestData['cardDetails']['paymentProfile'].'</customerPaymentProfileId>
								  <transId>'.$requestData['transId'].'</transId>
                              </profileTransRefund>
                          </transaction>
                      </createCustomerProfileTransactionRequest>';
        $this->processCIM();
	}

    private function getLineItems(){
        $tempXml = '';
        foreach ($this->items as $item)
        {
            foreach ($item as $key => $value)
            {
                $tempXml .= "\t" . '<' . $key . '>' . $value . '</' . $key . '>' . "\n";
            }
        }
        return $tempXml;
    }

    public function setLineItem($itemId, $name, $description, $quantity, $unitprice,$taxable = 'false'){
        $this->items[] = array('itemId' => $itemId, 'name' => $name, 'description' => $description, 'quantity' => $quantity, 'unitPrice' => $unitprice, 'taxable' => $taxable);
    }

    public function setParameter($field = '', $value = null){
	    if(!empty($value)){
			$field = (is_string($field)) ? trim($field) : $field;
			$value = (is_string($value)) ? trim($value) : $value;
			$this->params[$field] = $value;
	    }
    }

    private function parseResults(){
        $response = str_replace('xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd"', '', $this->response);
        $xml = new SimpleXMLElement($response);
	    //echo 'kkk'.print_r($xml);
	    if(isset($xml->ids)){
		    $this->ids[] = array();
		    //echo $xml->ids .'--'.print_r($xml->ids);
			foreach($xml->ids->numericString as $id){
				$this->ids[] = (string)$id;
			}
		    // echo print_r($this->ids).'eee';
	    }

	    if(isset($xml->profile->paymentProfiles)){
		    $this->paymentProfiles = array();
		    //echo $xml->profile->paymentProfiles .'--'.print_r($xml->paymentProfiles);
			foreach($xml->profile->paymentProfiles as $payment){
				$this->paymentProfiles[] = $payment;
			}
		    // echo print_r($this->ids).'eee';
	    }


        $this->resultCode       = (string) $xml->messages->resultCode;
        $this->rCode             = (string) $xml->messages->message->code;
        $this->text             = (string) $xml->messages->message->text;
        $this->validation       = (string) $xml->validationDirectResponse;
        $this->directResponse   = (string) $xml->directResponse;
	    if(isset($xml->profile->customerProfileId)){
            $this->profileId        = (int) $xml->profile->customerProfileId;
        }
	    if(isset($xml->transId)){
		    $this->transactionId = (string)$xml->transId;
	    }
	    if(isset($xml->customerPaymentProfileId)){
            $this->paymentProfileId = (int) $xml->customerPaymentProfileId;
	    }
        $this->results          = explode(',', $this->directResponse);
    }

    public function isSuccessful(){
        return $this->success;
    }

    public function isError(){
        return $this->error;
    }

    public function getResponseSummary(){
        return 'Response code: ' . $this->getRCode() . ' Message: ' . $this->getResponse();
    }

    public function getResponse(){
        return strip_tags($this->text);
    }

    public function getRCode(){
        return $this->rCode;
    }

    public function getProfileID(){
        return $this->profileId;
    }

    public function validationDirectResponse(){
        return $this->validation;
    }

    public function getCustomerAddressId(){
        return $this->addressId;
    }

    public function getDirectResponse(){
        return $this->directResponse;
    }

    public function getPaymentProfileId(){
        return $this->paymentProfileId;
    }

    public function getResponseSubcode(){
        return $this->results[1];
    }

    public function getResponseCode(){
        return $this->results[2];
    }

    public function getResponseText(){
        return $this->results[3];
    }

    public function getAuthCode(){
        return $this->results[4];
    }

    public function getAVSResponse(){
        return $this->results[5];
    }

    public function getTransactionID(){
        return $this->results[6];
    }

    public function getCVVResponse(){
        return $this->results[38];
    }

    public function getCAVVResponse(){
        return $this->results[39];
    }
	}
?>