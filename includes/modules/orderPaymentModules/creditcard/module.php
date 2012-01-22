<?php
/*
	Rental Store Version 2

	I.T. Web Experts
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class OrderPaymentCreditcard extends CreditCardModule
{

	public function __construct() {
		/*
					 * Default title and description for modules that are not yet installed
					 */
		$this->setTitle('Credit Card');
		$this->setDescription('Credit Card');

		$this->init('creditcard');

		if ($this->isEnabled() === true){
			$this->requireCvv = ($this->getConfigData('MODULE_PAYMENT_CREDITCARD_CVV_ENABLED') == 'True');
		}
	}

	public function onSelect() {
		$return = parent::onSelect();

		$return['fields'] = array(
			array(
				'title' => sysLanguage::get('MODULE_PAYMENT_CREDITCARD_TEXT_CREDIT_CARD_OWNER'),
				'field' => $this->getCreditCardOwnerField()
			),
			array(
				'title' => sysLanguage::get('MODULE_PAYMENT_CREDITCARD_TEXT_CREDIT_CARD_NUMBER'),
				'field' => $this->getCreditCardNumber()
			),
			array(
				'title' => sysLanguage::get('MODULE_PAYMENT_CREDITCARD_TEXT_CREDIT_CARD_EXPIRES'),
				'field' => $this->getCreditCardExpMonthField() . '&nbsp;' . $this->getCreditCardExpYearField()
			)
		);

		if ($this->requireCvv === true){
			$return['fields'][] = array(
				'title' => sysLanguage::get('MODULE_PAYMENT_CREDITCARD_TEXT_CREDIT_CARD_CVV'),
				'field' => $this->getCreditCardCvvField()
			);
		}

		return $return;
	}

		public function processPayment($orderID = null, $amount = null){
		global $order, $onePageCheckout, $userAccount;
		$paymentInfo = OrderPaymentModules::getPaymentInfo();

		return $this->onResponse(array(
				'orderID' => $order->newOrder['orderID'],
				'amount' => $order->info['total'],
				'message' => 'Payment Successful',
				'success' => 1,
				'cardDetails' => array(
					'cardOwner' => $paymentInfo['cardDetails']['cardOwner'],
					'cardNumber' => $paymentInfo['cardDetails']['cardNumber'],
					'cardExpMonth' => $paymentInfo['cardDetails']['cardExpMonth'],
					'cardExpYear' => $paymentInfo['cardDetails']['cardExpYear'],
					'cardCvvNumber' => (!empty($paymentInfo['cardDetails']['cardCvv']) ? $paymentInfo['cardDetails']['cardCvv'] : '')
				)
			));
	}

	public function processPaymentCron($orderID) {
		$Qorder = Doctrine_Query::create()
			->from('Orders o')
			->leftJoin('o.OrdersAddresses oa')
			->leftJoin('o.OrdersTotal ot')
			->leftJoin('o.CustomersMembership m ON (customers_id)')
			->where('o.orders_id = ?', $orderID)
			->andWhere('oa.address_type = ?', 'billing')
			->andWhereIn('ot.module_type', array('total', 'ot_total'))
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		$cardExpDate = cc_decrypt($Qorder[0]['CustomersMembership']['exp_date']);
		$this->cronMsg = 'Payment Successful';
		$this->onSuccess(array(
				'orderID' => $orderID,
				'amount' => $Qorder[0]['OrdersTotal'][0]['value'],
				'message' => 'Payment Successful',
				'success' => 1,
				'CardDetails' => array(
					'cardOwner' => $Qorder[0]['OrdersAddresses'][0]['entry_name'],
					'cardNumber' => cc_decrypt($Qorder[0]['CustomersMembership']['card_num']),
					'cardExpMonth' => substr($cardExpDate, 0, 2),
					'cardExpYear' => substr($cardExpDate, 2),
					'cardCvvNumber' => (tep_not_null($Qorder[0]['CustomersMembership']['card_cvv']) ? cc_decrypt($Qorder[0]['CustomersMembership']['card_cvv']) : '')
				)
			));
		return true;
	}

	private function onResponse($logData) {
		$this->onSuccess($logData);
		return true;
	}

	private function onSuccess($logData) {
		$this->logPayment($logData);
	}

	private function onFail($info) {
	}
}

?>