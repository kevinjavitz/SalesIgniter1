<?php
class membershipUpdate_cron {
	public function __construct(){
		$this->adminEmailContent = array();
		$this->testMode = false;
		$this->paymentDeclinedStatusID = 5;
		$this->paymentSuccessStatusID = 3;
		$this->retryMaxTimes = sysConfig::get('MAX_RECURRING_BILLING_DAYS');
		$this->billingAction = false;
	}

	public function &getUserAccount(){
		global $userAccount;
		return $userAccount;
	}

	public function parseDate($date){
		$dateArr = date_parse($date);
		return $dateArr;
	}

	public function timeToBill($date){
		$dateArr = $this->parseDate($date);
		return (date('Y-m-d', mktime(0,0,0,$dateArr['month'],$dateArr['day'],$dateArr['year'])) == date('Y-m-d'))  /*((date('Y-m-d', mktime(0,0,0,$dateArr['month'],$dateArr['day'],$dateArr['year'])) >= date('Y-m-d', mktime(0,0,0,10,19,2011))) && (date('Y-m-d', mktime(0,0,0,$dateArr['month'],$dateArr['day'],$dateArr['year'])) <= date('Y-m-d', mktime(0,0,0,10,24,2011))))*/;
	}

	public function getBillingAttempts($oID){
		$Qcheck = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc('select bill_attempts from orders where orders_id = "' . $oID .'"');
		return $Qcheck[0]['bill_attempts'];
	}

	public function updateBillingAttempts($oID){
		Doctrine_Query::create()
			->update('Orders')
			->set('bill_attempts', '?', ($this->getBillingAttempts($oID)+1))
			->where('orders_id = ?', $oID)
			->execute();
	}

	public function needsRetry($cID){
		$Qcheck = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc('select * from membership_billing_report where customers_id = "' . $cID .'" order by billing_report_id desc limit 1');

		if (count($Qcheck) > 0){
			$LastReport = $Qcheck[0];
			$this->orderId = $LastReport['orders_id'];
			if ($LastReport['status'] == 'D'){
				$dateAdded = $this->parseDate($LastReport['date']);
				$timeAdded = mktime(0,0,0,$dateAdded['month'],$dateAdded['day'],$dateAdded['year']);
				$timeNow = time();
				$daysDiff = ($timeNow - $timeAdded) / (60*60*24);
				if ($daysDiff <= $this->retryMaxTimes){
					if ($this->getBillingAttempts($this->orderId) < $this->retryMaxTimes){
						return true;
					}
				}
			}
		}
		return false;
	}

	public function setAction($action){
		$this->billingAction = $action;
	}

	public function isRetry(){
		return ($this->billingAction == 'retry');
	}

	public function isFromTrial(){
		return ($this->billingAction == 'trial');
	}

	public function isRecurring(){
		return ($this->userAccount->plugins['membership']->isRecurringPlan());
	}

	public function setCurrentCustomer(&$userAccount){
		$this->userAccount =& $userAccount;

		if (!$this->isRetry()){
			$this->orderID = false;
		}
	}

	public function removeCustomerPlanUpdate(){
		Doctrine_Query::create()
		->delete('MembershipUpdate')
		->where('customers_id = ?', $this->userAccount->getCustomerId())
		->execute();
	}

	public function setPlan($planId){
		$this->userAccount->plugins['membership']->setCustomerPlan($planId);
		$this->removeCustomerPlanUpdate();
	}

	public function cancelMembership(){
		$this->userAccount->plugins['membership']->cancelCustomerMembership();
	}

	public function concludeFreeTrial(){
		$this->userAccount->plugins['membership']->updateTrialStatus('N');
	}

	public function updateActivationStatus($status){
		$this->userAccount->plugins['membership']->updateActivationStatus($status);
	}

	public function disableAccount(){
		$this->userAccount->plugins['membership']->updateActivationStatus('N');
	}

	public function enableAccount(){
		$this->userAccount->plugins['membership']->updateActivationStatus('Y');
	}

	public function isCanceled(){
		return $this->userAccount->plugins['membership']->planIsCancelled();
	}

	public function isMember(){
		return $this->userAccount->plugins['membership']->isRentalMember();
	}

	public function canStream(){
		return $this->userAccount->plugins['membership']->isAllowedStreaming();
	}

	public function paymentMethod(){
		return $this->userAccount->plugins['membership']->getPaymentMethod();
	}

	public function setPaymentObj($obj){
		$this->paymentObj = $obj;
	}

	public function isActivated(){
		return $this->userAccount->plugins['membership']->isActivated();
	}

	public function updateStreamingAccess(){
		if ($this->canStream() && $this->isCanceled() && $this->isMember()){
			$membership =& $this->userAccount->plugins['membership'];
			$streamingEnds = $membership->getStreamingEndDate(true);
			$now = mktime(0,0,0,date('m'),date('d'),date('Y'));
			if ($streamingEnds < $now){
				$streamViewPeriod = $membership->getStreamingViewPeriod();
				if ($streamViewPeriod == 'T'){
					$streamViewTime = $membership->getStreamingViewTime();
					$streamViewTimePeriod = $membership->getStreamingViewTimePeriod();
					if ($streamViewTimePeriod == 'D'){
						$period = 'day';
					}elseif ($streamViewTimePeriod == 'W'){
						$period = 'week';
					}elseif ($streamViewTimePeriod == 'M'){
						$period = 'month';
					}
					$endTime = strtotime('+' . (int)$streamViewTime . ' ' . $period, $now);
				}else{
					$membershipMonths = $membership->getMembershipMonths();
					$membershipDays = $membership->getMembershipDays();
					$endTime = strtotime('+' . (int)$membershipMonths . ' month ' . (int)$membershipDays . ' day', $now);
				}
				$newStart = date('Y-m-d', $now);
				$newEnd = date('Y-m-d', $endTime);
				$membership->updateStreamingAccessDates($newStart, $newEnd);
			}
		}
	}

	public function updateCustomersNextBillDate(){
		$membership =& $this->userAccount->plugins['membership'];
		$membershipMonths = $membership->getMembershipMonths();
		$membershipDays = $membership->getMembershipDays();
		$nextTime = strtotime('+' . (int)$membershipMonths . ' month ' . (int)$membershipDays . ' day');
		$nextBillDate = date('Y-m-d', $nextTime);

		$membership->updateMembershipBillDate($nextBillDate);
		$this->removeCustomerPlanUpdate();
		$this->addAdminEmailContent();
	}

	public function addAdminEmailContent(){
		$membership =& $this->userAccount->plugins['membership'];
		$membershipMonths = $membership->getMembershipMonths();
		$membershipDays = $membership->getMembershipDays();
		$currentNextBill = $membership->getNextBillDate();
		$currentPlanName = $membership->getPlanName();
		$currentPlanPrice = $membership->getPlanPrice();
		$currentPlanTaxRate = $membership->getPlanTaxRate();

		$timeNextBill = strtotime('+' . (int)$membershipMonths . ' month ' . (int)$membershipDays . ' day');
		$dateArr = $this->parseDate($currentNextBill);
		$timeBilled = mktime(0,0,0,$dateArr['month'],$dateArr['day'],$dateArr['year']);

		$this->adminEmailContent[] = array(
			$this->userAccount->getFullName(),
			$currentPlanName,
			$currentPlanPrice,
			tep_add_tax($currentPlanPrice, $currentPlanTaxRate),
			date('m-d-Y'),
			date('m-d-Y', $timeNextBill)
		);
	}

	public function sendAdminEmail(){
		if(sizeof($this->adminEmailContent) > 0){
			$subject = 'Billing Notification';
			$body = 'Dear Admin,' . "\n" .
			'The following members should be billed today. Their billing info on the web site has been automatically renewed.' . "\n" .
			'If you need to process their payment through your payment gateway please do so.' . "\n" .
			'Member /  Plan  /  Plan Amount  /  Plan Amount With Tax  /  Date Billed  /  Next Bill Date' . "\n" .
			'------------------------------------------------------------------------------------------' . "\n";

			foreach($this->adminEmailContent as $i => $lineItems){
				$body .= implode(' / ', $lineItems) . "\n";
			}

			mail(
				sysConfig::get('STORE_OWNER_EMAIL_ADDRESS'),
				$subject,
				$body,
				"From:" . sysConfig::get('EMAIL_FROM')
			);
		}
		//mail(/*STORE_OWNER_EMAIL_ADDRESS*/'sw45859@centurytel.net',$subject,$body,"From:".EMAIL_FROM);
	}

	public function processPayment($orderId){
		$billingArray = array(
			'order_id' => $orderId
		);
		if ($this->paymentObj->processPaymentCron($orderId) === true){
			$billingArray['status'] = 'A';
			$billingArray['error'] = $this->paymentObj->getTitle() . ' ( ' . $this->paymentObj->cronMsg . ' )';

			if ($this->userAccount->plugins['membership']->isActivated() === false){
				$this->enableAccount();
			}

			$return = true;
		}else{
			$billingArray['status'] = 'D';
			$billingArray['error'] = $this->paymentObj->getTitle() . ' ( ' . $this->paymentObj->cronMsg . ' )';

			$this->disableAccount();

			$return = false;

			$historyArray['customer_notified'] = 0;
			if ($this->getBillingAttempts($billingArray['order_id']) < $this->retryMaxTimes){
				$historyArray['customer_notified'] = 1;

				$emailEvent = new emailEvent('membership_renewal_failed', $this->userAccount->getLanguageId());
				$emailEvent->setVars(array(
					'customerFullName' => $this->userAccount->getFullName(),
					'declineReason' => $this->paymentObj->cronMsg
				));
				$emailEvent->sendEmail(array(
					'email' => $this->userAccount->getEmailAddress(),
					'name'  => $this->userAccount->getFullName()
				));
			}
		}

		if ($this->isRetry()){
			$billingArray['error'] = 'RETRY:: ' . $billingArray['error'];
		}

		$this->addBillingReport($billingArray);
		return $return;
	}

	public function insertOrder(){
		global $currencies;
		$order = new OrderProcessor;
		if ($this->isRetry()){
			$this->updateBillingAttempts($this->orderId);
			return $this->orderId;
		}
		$OrderTotalModules = new OrderTotalModules;

		$addressBook =& $this->userAccount->plugins['addressBook'];
		$membership =& $this->userAccount->plugins['membership'];
		$rentalAddress = $addressBook->getAddress($membership->getRentalAddressId());

		$addressBook->addAddressEntry('customer', $rentalAddress);
		$addressBook->addAddressEntry('delivery', $rentalAddress);
		$addressBook->addAddressEntry('billing', $rentalAddress);
		$addressBook->addAddressEntry('pickup', $rentalAddress);

		$order->info['payment'] = array(
			'id'    => $this->paymentObj->getCode(),
			'title' => $this->paymentObj->getTitle()
		);
		$order->info['is_rental'] = '1';
		$order->info['bill_attempts'] = '1';

		$order->createOrder();

		if (sysConfig::get('CRON_BILL_METHOD') == 'current'){
			$planPrice = $membership->getPlanPrice();
			$planName = $membership->getPlanName();
			$taxRate = $membership->getPlanTaxRate($rentalAddress['entry_country_id'], $rentalAddress['entry_zone_id']);
		}else{
			$planPrice = $membership->getMembershipPrice();
			$planName = $membership->getMembershipName();
			$taxRate = $membership->getMembershipTaxRate($rentalAddress['entry_country_id'], $rentalAddress['entry_zone_id']);
		}

		$productsOrdered = '';
		$order->insertMembershipProduct(array(
			'id'            => 0,
			'model'         => 'membershipRenewal',
			'name'          => 'Membership Renewal ( ' . $planName . ' )',
			'price'         => $planPrice,
			'final_price'   => $planPrice,
			'tax'           => $taxRate,
			'quantity'      => 1,
			'purchase_type' => 'membership'
		), $productsOrdered);

		$TotalModule = $OrderTotalModules->getModule('total');
		$SubTotalModule = $OrderTotalModules->getModule('subtotal');
		if(is_object($SubTotalModule)){
			$order->newOrder['orderTotals'][] = array(
				'module'     => $SubTotalModule->getCode(),
				'method'     => null,
				'title'      => $SubTotalModule->getTitle() . ':',
				'text'       => $currencies->format(tep_add_tax($planPrice, $taxRate)),
				'value'      => tep_add_tax($planPrice, $taxRate),
				'code'       => $SubTotalModule->getCode(),
				'sort_order' => 1
			);
		}

		$order->newOrder['orderTotals'][] = array(
				'module'     => $TotalModule->getCode(),
				'method'     => null,
				'title'      => $TotalModule->getTitle() . ':',
				'text'       => '<b>' . $currencies->format(tep_add_tax($planPrice, $taxRate)) . '</b>',
				'value'      => tep_add_tax($planPrice, $taxRate),
				'code'       => $TotalModule->getCode(),
				'sort_order' => 2
		);
		$order->insertOrderTotals();


		$order->insertStatusHistory(array(
			'orders_status_id'  => sysConfig::get('DEFAULT_ORDERS_STATUS_ID'),
			'customer_notified' => '0',
			'comments'          => ''
		));
		return $order->newOrder['orderID'];
	}

	public function addBillingReport($data){
		$BillingReport = new MembershipBillingReport();
		$BillingReport->customers_id = $this->userAccount->getCustomerId();
		$BillingReport->orders_id = $data['order_id'];
		$BillingReport->error = $data['error'];
		$BillingReport->status = $data['status'];
		$BillingReport->save();
	}
}
?>