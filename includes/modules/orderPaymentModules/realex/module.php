<?php
/*
	Rental Store Version 2

	I.T. Web Experts
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	class OrderPaymentRealex extends CreditCardModule {

		public function __construct(){
			/*
			 * Default title and description for modules that are not yet installed
			 */
			$this->setTitle('Credit Card Via Realex');
			$this->setDescription('Credit Card Via Realex');

			$this->init('realex');

			if ($this->isEnabled() === true){
				$this->isCron = false;
				$this->removeOrderOnFail = false;
				$this->requireCvv = true;
				$this->can_reuse = $this->getReuses();
				$this->startNumbersRejected = explode(',', $this->getConfigData('MODULE_PAYMENT_REALEX_REJECTED_CC'));
				$this->allowedTypes = array();

				// Credit card pulldown list
				$cc_array = explode(',', $this->getConfigData('MODULE_PAYMENT_REALEX_ACCEPTED_CC'));
				foreach($cc_array as $k => $v){
					$this->allowedTypes[trim($v)] = $this->cardTypes[trim($v)];
				}

				$this->gatewayUrl = $this->getConfigData('MODULE_PAYMENT_REALEX_URL');
				$this->login    = trim($this->getConfigData('MODULE_PAYMENT_REALEX_MERCHANT_ID'));//'55tZyWtL9629';
				$this->transkey = trim($this->getConfigData('MODULE_PAYMENT_REALEX_SHARED_SECRET'));//'2h6t5Vh8n8Xd5EVZ';

				$this->params['customerType']     = 'individual';
				$this->params['validationMode']   = 'liveMode';
				$this->params['taxExempt']        = 'false';
				$this->params['recurringBilling'] = 'false';
				/*
				 * Use Realex's param dump to show what they are recieving from the server
				 */
				//$this->gatewayUrl = '';
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

		}

		public function validatePost(){
			if(!isset($_POST['payment_profile']) || $_POST['payment_profile'] == -1){
				return parent::validatePost();
			}
			return true;
		}

		public function onSelect(){
			global $onePageCheckout;
			$fieldsArray = array();

			$fieldsArray[] = array(
				'title' => sysLanguage::get('MODULE_PAYMENT_REALEX_TEXT_CREDIT_CARD_TYPE'),
				'field' => $this->getCreditCardTypeField()
			);

			$fieldsArray[] = array(
				'title' => sysLanguage::get('MODULE_PAYMENT_REALEX_TEXT_CREDIT_CARD_OWNER'),
				'field' => $this->getCreditCardOwnerField()
			);

			$fieldsArray[] = array(
				'title' => sysLanguage::get('MODULE_PAYMENT_REALEX_TEXT_CREDIT_CARD_NUMBER'),
				'field' => $this->getCreditCardNumber()
			);

			$fieldsArray[] = array(
				'title' => sysLanguage::get('MODULE_PAYMENT_REALEX_TEXT_CREDIT_CARD_EXPIRES'),
				'field' => $this->getCreditCardExpMonthField() . '&nbsp;' . $this->getCreditCardExpYearField()
			);

			if ($this->requireCvv === true){
				$fieldsArray[] = array(
					'title' => 'CVV number ' . ' ' .'<a href="#" onclick="popupWindow(\'' . itw_app_link('rType=ajax&appExt=infoPages&dialog=true', 'show_page', 'cvv_help') . '\', 400, 300);return false">' . '<u><i>' . '(' . sysLanguage::get('MODULE_PAYMENT_REALEX_TEXT_CVV_LINK') . ')' . '</i></u></a>',
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
		}

		public function processPayment(){
			global $order, $onePageCheckout;

			$this->removeOrderOnFail = true;
			if(isset($_POST['canReuse'])){
				$onePageCheckout->onePage['info']['payment']['cardDetails']['canReuse'] = true;
			}

			$paymentAmount = $order->info['total'];

			$userAccount = OrderPaymentModules::getUserAccount();
			$paymentInfo = OrderPaymentModules::getPaymentInfo();

			$addressBook =& $userAccount->plugins['addressBook'];
			$billingAddress = $addressBook->getAddress('billing');
			$countryInfo = $userAccount->plugins['addressBook']->getCountryInfo($billingAddress['entry_country_id']);

			$xExpDate = $paymentInfo['cardDetails']['cardExpMonth'] . $paymentInfo['cardDetails']['cardExpYear'];
			$expirationDate = $paymentInfo['cardDetails']['cardExpYear'].'-'. $paymentInfo['cardDetails']['cardExpMonth'];
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
			global $messageStack;
			$timestamp = strftime("%Y%m%d%H%M%S");
			$merchantid = $this->login;
			$orderid = $requestParams['orderID'];
			$curr = $requestParams['currencyCode'];
			$amount = number_format(($requestParams['amount'] * 100), 0, '', '');
			$amount2 = $requestParams['amount'];
			$secret = $this->transkey;
			$ccnum = $requestParams['cardNum'];
			$expdate = substr($requestParams['cardExpDate'], 0, 2) . substr($requestParams['cardExpDate'], 4);
			// creating the hash.
			$tmp = "$timestamp.$merchantid.$orderid.$amount.$curr.$ccnum";
			$md5hash = md5($tmp);
			$tmp = "$md5hash.$secret";
			$md5hash = md5($tmp);
			include_once(sysConfig::getDirFsCatalog() . 'includes/classes/cc_validation.php');
			$validator = new cc_validation();
			$cardType = $validator->getCardType($ccnum);
			if(ereg('^Master Card$', $cardType)){
				$cardType = 'MC';
			}
			if(ereg('^American Express$', $cardType)){
				$cardType = 'AMEX';
			}

			if(!empty($requestParams['cardCvv'])){
				$presind = "1";
			} else
				$presind = "4";

			$xml = "<request type='auth' timestamp='" . $timestamp . "'>
				<merchantid>" . $merchantid . "</merchantid>
				<account>internet</account>
				<orderid>" . $orderid . "</orderid>
				<amount currency='" . $curr . "'>" . $amount . "</amount>
				<card>
					<number>" . $ccnum . "</number>
					<expdate>" . $expdate . "</expdate>
					<type>" . $cardType . "</type>
					<chname>" . $requestParams['customerFirstName'] . " " . $requestParams['customerLastName'] . "</chname>
					<cvn>
					  <number>" . $requestParams['cardCvv'] . "</number>
					  <presind>$presind</presind>
					</cvn>

				</card>
				<autosettle flag='1'/>
				<comments><comment id='1'>" . $amount2 . "</comment></comments>
					<tssinfo>
					<address type='billing'>
						<code>" . $requestParams['customerPostcode'] . "</code>
						<country>" . $requestParams['customerCountry'] . "</country>
					</address>
					<custnum>" . $requestParams['customerId'] . "</custnum>
				</tssinfo>
				<md5hash>$md5hash</md5hash>
			</request>";

			$CurlRequest = new CurlRequest($this->gatewayUrl);
			$CurlRequest->setData($xml, 'string');
			$CurlRequest->setDataRaw($requestParams);
			$CurlResponse = $CurlRequest->execute();

			return $this->onResponse($CurlResponse);
		}

		private function onResponse($CurlResponse, $isCron = false){
			$success = false;
			//ob_end_clean();
			//var_dump($CurlResponse);
			//die();
			$response = $CurlResponse->getResponse();
			$response = eregi_replace("[[:space:]]+", " ", $response);
			$response = eregi_replace("[\n\r]", "", $response);
			preg_match("/<result>(.*?)<\/result>/i", $response, $matches);
			$XMLresult = $matches[1];
			preg_match("/<message>(.*)<\/message>/i", $response, $matches);
			$XMLmessage = $matches[1];
			$errMsg = $XMLresult . "     " . $XMLmessage;
			$realexResult = $XMLresult;

			if((string)$realexResult == "00"){
				$success = true;
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
			$orderId = $RequestData['orderID'];

			$cardDetails = array(
					'cardOwner'    => $RequestData['customerFirstName'] . ' ' . $RequestData['customerLastName'],
					'cardNumber'   => $RequestData['cardNum'],
					'cardExpMonth' => substr($RequestData['cardExpDate'], 0, 2),
					'cardExpYear'  => substr($RequestData['cardExpDate'], 2),
					'transId'      => (isset($this->transactionId)?$this->transactionId:'')
			);

			$this->logPayment(array(
				'orderID' => $orderId,
				'amount'  => $RequestData['amount'],
				'message' => $info['message'],
				'success' => 1,
				'can_reuse' => (isset($_POST['canReuse'])?1:0),
				'cardDetails' => $cardDetails
			));
		}

		private function onFail($info){
			global $messageStack;
			$RequestData = $info['curlResponse']->getDataRaw();
			$orderId = $RequestData['orderID'];
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
					'amount'  => $RequestData['amount'],
					'message' => $info['message'],
					'success' => 0,
					'cardDetails' => array(
						'cardOwner'    => $RequestData['customerFirstName'] . ' ' . $RequestData['customerLastName'],
						'cardNumber'   => $RequestData['cardNum'],
						'cardExpMonth' => substr($RequestData['cardExpDate'], 0, 2),
						'cardExpYear'  => substr($RequestData['cardExpDate'], 2)
					)
				));
			}
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
/*
    public function setParameter($field = '', $value = null){
	    if(!empty($value)){
			$field = (is_string($field)) ? trim($field) : $field;
			$value = (is_string($value)) ? trim($value) : $value;
			$this->params[$field] = $value;
	    }
    }
*/
/*
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
*/
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

    public function validationDirectResponse(){
        return $this->validation;
    }

    public function getCustomerAddressId(){
        return $this->addressId;
    }

    public function getDirectResponse(){
        return $this->directResponse;
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