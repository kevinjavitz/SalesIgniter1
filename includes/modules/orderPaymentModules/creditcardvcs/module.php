<?php
/**
 * Order payment module for Virtual Card Services
@section gateway_doc Gateway Documentation
<a href="http://www.vcs.co.za/interfaces/Documents/VirtualOnline/H2H_xml.pdf" target="_blank">View Documentation</a>
 */

class OrderPaymentCreditcardvcs extends CreditCardModule
{

	public function __construct() {
		$this->setTitle('Virtual Card Services Credit Card');
		$this->setDescription('Virtual Card Services Credit Card');

		$this->init('creditcardvcs');

		if ($this->isEnabled() === true){
			$this->gatewayUrl = 'https://www.vcs.co.za/vvonline/ccxmlauth.asp';
			$this->requireCvv = true;
			$this->isCron = false;
		}
	}

	/**
	 * Called from the checkout to determine if there are any entry fields that need to be displayed
	 * @return array
	 */
	public function onSelect() {
		$return = parent::onSelect();

		$return['fields'] = array(
			array(
				'title' => sysLanguage::get('MODULE_PAYMENT_CREDITCARDVCS_TEXT_CREDIT_CARD_OWNER'),
				'field' => $this->getCreditCardOwnerField()
			),
			array(
				'title' => sysLanguage::get('MODULE_PAYMENT_CREDITCARDVCS_TEXT_CREDIT_CARD_NUMBER'),
				'field' => $this->getCreditCardNumber()
			),
			array(
				'title' => sysLanguage::get('MODULE_PAYMENT_CREDITCARDVCS_TEXT_CREDIT_CARD_EXPIRES'),
				'field' => $this->getCreditCardExpMonthField() . '&nbsp;' . $this->getCreditCardExpYearField()
			),
			array(
				'title' => sysLanguage::get('MODULE_PAYMENT_CREDITCARDVCS_TEXT_CREDIT_CARD_CVV'),
				'field' => $this->getCreditCardCvvField()
			)
		);

		return $return;
	}

	/**
	 * Called from the checkout to send any requests required
	 * @return bool
	 */
	public function processPayment() {
		global $order, $onePageCheckout;
		$paymentInfo = OrderPaymentModules::getPaymentInfo();

		$userAccount = OrderPaymentModules::getUserAccount();
		$addressBook = $userAccount->plugins['addressBook'];

		$billingAddress = $addressBook->getAddress('billing');

		$dataArray = array(
			'AuthorisationRequest' => array(
				'UserId' => $this->getConfigData('MODULE_PAYMENT_CC_VCS_LOGIN'),
				'Reference' => $order->newOrder['orderID'] . '-' . date('YmdHis'),
				'Description' => sysConfig::get('STORE_NAME') . ' Order #' . $order->newOrder['orderID'],
				'Amount' => $order->info['total'],
				'CardholderName' => $billingAddress['entry_firstname'] . ' ' . $billingAddress['entry_lastname'],
				'CardNumber' => trim(str_replace(' ', '', $paymentInfo['cardDetails']['cardNumber'])),
				'ExpiryMonth' => $paymentInfo['cardDetails']['cardExpMonth'],
				'ExpiryYear' => substr($paymentInfo['cardDetails']['cardExpYear'], 2),
				'CardValidationCode' => $paymentInfo['cardDetails']['cardCvvNumber'],
				'CardPresent' => 'N'
			)
		);

		$CurlRequest = new CurlRequest($this->gatewayUrl);
		$CurlRequest->setData($dataArray, 'xml', 'xmlmessage=');
		$CurlResponse = $CurlRequest->execute();

		return $this->onResponse($CurlResponse);
	}

	/**
	 * Called from the membership_update.php, which is run via a cron job, to send any requests required
	 * @param $orderID The order id currently being processed by the cron script
	 * @return bool
	 */
	public function processPaymentCron($orderID) {
		$this->isCron = true;
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

		$cardExpDate = cc_decrypt($Qorder[0]['CustomersMembership']['exp_date']);

		$dataArray = array(
			'AuthorisationRequest' => array(
				'UserId' => $this->getConfigData('MODULE_PAYMENT_CC_VCS_LOGIN'),
				'Reference' => $orderID . '-' . date('YmdHis'),
				'Description' => sysConfig::get('STORE_NAME') . ' Order #' . $orderID,
				'Amount' => $Qorder[0]['OrdersTotal'][0]['value'],
				'CardholderName' => $Qorder[0]['OrdersAddresses'][0]['entry_name'],
				'CardNumber' => cc_decrypt($Qorder[0]['Customers']['CustomersMembership']['card_num']),
				'ExpiryMonth' => substr($cardExpDate, 0, 2),
				'ExpiryYear' => substr($cardExpDate, 2),
				//'CardValidationCode' => $paymentInfo['cardDetails']['cardCvvNumber'],
				'CardPresent' => 'N'
			)
		);

		$CurlRequest = new CurlRequest($this->gatewayUrl);
		$CurlRequest->setData($dataArray, 'xml', 'xmlmessage=');
		$CurlResponse = $CurlRequest->execute();

		$this->onResponse($CurlResponse);
		return true;
	}

	private function onResponse($CurlResponse) {
		$response = $CurlResponse->getResponse();

		$success = true;
		$errMsg = 'Payment Successful';
		if (stristr($response, 'approved') === false){
			$success = false;
			if ($response !== false){
				$errMsg = 'Payment Error: ' . $response;
			}
			else {
				$errMsg = 'There was an unknown error with your payment, please try again.';
			}
		}

		if ($success === true){
			$this->onSuccess(array(
					'curlResponse' => $CurlResponse,
					'message' => $errMsg
				));
		}
		else {
			$this->onFail(array(
					'curlResponse' => $CurlResponse,
					'message' => $errMsg
				));
		}
		return $success;
	}

	private function onSuccess($info) {
		$RequestData = $info['curlResponse']->getDataRaw();
		$orderId = substr($RequestData['Reference'], 0, strpos($RequestData['Reference'], '-') - 1);
		$this->logPayment(array(
				'orderID' => $orderId,
				'amount' => $RequestData['Amount'],
				'message' => $info['message'],
				'success' => 1,
				'CardDetails' => array(
					'cardOwner' => $RequestData['CardholderName'],
					'cardNumber' => $RequestData['CardNumber'],
					'cardExpMonth' => $RequestData['ExpiryMonth'],
					'cardExpYear' => $RequestData['ExpiryYear']
				)
			));
	}

	private function onFail($info) {
		global $messageStack, $order;
		if ($this->isCron === false){
			$RequestData = $info['curlResponse']->getDataRaw();
			$orderId = substr($RequestData['Reference'], 0, strpos($RequestData['Reference'], '-') - 1);
			$Order = Doctrine_Core::getTable('Orders')->find($orderId);
			if ($Order){
				$Order->delete();
			}

			$messageStack->addSession('pageStack', $info['message'], 'error');
			tep_redirect(itw_app_link('payment_error=1', 'checkout', 'default', 'SSL'));
		}
	}
}

?>