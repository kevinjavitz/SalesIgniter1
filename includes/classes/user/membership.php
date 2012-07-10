<?php
interface BindableMethods {
	public function bindMethod($methodName, Closure $func);
	public function unbindMethod($methodName);
}

abstract class StandardClass implements BindableMethods, Serializable {
	protected $boundMethods = array();
	
	public function __call($method, $args){
		if (isset($this->boundMethods[$method])){
			return call_user_func_array($this->boundMethods[$method], array_merge(array(&$this), $args));
		}
	}
	
	public function bindMethod($methodName, Closure $func){
		$this->boundMethods[$methodName] = $func;
	}

	public function unbindMethod($methodName){
		if (array_key_exists($methodName, $this->boundMethods)){
			unset($this->boundMethods[$methodName]);
		}
	}

	public function serialize(){
		$serialize = array();
		foreach(get_object_vars($this) as $varName => $varVal){
			if ($varName == 'boundMethods'){
				continue;
			}else{
				$serialize[$varName] = $varVal;
			}
		}
		return serialize($serialize);
	}
	
	public function unserialize($data){
		$data = unserialize($data);
		foreach($data as $varName => $varVal){
			$this->$varName = $varVal;
		}
	}
}

class rentalStoreUser_membership extends StandardClass {
	
	public function __construct($customerId){
		$this->customerId = $customerId;
		if ($customerId > 0){
			$this->loadMembershipInfo();
			$this->loadPlanInfo();
			//$this->userAccount->plugins['addressBook']->setRentalAddressId($this->membershipInfo['rental_address_id']);
		}
	}

	public function &getUserAccount(){
		global $userAccount;
		if (Session::exists('userAccount') === true){
			$userAccountCheck = &Session::getReference('userAccount');
			if (is_object($userAccountCheck)){
				$userAccount =& $userAccountCheck;
			}
		}
		return $userAccount;
	}
	
	public function dateToTime($date){
		$date = date_parse($date);
		return mktime(0,0,0,$date['month'],$date['day'],$date['year']);
	}

	public function loadMembershipInfo(){
		$Qmembership = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchAssoc('select * from customers_membership where customers_id = "' . $this->customerId .'"');

		if(isset($Qmembership[0])){
			$nextBillDate = $this->dateToTime($Qmembership[0]['next_bill_date']);
			$freeTrialEnds = $this->dateToTime($Qmembership[0]['free_trial_ends']);

			$userAccount = &$this->getUserAccount();
			$this->membershipInfo = array(
				'plan_id'                    => $Qmembership[0]['plan_id'],
				'plan_name'                  => $Qmembership[0]['plan_name'],
				'plan_price'                 => $Qmembership[0]['plan_price'],
				'plan_tax_class_id'          => $Qmembership[0]['plan_tax_class_id'],
				'ismember'                   => $Qmembership[0]['ismember'],
				'activate'                   => $Qmembership[0]['activate'],
				'canceled'                   => $Qmembership[0]['canceled'],
				'membership_date'            => $Qmembership[0]['membership_date'],
				'next_bill_date'             => $nextBillDate,
				'free_trial_flag'            => $Qmembership[0]['free_trial_flag'],
				'free_trial_ends'            => $freeTrialEnds,
				'cancel_date'                => $Qmembership[0]['cancel_date'],
				'payment_method'             => $Qmembership[0]['payment_method'],
				'rental_address_id'          => $userAccount->getDefaultAddressId()/*$Qmembership->getVal('rental_address_id')*/,
				'subscr_id'                  => $Qmembership[0]['subscr_id'],
				'payment_term'               => $Qmembership[0]['payment_term'],
                'auto_billing'               => $Qmembership[0]['auto_billing']/*,
				'card_num'                   => $Qmembership[0]['card_num'],
				'exp_date'                   => $Qmembership[0]['exp_date']*/
			);

			EventManager::notify('LoadUserMembershipInfo', $this, &$this->membershipInfo, $Qmembership);
		}
	}

	public function loadPlanInfo(){
		if(isset($this->membershipInfo['plan_id'])){
			$this->planInfo = $this->getPlanInfo($this->membershipInfo['plan_id']);
		}
	}

	public function getPlanInfo($planId){

		$Qmembership = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchAssoc('select m.*,md.* from membership m left join membership_plan_description md on m.plan_id=md.plan_id where md.language_id = "' . Session::get('languages_id') .'" and m.plan_id = "'.$planId.'"');

		$planInfo = null;
		if (count($Qmembership) > 0){
			$planInfo = array(
				'plan_id'                     => $Qmembership[0]['plan_id'],
				'price'                       => $Qmembership[0]['price'],
				'rent_tax_class_id'           => $Qmembership[0]['rent_tax_class_id'],
				'package_name'                => $Qmembership[0]['name'],
				'membership_months'           => $Qmembership[0]['membership_months'],
				'membership_days'             => $Qmembership[0]['membership_days'],
				'no_of_titles'                => $Qmembership[0]['no_of_titles'],
				'date_added'                  => $Qmembership[0]['date_added'],
				'last_modified'               => $Qmembership[0]['last_modified'],
				'free_trial'                  => $Qmembership[0]['free_trial'],
				'free_trial_flag'             => $Qmembership[0]['free_trial'] > 0 ? 'Y' : 'N',
				'free_trial_amount'           => $Qmembership[0]['free_trial_amount'],
				'reccurring'                  => $Qmembership[0]['reccurring'],
				'payment_term'                => $Qmembership[0]['payment_term']
			);
		
			EventManager::notify('GetUserMembershipPlanInfo', &$planInfo, $Qmembership);
		}
		return $planInfo;
	}

	public function setCustomerPlan($planId){
		$planInfo = $this->getPlanInfo($planId);

		$CustomersMembership = Doctrine_Core::getTable('CustomersMembership')->findOneByCustomersId($this->customerId);
		$CustomersMembership->plan_id = $planInfo['plan_id'];
		$CustomersMembership->plan_name = $planInfo['package_name'];
		$CustomersMembership->plan_price = $planInfo['price'];
		$CustomersMembership->plan_tax_class_id = $planInfo['rent_tax_class_id'];
		$CustomersMembership->payment_term = $planInfo['payment_term'];
		
		EventManager::notify('SetUserMembershipPlanBeforeSave', $this, $planInfo, $CustomersMembership);
		
		$CustomersMembership->save();
	}

	public function cancelCustomerMembership(){
		Doctrine_Query::create()
		->update('CustomersMembership')
		->set('canceled', '?', '1')
		->set('ismember', '?', 'U')
		->set('activate', '?', 'N')
		->where('customers_id = ?', $this->customerId)
		->execute();
	}

	public function updateActivationStatus($newStatus){
		Doctrine_Query::create()
		->update('CustomersMembership')
		->set('activate', '?', $newStatus)
		->where('customers_id = ?', $this->customerId)
		->execute();
	}

	public function updateMembershipBillDate($newDate){
		Doctrine_Query::create()
		->update('CustomersMembership')
		->set('next_bill_date', '?', $newDate)
		->where('customers_id = ?', $this->customerId)
		->execute();
	}

	public function updateCreditCard($cardNum, $expDate, $cardCvv = false){
		Doctrine_Query::create()
		->update('CustomersMembership')
		->set('card_num', '?', cc_encrypt($cardNum))
		->set('exp_date', '?', cc_encrypt($expDate))
		->set('card_cvv', '?', ($cardCvv !== false ? cc_encrypt($cardCvv) : ''))
		->where('customers_id = ?', $this->customerId)
		->execute();
	}

	public function updateTrialStatus($status){
		Doctrine_Query::create()
		->update('CustomersMembership')
		->set('free_trial_flag', '?', $status)
		->where('customers_id = ?', $this->customerId)
		->execute();
	}

	public function getCreditCardInfo(){
		$userAccount =& $this->getUserAccount();
		$QcreditCard = Doctrine_Query::create()
			->from('CustomersMembership')
			->where('customers_id = ?', $this->customerId)
			->fetchArray();
		return array(
			'cardNumEnc' => $QcreditCard[0]['card_num'],
			'expDateEnc' => $QcreditCard[0]['exp_date'],
			'cardCvvEnc' => $QcreditCard[0]['card_cvv']
		);
	}

	public function setRentalAddress($aID, $updateDB = false){
		$this->membershipInfo['rental_address_id'] = $aID;

		if ($updateDB === true){
			Doctrine_Query::create()
			->update('CustomersMembership')
			->set('rental_address_id', '?', $aID)
			->where('customers_id = ?', $this->customerId)
			->execute();
		}
	}

	public function createNewMembership(){		
		$userAccount =& $this->getUserAccount();
		$addressBook = $userAccount->plugins['addressBook'];
		$billingAddress = $addressBook->getAddress('billing');
		$CustomersMembership = Doctrine_Core::getTable('CustomersMembership')->findOneByCustomersId($this->customerId);
		if (!$CustomersMembership){
			$CustomersMembership = new CustomersMembership();
		}
		$CustomersMembership->customers_id = $this->customerId;
		$CustomersMembership->plan_id = $this->planInfo['plan_id'];
		$CustomersMembership->plan_name = $this->planInfo['package_name'];
		$CustomersMembership->plan_price = $this->planInfo['price'];
		$CustomersMembership->plan_tax_class_id = $this->planInfo['rent_tax_class_id'];
		$CustomersMembership->ismember = 'M';
		$CustomersMembership->activate = $this->membershipInfo['activate'];
		$CustomersMembership->canceled = '0';
		$CustomersMembership->cancel_date = '0000/00/00';
		$CustomersMembership->next_bill_date = date('Y-m-d', $this->membershipInfo['next_bill_date']);
		if (!isset($this->membershipInfo['free_trial_flag'])){
			$this->membershipInfo['free_trial_flag'] = 'N';
		}
		$CustomersMembership->free_trial_flag = $this->membershipInfo['free_trial_flag'];
		$CustomersMembership->free_trial_ends = ($this->membershipInfo['free_trial_flag'] == 'Y' ? date('Y-m-d', $this->membershipInfo['free_trial_ends']) : date('Y-m-d'));
		$CustomersMembership->payment_method = $this->membershipInfo['payment_method'];
		$CustomersMembership->rental_address_id = $this->membershipInfo['rental_address_id'];
		$CustomersMembership->subscr_id = (isset($this->membershipInfo['subscr_id']) ? $this->membershipInfo['subscr_id'] : '');
		$CustomersMembership->payment_term = $this->membershipInfo['payment_term'];
        $CustomersMembership->auto_billing = $this->membershipInfo['auto_billing'];
		if (isset($this->membershipInfo['card_num'])) $CustomersMembership->card_num = $this->membershipInfo['card_num'];
		if (isset($this->membershipInfo['exp_date'])) $CustomersMembership->exp_date = $this->membershipInfo['exp_date'];
		if (isset($this->membershipInfo['card_cvv'])) $CustomersMembership->card_cvv = $this->membershipInfo['card_cvv'];
		
		EventManager::notify('CreateUserMembershipAccountBeforeSave', $this, $CustomersMembership);
		
		$CustomersMembership->save();
		
		Session::set('account_action', 'new');
		$this->sendMembershipEmail();
	}

	public function updateMembership(){
		$userAccount =& $this->getUserAccount();
		$addressBook = $userAccount->plugins['addressBook'];
		$billingAddress = $addressBook->getAddress('billing');

		$CustomersMembership = Doctrine_Core::getTable('CustomersMembership')->findOneByCustomersId($this->customerId);
		if(isset($this->membershipInfo['next_bill_date']) && !empty($this->membershipInfo['next_bill_date'])){
			$CustomersMembership->plan_id = $this->planInfo['plan_id'];
		}
		$CustomersMembership->plan_name = $this->planInfo['package_name'];
		$CustomersMembership->plan_price = $this->planInfo['price'];
		$CustomersMembership->plan_tax_class_id = $this->planInfo['rent_tax_class_id'];
		$CustomersMembership->ismember = $this->membershipInfo['ismember'];
		$CustomersMembership->activate = $this->membershipInfo['activate'];

		if(isset($this->membershipInfo['next_bill_date']) && !empty($this->membershipInfo['next_bill_date'])){
			$CustomersMembership->next_bill_date = date('Y-m-d', $this->membershipInfo['next_bill_date']);
		}
		$CustomersMembership->free_trial_flag = $this->membershipInfo['free_trial_flag'];
		$CustomersMembership->free_trial_ends = (!empty($this->membershipInfo['free_trial_ends']) ? date('Y-m-d', $this->membershipInfo['free_trial_ends']) : '');
		if(isset($this->membershipInfo['payment_method']) && !empty($this->membershipInfo['payment_method'])){
			$CustomersMembership->payment_method = $this->membershipInfo['payment_method'];
		}
		if(isset($this->membershipInfo['payment_term']) && !empty($this->membershipInfo['payment_term'])){
			$CustomersMembership->payment_term = $this->membershipInfo['payment_term'];
		}

        if(isset($this->membershipInfo['auto_billing']) && !empty($this->membershipInfo['auto_billing'])){
            $CustomersMembership->auto_billing = $this->membershipInfo['auto_billing'];
        }

		if(isset($this->membershipInfo['card_num']) && !empty($this->membershipInfo['card_num'])){
			$CustomersMembership->card_num = $this->membershipInfo['card_num'];
		}
		if(isset($this->membershipInfo['exp_date']) && !empty($this->membershipInfo['exp_date'])){
			$CustomersMembership->exp_date = $this->membershipInfo['exp_date'];
		}
		if(isset($this->membershipInfo['card_cvv']) && !empty($this->membershipInfo['card_cvv'])){
			$CustomersMembership->card_cvv = $this->membershipInfo['card_cvv'];
		}
		
		EventManager::notify('UpdateUserMembershipAccountBeforeSave', $this, $CustomersMembership);
		
		$CustomersMembership->save();
	}

	public function getBillingAttempts($oID){
		$Qcheck = Doctrine_Query::create()
		->from('Orders')
		->where('orders_id = ?', $oID)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		return $Qcheck[0]['bill_attempts'];
	}

	public function needsRetry($cID){
		$Qcheck = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchAssoc('select * from membership_billing_report where customers_id = "' . $cID .'" order by billing_report_id desc limit 1');

		if ($Qcheck){
			$LastReport = $Qcheck[0];
			if ($LastReport['status'] == 'D'){
				$dateAdded = date_parse($LastReport['date']);
				$timeAdded = mktime(0,0,0,$dateAdded['month'],$dateAdded['day'],$dateAdded['year']);
				$timeNow = time();
				$daysDiff = ($timeNow - $timeAdded) / (60*60*24);
				if ($daysDiff <= sysConfig::get('MAX_RECURRING_BILLING_DAYS')){
					if ($this->getBillingAttempts($LastReport['orders_id']) < sysConfig::get('MAX_RECURRING_BILLING_DAYS')){
						return true;
					}
				}
			}
		}
		return false;
	}

	public function sendMembershipEmail($sendAdmin = true){
		global $currencies, $order;
		$planInfo = $this->getPlanInfo($this->membershipInfo['plan_id']);
		$paymentInfo = $this->membershipInfo['payment_method'];
		$userAccount =& $this->getUserAccount();
		$addressBook = $userAccount->plugins['addressBook'];
		$billingAddress = $addressBook->getAddress('billing');

		$planTaxRate = $this->getPlanTaxRate($billingAddress['entry_country_id'], $billingAddress['entry_zone_id']);
		$priceIncTax = tep_add_tax($planInfo['price'], $planTaxRate);
		if (isset($order)){
			$totalPrice = $currencies->format($priceIncTax, true, $order->info['currency'], $order->info['currency_value']);
		}else{
			$totalPrice = $currencies->format($priceIncTax);
		}

		$newAccount = false;
		$renewAccount = false;
		$upgradeAccount = false;
		switch(Session::get('account_action')){
			case 'new':
				$newAccount = true;
				$actionText = 'created';
				break;
			case 'renew':
				$renewAccount = true;
				$actionText = 'renewed';
				break;
			case 'upgrade':
				$upgradeAccount = true;
				$actionText = 'upgraded';
				break;
		}

		$emailEvent = new emailEvent('rental_order_success', Session::get('languages_id'));
		$emailEvent->setVars(array(
			'newAccount'       => $newAccount,
			'renewAccount'     => $renewAccount,
			'upgradeAccount'   => $upgradeAccount,
			'firstname'        => $userAccount->getFirstName(),
			'actionText'       => $actionText,
			'customerLastName'  => $userAccount->getLastName(),
			'currentPlanPackageName'      => $planInfo['package_name'],
			'currentPlanNumberOfTitles'  => $planInfo['no_of_titles'],
			'currentPlanFreeTrial'  => $planInfo['free_trial'],
			'currentPlanPrice'      => $totalPrice
		));

		if ($planInfo['membership_months'] > 0){
			$emailEvent->setVar('currentPlanMembershipDays', $planInfo['membership_months'] . ' Months');
		}elseif ($planInfo['membership_days'] > 0){
			$emailEvent->setVar('currentPlanMembershipDays', $planInfo['membership_days'] . ' Days');
		}
		
		$emailEvent->sendEmail(array(
			'email' => $userAccount->getEmailAddress(),
			'name'  => $userAccount->getFullName()
		));

		if ($sendAdmin === true){
			$emailEvent->setEvent('rental_order_success_admin', Session::get('languages_id'));
			
			switch(Session::get('account_action')){
				case 'new':
					$emailEvent->setVar('adminSubject', 'new subscription');
					break;
				case 'renew':
					$emailEvent->setVar('adminSubject', 'subscription renewal');
					break;
				case 'upgrade':
					$emailEvent->setVar('adminSubject', 'subscription upgrade');
					break;
			}
			$emailEvent->setVars(array(
				'customerId'   => $userAccount->getCustomerId(),
				'full_name'    => $userAccount->getFullName(),
				'emailAddress' => $userAccount->getEmailAddress()
			));
		
			$emailEvent->sendEmail(array(
				'email' => sysConfig::get('STORE_OWNER_EMAIL_ADDRESS'),
				'name'  => sysConfig::get('STORE_OWNER_EMAIL_ADDRESS')
			));
		}
	}

	public function setPlanId($value){
		$this->planInfo = $this->getPlanInfo($value);

		$this->membershipInfo['plan_id'] = $this->planInfo['plan_id'];
		$this->membershipInfo['plan_name'] = $this->planInfo['package_name'];
		$this->membershipInfo['plan_price'] = $this->planInfo['price'];
		$this->membershipInfo['plan_tax_class_id'] = $this->planInfo['rent_tax_class_id'];
		$this->membershipInfo['payment_term'] = $this->planInfo['payment_term'];
	}

	public function setCustomerId($value){
		$this->customerId = $value;
	}

	public function setMembershipStatus($value){
		$this->membershipInfo['ismember'] = $value;
	}

    public function setAutoBilling($value){
        $this->membershipInfo['auto_billing'] = $value;
    }

	public function setActivationStatus($value){
		$this->membershipInfo['activate'] = $value;
	}

	public function setFreeTrailEnd($value){
		$now = time();
		if ($value > $now){
			$this->membershipInfo['free_trial_flag'] = 'Y';
		}else{
			$this->membershipInfo['free_trial_flag'] = 'N';
		}

		$this->membershipInfo['free_trial_ends'] = $value;
	}

	public function setNextBillDate($value){
		$this->membershipInfo['next_bill_date'] = $value;
	}

	public function setPaymentTerm($value){
		$this->membershipInfo['payment_term'] = $value;
	}

	public function setPaymentMethod($value){
		$this->membershipInfo['payment_method'] = $value;
	}

	public function setCreditCardNumber($value){
		$this->membershipInfo['card_num'] = cc_encrypt($value);
	}

	public function setCreditCardCvvNumber($value){
		$this->membershipInfo['card_cvv'] = cc_encrypt($value);
	}

	public function setCreditCardExpirationDate($value){
		$this->membershipInfo['exp_date'] = cc_encrypt($value);
	}

	public function setSubscriptionId($value){
		$this->membershipInfo['subscr_id'] = $value;
	}

	public function planIsCancelled(){
		return ($this->membershipInfo['canceled'] == '1');
	}

	public function isRentalMember(){
		return (isset($this->membershipInfo['ismember']) && $this->membershipInfo['ismember'] == 'M');
	}

	public function isPastDue(){
		return ($this->isRentalMember() && ($this->getNextBillDate() + sysConfig::get('RENTAL_DAYS_CUSTOMER_PAST_DUE')*60*60*24) < time());
	}

	public function isExpiring(){
		$cardInfo = $this->getCreditCardInfo();

		if (!empty($cardInfo['cardNumEnc'])){
			$cardExpDate = cc_decrypt($cardInfo['expDateEnc']);

			$year = substr($cardExpDate,-4);
			if ($year != null){
				$arr_date = explode($year, $cardExpDate);
			}
			$todayMonth = date('m');
			$todayYear = date('Y');
			if(isset($arr_date[0]) && $todayMonth == $arr_date[0] && $todayYear == $year){
				return true;
			}

		}
	   return false;

	}

	public function isActivated(){
		return ($this->membershipInfo['activate'] == 'Y');
	}

	public function needsRenewal(){
		return ($this->membershipInfo['activate'] == 'N');
	}

	public function getRentalAddressId(){
		return $this->membershipInfo['rental_address_id'];
	}

	public function getPaymentMethod(){
		return $this->membershipInfo['payment_method'];
	}

	public function getMembershipMonths(){
		return $this->planInfo['membership_months'];
	}

	public function getMembershipDays(){
		return $this->planInfo['membership_days'];
	}

	public function getNextBillDate(){
		return $this->membershipInfo['next_bill_date'];
	}

	public function getPlanTaxId(){
		return $this->planInfo['rent_tax_class_id'];
	}

	public function getPlanId(){
		return $this->planInfo['plan_id'];
	}

	public function getRentalsAllowed(){
		return $this->planInfo['no_of_titles'];
	}

	public function getMembershipDate(){
		return $this->membershipInfo['membership_date'];
	}

	public function getPlanTaxRate($country_id = -1, $zone_id = -1){
		if ($country_id == -1){
			$country_id = sysConfig::get('STORE_COUNTRY');
		}
		if ($zone_id == -1){
			$zone_id = sysConfig::get('STORE_ZONE');
		}
		return tep_get_tax_rate($this->planInfo['rent_tax_class_id'], $country_id, $zone_id);
	}

	public function getMembershipTaxRate($country_id = -1, $zone_id = -1){
		if ($country_id == -1){
			$country_id = sysConfig::get('STORE_COUNTRY');
		}
		if ($zone_id == -1){
			$zone_id = sysConfig::get('STORE_ZONE');
		}
		return tep_get_tax_rate($this->membershipInfo['plan_tax_class_id'], $country_id, $zone_id);
	}

	public function getPlanPrice(){
		return $this->planInfo['price'];
	}

	public function isRecurringPlan(){
		return (($this->planInfo['reccurring'] == '1')?true:false);
	}

	public function getMembershipPrice(){
		return $this->membershipInfo['plan_price'];
	}

	public function getPlanName(){
		return $this->planInfo['package_name'];
	}

	public function getMembershipName(){
		return $this->membershipInfo['plan_name'];
	}
	public function getAllPlanInfo(){
		return $this->planInfo;
	}
}
?>