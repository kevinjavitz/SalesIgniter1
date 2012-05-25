<?php

class Extension_postaffiliatepro extends ExtensionBase {

	/**
	 * class constructor
	 * @public
	 * @return void
	 */
	public function __construct() {
		parent::__construct('postaffiliatepro');
	}

	// -------------------------------------------------------------------------------------------

	/**
	 * Initialize this class. (loaded by core)
	 *
	 * @public
	 * @return void
	 */
	public function init() {
		global $App, $appExtension;
		if ($this->isEnabled() === false) return;


		EventManager::attachEvents(array(
			'CheckoutSuccessFinish',
			'CronPaymentSuccess',
			'CheckoutSuccessRemoteFinish',
			'CustomersBeforeDelete',
			'CancelOrderAfterExecute',
			'ApplicationTopBeforeCartAction'
		), null, $this);

	}

	public function CustomersBeforeDelete($Customers){
		$UsrnamesToIds = Doctrine::getTable('UsernamesToIds')->findOneByCustomersEmailAddress($Customers->customers_email_address);
		if ($UsrnamesToIds){
			$UsrnamesToIds->delete();
		}
		$CustomersToOrders = Doctrine::getTable('CustomersToOrders')->findOneByCustomersId($Customers->customers_id);
		if ($CustomersToOrders){
			$CustomersToOrders->delete();
		}
	}

	public function CancelOrderAfterExecute($oID){
		$CustomersToOrders = Doctrine::getTable('CustomersToOrders')->findOneByOrdersId($oID);
		if ($CustomersToOrders){
			$CustomersToOrders->delete();
		}
	}

	public function CronPaymentSuccess($membershipUpdate){
		include_once(sysConfig::getDirFsCatalog(). 'ext/pap/api/PapApi.class.php');
		$session = new Gpf_Api_Session(sysConfig::get('EXTENSION_PAP_URL').'scripts/server.php');
		if(!$session->login(sysConfig::get('EXTENSION_PAP_MERCH'), sysConfig::get('EXTENSION_PAP_PASS'))) {
			return;
		}

		$QCustToOrders = Doctrine::getTable('CustomersToOrders')->findOneByCustomersId($membershipUpdate->getUserAccount()->getCustomerId());
		try{
		$saleTracker = new Pap_Api_SaleTracker(sysConfig::get('EXTENSION_PAP_URL').'scripts/sale.php');
		$saleTracker->setAccountId('default1');
		$sale = $saleTracker->createSale();
		$sale->setTotalCost($QCustToOrders->cost);
		$sale->setOrderID('rec_'.$QCustToOrders->orders_id);
		$sale->setProductID($QCustToOrders->product);
		$sale->setAffiliateID($QCustToOrders->affiliate);
		$sale->setStatus('A');//is automatically approved
		$saleTracker->register();
		} catch(Exception $e){
		}
	}

	public function ApplicationTopBeforeCartAction(){
		//how much the affiliate cookie lasts
		if(isset($_GET['a_aid'])&& !empty($_GET['a_aid'])){
			$ResultSet = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchAssoc('select ids from usernames_to_ids where username = "' . $_GET['a_aid'] . '"');
			if(isset($ResultSet[0])){
				Session::set('refid', $ResultSet[0]['ids']);
			}else{
				Session::set('refid', $_GET['a_aid']);
			}
		}
	}
	public function CheckoutSuccessFinish($Order){
		$QMembershipPlan = Doctrine_Query::create()
			->from('Membership m')
			->leftJoin('m.MembershipPlanDescription mp')
			->where('mp.name = ?', $Order['OrdersProducts'][0]['products_name'])
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if(Session::exists('refid')){
			$referralid = Session::get('refid');
		}
		include_once(sysConfig::getDirFsCatalog().'ext/pap/api/PapApi.class.php');
		if(isset($QMembershipPlan[0]) && $QMembershipPlan[0]['is_affiliate'] == 1){
			$session = new Gpf_Api_Session(sysConfig::get('EXTENSION_PAP_URL').'scripts/server.php');
			if(!$session->login(sysConfig::get('EXTENSION_PAP_MERCH'), sysConfig::get('EXTENSION_PAP_PASS'))) {
			  return;
			}

			$Customer = Doctrine::getTable('Customers')->find((int)$Order['customers_id']);

			// create new affiliate
			$affiliate = new Pap_Api_Affiliate($session);
			$affiliate->setUsername($Order['customers_email_address']);
			$affiliate->setFirstname($Customer->customers_firstname);
			$affiliate->setLastname($Customer->customers_lastname);

			try {
					if ($affiliate->add()) {
						//echo "Affiliate saved successfully";
						$QUsernamesToIds = new UsernamesToIds();
						$QUsernamesToIds->ids = $affiliate->getRefid();
						$QUsernamesToIds->username = $Order['customers_email_address'];
						$QUsernamesToIds->customers_email_address = $Order['customers_email_address'];
						$QUsernamesToIds->save();
						$affiliate->sendConfirmationEmail();
						$affiliate->setStatus('A');
					}
					else {
						echo ("Cannot save affiliate: ".$affiliate->getMessage().'__'.$Customer->customers_firstname.'---'.$Customer->customers_lastname);
					}
			}	catch (Exception $e) {

			}
			try{
				if(isset($referralid)){
					$affiliate->setParentUserId($referralid); //setting the parent affiliate
				}
				$affiliate->save();
			} catch(Exception $e){
				echo 'Cannot set parent:'.$e->getMessage();
			}

		}

		if(isset($referralid) && isset($QMembershipPlan[0]) && $QMembershipPlan[0]['no_of_titles'] > 0){
				try{
					$QCustToOrders = Doctrine::getTable('CustomersToOrders')->findOneByCustomersId((int)$Order['customers_id']);
					if(!$QCustToOrders){
						$QCustToOrders = new CustomersToOrders();
					}
					$planId = $QMembershipPlan[0]['plan_id'];
					$QCustToOrders->customers_id = $Order['customers_id'];
					$QCustToOrders->orders_id = $Order['orders_id'];
					$QCustToOrders->cost = $Order['OrdersTotal'][0]['value'];
					$QCustToOrders->affiliate = $referralid;
					$QCustToOrders->product = $planId;
					$QCustToOrders->save();
					$saleTracker = new Pap_Api_SaleTracker(sysConfig::get('EXTENSION_PAP_URL').'scripts/sale.php');
					$saleTracker->setAccountId('default1');
					$sale = $saleTracker->createSale();
					$sale->setTotalCost($Order['OrdersTotal'][0]['value']);
					$sale->setOrderID($Order['orders_id']);
					$sale->setProductID($planId);
					$sale->setAffiliateID($referralid);
					$sale->setStatus('A');//is automatically approved
					$saleTracker->register();
				} catch(Exception $e){
					echo 'cannot save commision';
				}
		}

	}
	
	public function CheckoutSuccessRemoteFinish($orderId, $amount, $ip, $refid, $customers_id, $planId){
		if(is_numeric($planId)){
			$QMembershipPlan = Doctrine_Query::create()
				->from('Membership m')
				->leftJoin('m.MembershipPlanDescription mp')
				->where('m.plan_id = ?', $planId)
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if($refid != 'n'){
				$referralid = '';
			}
			include_once(sysConfig::getDirFsCatalog().'ext/pap/api/PapApi.class.php');
			if(isset($QMembershipPlan[0]) && $QMembershipPlan[0]['is_affiliate'] == 1){
				$session = new Gpf_Api_Session(sysConfig::get('EXTENSION_PAP_URL').'scripts/server.php');
				if(!$session->login(sysConfig::get('EXTENSION_PAP_MERCH'), sysConfig::get('EXTENSION_PAP_PASS'))) {
					return;
				}

				$Customer = Doctrine::getTable('Customers')->find((int)$customers_id);

				// create new affiliate
				$affiliate = new Pap_Api_Affiliate($session);
				$affiliate->setUsername($Customer->customers_email_address);
				$affiliate->setFirstname($Customer->customers_firstname);
				$affiliate->setLastname($Customer->customers_lastname);

				try {
					if ($affiliate->add()) {
						//echo "Affiliate saved successfully";
						$QUsernamesToIds = new UsernamesToIds();
						$QUsernamesToIds->ids = $affiliate->getRefid();
						$QUsernamesToIds->username = $Customer->customers_email_address;
						$QUsernamesToIds->customers_email_address = $Customer->customers_email_address;
						$QUsernamesToIds->save();
						$affiliate->sendConfirmationEmail();
						$affiliate->setStatus('A');
					}
					else {
						//echo ("Cannot save affiliate: ".$affiliate->getMessage().'__'.$Customer->customers_firstname.'---'.$Customer->customers_lastname);
					}
				}	catch (Exception $e) {

				}
				try{
					if(isset($referralid)){
						$affiliate->setParentUserId($referralid); //setting the parent affiliate
					}
					$affiliate->save();
				} catch(Exception $e){
					//echo 'Cannot set parent:'.$e->getMessage();
				}

			}

			if(isset($referralid) && isset($QMembershipPlan[0]) && $QMembershipPlan[0]['no_of_titles'] > 0){
				try{
					$QCustToOrders = Doctrine::getTable('CustomersToOrders')->findOneByCustomersId((int)$customers_id);
					if(!$QCustToOrders){
						$QCustToOrders = new CustomersToOrders();
					}
					$planId = $QMembershipPlan[0]['plan_id'];
					$QCustToOrders->customers_id = $customers_id;
					$QCustToOrders->orders_id = $orderId;
					$QCustToOrders->cost = $amount;
					$QCustToOrders->affiliate = $referralid;
					$QCustToOrders->product = $planId;
					$QCustToOrders->save();
					$saleTracker = new Pap_Api_SaleTracker(sysConfig::get('EXTENSION_PAP_URL').'scripts/sale.php');
					$saleTracker->setAccountId('default1');
					$sale = $saleTracker->createSale();
					$sale->setTotalCost($amount);
					$sale->setOrderID($orderId);
					$sale->setProductID($planId);
					$sale->setAffiliateID($referralid);
					$sale->setStatus('A');//is automatically approved
					$saleTracker->register();
				} catch(Exception $e){
					//echo 'cannot save commision';
				}
			}
		}
	}
}

/*echo '<script id="pap_x2s6df8d" src="'.sysConfig::get('EXTENSION_PAP_URL').'scripts/salejs.php" type="text/javascript"></script>
			  <script type="text/javascript">
			  var sale = PostAffTracker.createSale();
			  sale.setTotalCost('.$Order['OrdersTotal'][0]['value'].');
			  sale.setOrderID('.$Order['orders_id'].');
			  sale.setProductID(\''.$Order['OrdersProducts'][0]['products_name'].'\');
			  PostAffTracker.register();
			  </script>';
*/
//echo '<img src="'.sysConfig::get('EXTENSION_PAP_URL').'scripts/sale.php?AccountId=default1&TotalCost='.$Order['OrdersTotal'][0]['value'].'&OrderID='.$Order['orders_id'].'&ProductID='.$Order['OrdersProducts'][0]['products_name'].'" width="1" height="1" >';
// sale.setData1(\''.$Order['customers_email_address'].'\'); --for lifetime

/*$recurringCommission = new Pap_Api_RecurringCommission($session);
		$recurringCommission->setOrderId($Order['orders_id']);
		try {
			$recurringCommission->createCommissions();
		} catch (Exception $e) {
			//die("Can not process recurring commission: ".$e->getMessage());
		}
*/

?>
