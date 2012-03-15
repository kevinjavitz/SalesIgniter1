<?php
/*
	Rental Store Version 2

	I.T. Web Experts
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class OrderPaymentValitor extends CreditCardModule {

	var $gatewayUrl;
	var $terminalUsername;
	var $terminalPassword;
	var $terminalPOSTermID;

	public function __construct(){
		/*
		 * Default title and description for modules that are not yet installed
		 */
		$this->setTitle('Credit Card Via Valitor');
		$this->setDescription('Credit Card Via Valitor');

		$this->init('valitor');
		

		//A list of allowed credit card types
		$this->allowedTypes = array();
		$cc_array = explode(',', $this->getConfigData('MODULE_PAYMENT_VALITOR_ACCEPTED_CC'));
		foreach($cc_array as $k => $v){
			$this->allowedTypes[trim($v)] = $this->cardTypes[trim($v)];
		}
		
		//Gateway details			   
		$this->gatewayUrl = $this->getConfigData('MODULE_PAYMENT_VALITOR_GATEWAY_URL');
		$this->terminalUsername = $this->getConfigData('MODULE_PAYMENT_VALITOR_USERNAME');
		$this->terminalPassword = $this->getConfigData('MODULE_PAYMENT_VALITOR_PASSWORD');
		$this->terminalPOSTermID = $this->getConfigData('MODULE_PAYMENT_VALITOR_POSTermID');
		
		//For testing:

		/*$this->gatewayUrl = 'https://testgreidslugatt.valitor.is/greidslugatt.asmx/FramkvaemaAdgerd';
		$this->terminalUsername = 'visatest'; //username
		$this->terminalPassword = 'visatest123'; //password
		$this->terminalPOSTermID = 5; //POSTermID
		*/
		
		//Always require verification code
		$this->requireCvv = true;
                
                $this->removeOrderOnFail = true;
	}


	//On payment method selected on checkout
	public function onSelect(){
		global $onePageCheckout;
		$fieldsArray = array();
		$paymentCards = array();
		$paymentProfiles = array();


		$fieldsArray[] = array(
			'title' => sysLanguage::get('MODULE_PAYMENT_VALITOR_TEXT_CREDIT_CARD_TYPE'),
			'field' => $this->getCreditCardTypeField()
		);	

		$fieldsArray[] = array(
			'title' => sysLanguage::get('MODULE_PAYMENT_VALITOR_TEXT_CREDIT_CARD_NUMBER'),
			'field' => $this->getCreditCardNumber()
		);

		$fieldsArray[] = array(
			'title' => sysLanguage::get('MODULE_PAYMENT_VALITOR_TEXT_CREDIT_CARD_EXPIRES'),
			'field' => $this->getCreditCardExpMonthField() . '&nbsp;' . $this->getCreditCardExpYearField()
		);

		if ($this->requireCvv === true){
			$fieldsArray[] = array(
				'title' => 'CVV number ' . ' ' .'<a href="#" onclick="popupWindow(\'' . itw_app_link('rType=ajax&appExt=infoPages&dialog=true', 'show_page', 'cvv_help') . '\', 400, 300);return false">' . '<u><i>' . '(' . sysLanguage::get('MODULE_PAYMENT_VALITOR_TEXT_CVV_LINK') . ')' . '</i></u></a>',
				'field' => $this->getCreditCardCvvField()
			);
		}


		$return = parent::onSelect();
		$return['module'] .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $this->getCardImages();
		$return['fields'] = $fieldsArray;

		return $return;
	}		
	
	
	//Validate submitted card details
	public function validatePost(){
            global $messageStack, $onePageCheckout;
            
            if (empty($_POST['cardNumber'])) {
                $error = sysLanguage::get('TEXT_CCVAL_ERROR_INVALID_NUMBER');
            } elseif($this->requireCvv && empty($_POST['cardCvvNumber'])) {
                $error = sysLanguage::get('TEXT_CCVAL_ERROR_CVV_LENGTH');
            } else {
                $error = null;
            }                       
            

            //Repopulate data
            $onePageCheckout->onePage['info']['payment']['cardDetails'] = array(
                    'cardNumber'   => $_POST['cardNumber'],
                    'cardExpMonth' => $_POST['cardExpMonth'],
                    'cardExpYear'  => $_POST['cardExpYear'],
                    'cardType'     => $_POST['cardType']
            );
            
            if ($this->requireCvv) {
                $onePageCheckout->onePage['info']['payment']['cardDetails']['cardCvvNumber'] = $_POST['cardCvvNumber'];
            }
            
            
            if ($error) {
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
                        'errorMsg'    => $error
                );             
            } else {
                return true;
            }

	}
	
	
	public function processPayment($orderID = null, $amount = null){
		global $order, $onePageCheckout;

		$paymentAmount = $order->info['total'];

		$paymentInfo = OrderPaymentModules::getPaymentInfo();
		
		$cardInfo = $paymentInfo['cardDetails']['cardNumber'].'-'.substr($paymentInfo['cardDetails']['cardExpYear'],0,2).$paymentInfo['cardDetails']['cardExpMonth'];
		$cardLast4Digits = substr($paymentInfo['cardDetails']['cardNumber'], -4, 4);

		return $this->sendPaymentRequest(array(
		    'orderID' => $order->newOrder['orderID'],
		    'Notandanafn' => $this->terminalUsername, //username
		    'Lykilord' => $this->terminalPassword, //password
		    'PosiId' => $this->terminalPOSTermID, //POSTermID
		    'Adgerd' => 'NETGREIDSLA', //operation = net payment
		    'Kortaupplysingar' => $cardInfo, //Credit card number + expiration date
		    'SidustuFjorirIKortnumeri' => $cardLast4Digits,
		    'Oryggisnumer' => $paymentInfo['cardDetails']['cardCvvNumber'], //Verification number (CVV)
		    'Chipgogn' => null, //Chipdata
		    'Faerslunumer' => null, //Transaction number
		    'Upphaed' => $paymentAmount, //Amount
		    'Stillingar' => 'Gjaldmidill:352' //settings (currency=ISL)
		));		
		
	}

	public function sendPaymentRequest($requestParams){
	    $CurlRequest = new CurlRequest($this->gatewayUrl);
	    $CurlRequest->setData($requestParams);
	    $CurlResponse = $CurlRequest->execute();

	    return $this->onResponse($CurlResponse);
	}
	
	private function onResponse($CurlResponse, $isCron = false){
                
		$response = $CurlResponse->getResponse();
		$xml = @simplexml_load_string($response);

		$error_number = (string)$xml->Villunumer;
		if (empty($error_number)) {
		    //Success
		    
		    $this->transactionId = (string)$xml->Faerslunumer;
                    $this->authId = (string)$xml->Kvittun->Heimildarnumer;
                    if (!empty($xml->Kvittun->TegundKorts)) {
                        $this->resultCardType = (string)$xml->Kvittun->TegundKorts;
                    }
		    
		    $this->onSuccess(array(
			    'curlResponse'=> $CurlResponse,
			    'message'	     => 'Success'
		    ));		    
                            
		    return true;
		} else {
		    //some error occured
		    $errorMsg = 'Error #'.$xml->Villunumer.': '.$xml->Villuskilabod;
		    
		    $this->onFail(array(
			    'curlResponse'=> $CurlResponse,
			    'message'	     => $errorMsg
		    ));
		    
		    
		    return false;
		}
	}	
	
	private function _getResponseDetails($curResponse)
	{
            $RequestData = $curResponse->getDataRaw();
            
	    $orderId = $RequestData['orderID'];

	    $card_info = explode('-', $RequestData['Kortaupplysingar']);//9999999999999999-1411

	    $cardDetails = array(
		    'cardOwner'    => null,
		    'cardNumber'   => $card_info[0], //card number
		    'cardExpYear'  => substr($card_info[1], 0, 2),
		    'cardExpMonth' => substr($card_info[1], 2, 2),
		    'cardCvvNumber' => $RequestData['Oryggisnumer'],
		    'transId'      => (isset($this->transactionId)?$this->transactionId:''),
                    'authId'      => (isset($this->authId)?$this->authId:''),                
                    'cardType'     => $this->resultCardType
	    );

	    return array(
		    'orderID' => $orderId,
		    'amount'  => $RequestData['Upphaed'],
		    'message' => $RequestData['message'],
		    'can_reuse' => (isset($_POST['canReuse'])?1:0),
		    'cardDetails' => $cardDetails
	    );
	}
	
	private function onSuccess($info){	    
	    $details = $this->_getResponseDetails($info['curlResponse']);
	    $details['success'] = 1;
	    $this->logPayment($details);
            
        return $details;
	}
	
	private function onFail($info){
		global $messageStack;
				
		$this->setErrorMessage($this->getTitle() . ' : ' . $info['message']);
		if ($this->removeOrderOnFail === true){
            $RequestData = $info['curlResponse']->getDataRaw();
            $orderId = $RequestData['orderID'];
                
			$Order = Doctrine_Core::getTable('Orders')->find($orderId);
			if ($Order){
				$Order->delete();
			}

			$messageStack->addSession('pageStack', $info['message'], 'error');
		}else{
		    $details = $this->_getResponseDetails($info['curlResponse']);
		    $details['success'] = 0;
		    $this->logPayment($details);
		    $messageStack->addSession('pageStack', $info['message'], 'error');
		}
                
         return $details;
	}	
}