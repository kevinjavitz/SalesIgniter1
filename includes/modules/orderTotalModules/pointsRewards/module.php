<?php
class OrderTotalPointsRewards extends OrderTotalModule {
	public function __construct(){
		/*
		 * Default title and description for modules that are not yet installed
		 */
		$this->setTitle('Points Rewards');
		$this->setDescription('Points on shopping and rewards.');
		$this->init('pointsRewards');
		if ($this->isInstalled() === true) {
			$this->credit_class = true;
			//$this->include_shipping = $this->getConfigData('MODULE_ORDER_TOTAL_COUPON_INC_SHIPPING');
			//$this->include_tax = $this->getConfigData('MODULE_ORDER_TOTAL_COUPON_INC_TAX');
			//$this->calculate_tax = $this->getConfigData('MODULE_ORDER_TOTAL_COUPON_CALC_TAX');
			//$this->tax_class = $this->getConfigData('MODULE_ORDER_TOTAL_COUPON_TAX_CLASS');
			$this->user_prompt = '';
			$this->header = $this->getConfigData('MODULE_ORDER_TOTAL_POINTSREWARDS_HEADER');
		}
	}

	public function process(){
		global $order;
		$ShoppingCart = &Session::getReference('ShoppingCart');
		$userAccount = &Session::getReference('userAccount');

		if(!Session::exists('pointsRewards_points') || Session::get('pointsRewards_points') <= 0)
			return false;
		
		$order->info['total'] = $order->info['total'];
		$purchaseTypes = false;
		$discountAmount = 0;
		foreach ($ShoppingCart->getProducts() as $cartProduct) {
			$purchaseType = $cartProduct->getPurchaseType();
			if ((is_array($purchaseTypes) && !in_array($purchaseType, $purchaseTypes)) || $purchaseTypes == false)
				$purchaseTypes[] = $purchaseType;
		}
		if ($purchaseTypes) {

			foreach ($purchaseTypes as $purchaseType) {
				$discountAmount = 0;
				$discountAmount += $this->getCustomerPRAmount($userAccount->getCustomerId(), $purchaseType);
			}
		}

		if ( $discountAmount > 0 && $discountAmount <= $order->info['total']) {
			$order->info['total'] = $order->info['total']- $discountAmount;
			$this->addOutput(array(
								  'title' => $this->getTitle() . ':',
								  'text' => '<b>-' . $this->formatAmount($discountAmount) . '</b>',
								  'value' => $order->info['total']
							 ));
		}
	}

	public function selection_test(){
		return true;
	}

	public function user_has_points($c_id, $purchaseType){
		$QpointsTable = Doctrine_Query::create()
				->select('sum(points) as totalPoints')
				->from('pointsRewardsPointsEarned ')
				->where('customers_id= ?', (int)$c_id);
		if(sysConfig::get('EXTENSION_POINTS_REWARDS_SYSTEM_POINTS_ON_SAME_PURCHASETYPE') == 'True'){
			$QpointsTable->andWhere('purchase_type = "' . $purchaseType . '"');
		}
		$Qpoints = $QpointsTable->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		$QpointsDeductedTable = Doctrine_Query::create()
				->select('sum(points) as totalPoints')
				->from('pointsRewardsPointsDeducted ')
				->where('customers_id= ?', (int)$c_id);
		if(sysConfig::get('EXTENSION_POINTS_REWARDS_SYSTEM_POINTS_ON_SAME_PURCHASETYPE') == 'True'){
			$QpointsDeductedTable->andWhere('purchase_type = "' . $purchaseType . '"');
		}
		$QpointsDeducted = $QpointsDeductedTable->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Qpoints) {
			$finalTotal = $Qpoints[0]['totalPoints'] - $QpointsDeducted[0]['totalPoints'];
			return $finalTotal;
		} else {
			$finalTotal = 0;
		}
		return $finalTotal;
	}
	public function check_user_threshold($c_id,$purchase_type){
		$pointsTotal = $this->user_has_points($c_id,$purchase_type);
		$purchaseTypeInfo = Doctrine_Core::getTable('pointsRewardsPurchaseTypes')
				->findOneByPurchaseType($purchase_type);
		if($pointsTotal >= $purchaseTypeInfo->threshold){
			return true;
		}
		return false;
	}
	public function user_threshold_count($c_id,$purchaseType){
		$QpointsEarned = Doctrine_Query::create()
				->select('sum(pointsEarned) as pointsTotal')
				->from('pointsRewardsPointsEarned ')
				->where('customers_id= ?', (int)$c_id);
		if(sysConfig::get('EXTENSION_POINTS_REWARDS_SYSTEM_POINTS_ON_SAME_PURCHASETYPE') == 'TRUE'){
				$QpointsEarned->andWhere('purchase_type = "' . $purchaseType . '"');
		}
				$QpointsEarned->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		$thresholdEarned = $QpointsEarned[0]['pointsTotal'];

		$QpointsDeducted = Doctrine_Query::create()
				->select('count(*) as thresholdTotal')
				->from('pointsRewardsPointsDeducted ')
				->where('customers_id= ?', (int)$c_id)
				->andWhere('purchase_type = "' . $purchaseType . '"')
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		$purchaseTypeInfo = Doctrine_Core::getTable('pointsRewardsPurchaseTypes')
				->findOneByPurchaseType($purchaseType);

		$thresholdDeducted = $QpointsDeducted[0]['thresholdTotal'];

		$thresholdTotal = $thresholdEarned - $thresholdDeducted;

		return $thresholdTotal;

	}

	public function getCustomerPRAmount($customerId, $purchaseType){
		global $order;
		$ShoppingCart = &Session::getReference('ShoppingCart');
		$userAccount = &Session::getReference('userAccount');
		$pointsAmount = 0;
		foreach ($ShoppingCart->getProducts() as $cartProduct) {
			$purchaseTypeInfo = Doctrine_Core::getTable('pointsRewardsPurchaseTypes')
					->findOneByPurchaseType($purchaseType);
			if ($purchaseType == 'reservation' && sysConfig::get('EXTENSION_POINTS_REWARDS_SYSTEM_FREE_PRODUCT_OFFER')=='True') {
				if($this->check_user_threshold($userAccount->getCustomerId(), $purchaseType)) {
					$pointsAmount += $cartProduct->getPrice();
				}
			} else {
				$pointsToSpend = $this->user_has_points((int)$userAccount->getCustomerId(), $purchaseType);

				if ($pointsToSpend > 0 && $this->check_user_threshold($userAccount->getCustomerId(), $purchaseType) ) {
					$pointsAmount += $purchaseTypeInfo->conversionRatio * $pointsToSpend;
				}
			}
		}
		return $pointsAmount;
	}
}
?>