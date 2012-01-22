<?php
/*
	Rental Store Version 2

	I.T. Web Experts
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class OrderPaymentCustomergrouppayment extends StandardPaymentModule
{

	public function __construct() {
		global $order, $currencies;
		/*
		 * Default title and description for modules that are not yet installed
		 */
		$this->setTitle('Customer Group Payment');
		$this->setDescription('Customer Group Payment');

		$this->init('customergrouppayment');
		if ($this->isEnabled() === true){
			$userAccount = OrderPaymentModules::getUserAccount();
			if (isset($userAccount)){
				$QCustomersGroups = Doctrine_Query::create()
					->from('CustomerGroups c')
					->leftJoin('c.CustomersToCustomerGroups cg')
					->where('cg.customers_id=?', $userAccount->getCustomerId())
					->orderBy('customer_groups_credit')
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

				$isCredit = false;
				foreach($QCustomersGroups as $iGroup){
					if ($iGroup['customer_groups_credit'] >= $order->info['total']){
						$this->setTitle($this->getTitle() . ' (' . sysLanguage::get('TEXT_CREDIT_LEFT') . ' ' . $currencies->format($iGroup['customer_groups_credit']) . ')');
						$isCredit = true;
						break;
					}
				}
				if (!$isCredit){
					$this->setTitle($this->getTitle() . ' (' . sysLanguage::get('TEXT_NOT_IN_ANY_GROUPS') . ')');
				}
			}
		}
	}

	public function sendPaymentRequest($requestData) {
		global $order;
		$userAccount = OrderPaymentModules::getUserAccount();
		//get customer group credit if is enough that success1 and on success add payment log and remove order total from customer group credit
		$QCustomersGroups = Doctrine_Query::create()
			->from('CustomerGroups c')
			->leftJoin('c.CustomersToCustomerGroups cg')
			->where('cg.customers_id=?', $userAccount->getCustomerId())
			->orderBy('customer_groups_credit')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		$success = 0;
		foreach($QCustomersGroups as $iGroup){
			if ($iGroup['customer_groups_credit'] >= $order->info['total']){
				$success = 1;
			}
		}

		return $this->onResponse(array(
				'orderID' => $requestData['orderID'],
				'amount' => $order->info['total'],
				'success' => $success
			));
	}

	public function processPayment($orderID = null, $amount = null){
		global $order;
	    $this->removeOrderOnFail = true;
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

	public function processPaymentCron($orderID) {
		global $order;
		$order->info['payment_method'] = $this->getTitle();

		$this->processPayment();
		return true;
	}

	private function onResponse($logData) {
		if ($logData['success'] == 1){
			$this->onSuccess($logData);
		}
		else {
			$this->onFail($logData);
		}
		return true;
	}

	private function onSuccess($logData) {
		$userAccount = OrderPaymentModules::getUserAccount();

		$QCustomersGroups = Doctrine_Query::create()
			->from('CustomerGroups c')
			->leftJoin('c.CustomersToCustomerGroups cg')
			->where('cg.customers_id=?', $userAccount->getCustomerId())
			->orderBy('customer_groups_credit')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		$isSaved = false;
		foreach($QCustomersGroups as $iGroup){
			if ($iGroup['customer_groups_credit'] >= $logData['amount']){
				$CustomerGroups = Doctrine_Core::getTable('CustomerGroups');
				$CustomerGroups = $CustomerGroups->find($iGroup['customer_groups_id']);
				$CustomerGroups->customer_groups_credit = $CustomerGroups->customer_groups_credit - $logData['amount'];
				$CustomerGroups->save();
				$isSaved = true;
				break;
			}
		}
		if ($isSaved){
			$this->logPayment(array(
					'orderID' => $logData['orderID'],
					'amount' => $logData['amount'],
					'message' => 'Payment Success',
					'success' => 1,
					'cardDetails' => array(
						'cardOwner' => '',
						'cardNumber' => '',
						'cardExpMonth' => '',
						'cardExpYear' => ''
					)
				));
		}
		else {
			$this->onFail($logData);
		}
	}

	private function onFail($info) {
		global $messageStack;
		if ($this->removeOrderOnFail === true){
			$Order = Doctrine_Core::getTable('Orders')->find($info['orderID']);
			if ($Order){
				$Order->delete();
			}
		}
		$this->setErrorMessage($this->getTitle() . ' : ' . sysLanguage::get('TEXT_GROUP_NOT_CREDIT'));
		$messageStack->addSession('pageStack', sysLanguage::get('TEXT_GROUP_NOT_CREDIT'), 'error');
	}
}

?>