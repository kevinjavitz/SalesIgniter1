<?php
class OrderPaymentMoneyorder extends StandardPaymentModule {

	public function __construct(){
		global $order;
		/*
		 * Default title and description for modules that are not yet installed
		 */
		$this->setTitle('Check/Money Order');
		$this->setDescription('Check/Money Order');
		
		$this->init('moneyorder');

		if ($this->isEnabled() === true){
			$this->email_footer = sprintf(
				sysLanguage::get('MODULE_PAYMENT_MONEYORDER_TEXT_EMAIL_FOOTER'),
				$this->getConfigData('MODULE_PAYMENT_MONEYORDER_PAYTO'),
				sysConfig::get('STORE_NAME_ADDRESS')
			);
		}
	}

	public function sendPaymentRequest($requestData){
		return $this->onResponse(array(
			'orderID' => $requestData['orderID'],
			'amount'  => $requestData['amount'],
			'message' => sysLanguage::get('PAYMENT_MODULE_MO_AWAITING_PAYMENT'),
			'success' => /*2*/1
		));
	}
	
	public function processPayment($orderID = null, $amount = null){
		global $order;

		if(is_null($orderID) && is_null($amount)){
			return $this->sendPaymentRequest(array(
					'orderID' => $order->newOrder['orderID'],
					'amount'  => $order->info['total']
				));
		}else{
			return $this->sendPaymentRequest(array(
					'orderID' => $orderID,
					'amount'  => $amount
				));
		}
	}

	public function processPaymentCron($orderID){
		global $order;
		$order->info['payment_method'] = $this->getTitle();
		
		$this->processPayment();
		return true;
	}

	public function getCreatorRow($Editor, &$headerPaymentCols){
		$headerPaymentCols[] = '<td class="ui-widget-content ui-state-hover" align="left" style="border-top:none;border-left:none;">' . '&nbsp;' . '</td>';
		$headerPaymentCols[] = '<td class="ui-widget-content ui-state-hover" align="left" style="border-top:none;border-left:none;">' . '&nbsp;' . '</td>';

		$headerPaymentCols[] = '<td class="ui-widget-content ui-state-hover" align="left" style="border-top:none;border-left:none;">' . '<input type="text" class="ui-widget-content" name="payment_amount" size="10">' . '</td>';

		$headerPaymentCols[] = '<td class="ui-widget-content ui-state-hover" align="left" style="border-top:none;border-left:none;">'.($Editor->getOrderId() ? htmlBase::newElement('button')->addClass('paymentProcessButton')->setText('Process')->draw() : 'Will process on save').'</td>';

	}
		
	private function onResponse($logData){
		$this->onSuccess($logData);
		return true;
	}
		
	private function onSuccess($logData){
		$this->logPayment($logData);
	}
		
	private function onFail($info){
	}
}
?>