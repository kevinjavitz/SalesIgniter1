<?php
/*
	Sales Igniter E-Commerce Store Version 2

	I.T. Web Experts
	http://www.itwebexperts.com

	Copyright (c) 2011 I.T. Web Experts

	This script and it's source is not redistributable
*/

class PaymentModuleBase extends ModuleBase
{

	private $paymentZone = null;

	private $checkoutMethod = null;

	private $paymentError = null;

	private $orderStatus = 0;

	private $formUrl = null;

	private $errorMessage = null;

	public function init($code, $forceEnable = false, $moduleDir = false) {
		global $onePageCheckout;

		$this->import(new Installable);
		$this->import(new SortedDisplay);

		$this->setModuleType('orderPayment');
		parent::init($code, $forceEnable, $moduleDir);

		if ($this->configExists($this->getModuleInfo('zone_key'))){
			$this->paymentZone = (int)$this->getConfigData($this->getModuleInfo('zone_key'));
		}

		if ($this->configExists($this->getModuleInfo('checkout_method_key'))){
			$this->checkoutMethod = $this->getConfigData($this->getModuleInfo('checkout_method_key'));
		}

		if ($this->configExists($this->getModuleInfo('order_status_key'))){
			$this->orderStatus = (int)$this->getConfigData($this->getModuleInfo('order_status_key'));
		}

		if (isset($onePageCheckout) && is_object($onePageCheckout)){
			if ($this->isEnabled() === true && $this->checkoutMethod != 'All'){
				if ($onePageCheckout->isMembershipCheckout() === true && $this->checkoutMethod == 'Normal'){
					$this->setEnabled(false);
				}

				if ($onePageCheckout->isMembershipCheckout() === false && $this->checkoutMethod == 'Membership'){
					$this->setEnabled(false);
				}
			}
		}
	}

	public function getStatus() {
		return $this->isEnabled();
	}

	public function hasError() {
		return (is_null($this->paymentError) === false);
	}

	public function setFormUrl($val) {
		$this->formUrl = $val;
	}

	public function getFormUrl() {
		return $this->formUrl;
	}

	public function hasFormUrl() {
		return (is_null($this->formUrl) === false);
	}

	public function updateStatus() {
		global $order, $onePageCheckout;
		if (is_object($order) && $this->isEnabled() === true && $this->paymentZone > 0){
			$userAccount = &Session::getReference('userAccount');
			$billingAddress = $userAccount->plugins['addressBook']->getAddress('billing');

			$check_flag = false;
			$Qcheck = Doctrine_Query::create()
				->from('GeoZones g')
				->leftJoin('g.ZonesToGeoZones ztg')
				->where('g.geo_zone_id = ?', $this->paymentZone)
				->andWhere('ztg.zone_country_id = ?', $billingAddress['entry_country_id'])
				->orderBy('ztg.zone_id')
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if ($Qcheck){
				foreach($Qcheck as $zInfo){
					foreach($zInfo['ZonesToGeoZones'] as $iInfo){
						if ($iInfo['zone_id'] < 1){
							$check_flag = true;
							break;
						}
						elseif ($iInfo['zone_id'] == $billingAddress['entry_zone_id']) {
							$check_flag = true;
							break;
						}
					}
				}
			}

				if ($check_flag == false){
					$this->setEnabled(false);
				}
			}
		
			if (isset($onePageCheckout) && is_object($onePageCheckout)){
				if ($this->getStatus() === true && $this->checkoutMethod != 'Both'){
					if ($onePageCheckout->isMembershipCheckout() === true && $this->checkoutMethod == 'Normal'){
						$this->setEnabled(false);
					}

					if ($onePageCheckout->isMembershipCheckout() === false && $this->checkoutMethod == 'Membership'){
						$this->setEnabled(false);
					}
				}
			}
		}

	public function javascriptValidation() {
		return '';
	}

	public function onSelect() {
		return array(
			'id' => $this->getCode(),
			'module' => $this->getTitle()
		);
	}

	/*
			 * Process the response from the gateway
			 */
	private function onResponse($response) {
	}

	/*
			 * On successful response from the gateway
			 */
	private function onSuccess($info) {
	}

	/*
			 * On failure response from the gateway
			 */
	private function onFail($info) {
	}

	public function sendPaymentRequest($requestData) {
		return true;
	}

	public function processPayment($orderID = null, $amount = null) {
		return false;
	}

	public function refundPayment($requestData) {
		return false;
	}

	public function processPaymentCron($orderID) {
		return false;
	}

	public function afterOrderProcess() {
		return false;
	}

	public function afterOrderProcessCron() {
		return false;
	}

	public function validatePost() {
		return true;
	}

	public function hasHiddenFields() {
		return false;
	}

	public function getHiddenFields() {
		return '';
	}

	public function beforeRentalProcess() {
		return false;
	}

	public function afterRentalProcess() {
		return false;
	}

	public function logToCollection(&$CollectionObj) {
		$this->logUseCollection = true;
		$this->Collection = $CollectionObj;
	}

	public function getErrorMessage() {
		return $this->errorMessage;
	}

	public function setErrorMessage($val) {
		$this->errorMessage = $val;
	}

	public function logPayment($info) {
		global $order;

		$Order = Doctrine_Core::getTable('Orders')->findOneByOrdersId((isset($info['orderID']) ? $info['orderID'] : $order->newOrder['orderID']));
		if(is_object($Order)){
			$newHistory =& $Order->OrdersStatusHistory;
			$idx = $newHistory->count();
			$Order->OrdersStatusHistory[$idx]->orders_status_id = $this->orderStatus;
			$Order->orders_status = $this->orderStatus;
			$Order->save();
			$newStatus = new OrdersPaymentsHistory();
			$newStatus->orders_id = (isset($info['orderID']) ? $info['orderID'] : $order->newOrder['orderID']);
			$newStatus->payment_module = $this->getCode();
			$newStatus->payment_method = $this->getTitle();
			$newStatus->payment_amount = $info['amount'];
			$newStatus->success = (int)$info['success'];
			$newStatus->can_reuse = (int)(isset($info['can_reuse']) ? $info['can_reuse'] : 0);

			if (isset($info['message'])){
				$newStatus->gateway_message = $info['message'];
			}

			if (isset($info['cardDetails'])){
				$newStatus->card_details = cc_encrypt(serialize($info['cardDetails']));
			}

			if (isset($this->logUseCollection) && $this->logUseCollection === true){
				$this->Collection->OrdersPaymentsHistory->add($newStatus);
			}
			else {
				$newStatus->save();
			}
		}
	}

	public function ownsProcessPage(){
		return (isset($_GET['paymentModule']) && $_GET['paymentModule'] == $this->getCode() ? true : false);
	}
}

?>