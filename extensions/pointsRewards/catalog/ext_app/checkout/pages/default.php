<?php
class pointsRewards_catalog_checkout_default extends Extension_pointsRewards {
	public function __construct(){
		parent::__construct();
	}

	public function load(){
		if ($this->isEnabled() === false) return;

		EventManager::attachEvents(array(
			'CheckoutAddBlockAfterCart',
			'CheckoutProcessPostProcess'
		), null, $this);
	}

	public function CheckoutAddBlockAfterCart(){
		$ShoppingCart = &Session::getReference('ShoppingCart');
		$userAccount = &Session::getReference('userAccount');
		
		$Module = OrderTotalModules::getModule('pointsRewards');
		if ($Module !== false && $Module->isEnabled() === true){
			$purchaseTypes = false;
			foreach($ShoppingCart->getProducts() as $cartProduct) {
				$purchaseType = $cartProduct->getPurchaseType();
				if((is_array($purchaseTypes) && !in_array($purchaseType,$purchaseTypes)) || $purchaseTypes == false)
					$purchaseTypes[] = $purchaseType;
			}

			if($purchaseTypes){
				foreach($purchaseTypes as $purchaseType){
					$purchaseTypeInfo = Doctrine_Core::getTable('pointsRewardsPurchaseTypes')
							->findOneByPurchaseType($purchaseType);

					$userThreshold = $Module->check_user_threshold($userAccount->getCustomerId(),$purchaseType);
					$userPoints = $Module->user_has_points($userAccount->getCustomerId(),$purchaseType);
					if($userPoints > 0 && $userThreshold ){
						$userPointsEarned = sprintf(sysLanguage::get('TEXT_POINTS_USE_'.strtoupper($purchaseType)),$userPoints);

						$htmlTable = htmlBase::newElement('table')
								->setCellPadding(2)
								->setCellSpacing(0)
								->addClass('ui-widget')
								->css(array(
								           'width' => '100%'
								      ));
						$redeemPoints = htmlBase::newElement('checkbox')
								->setName('redeem_points[]')
								->setValue($Module->user_has_points($userAccount->getCustomerId(),$purchaseType))
								->attr('purchase_type',$purchaseType);
						$htmlTable->addBodyRow(array(
						                            'columns' => array(
							                            array('addCls' => 'main',
							                                  'css'=>array('text-align:center'),
							                                  'text' => sysLanguage::get('TEXT_REDEEM_LABEL_'.strtoupper($purchaseType)))
						                            )));
						$htmlTable->addBodyRow(array(
						                            'columns' => array(
							                            array('addCls' => 'main',
							                                  'css'=>array('text-align:center'),
							                                  'text' => $redeemPoints->draw() . $userPointsEarned)
						                            )));
						echo $htmlTable->draw();
					}
				}
			}
		}
	}

	/*public function CheckoutProcessPostProcess(&$order){
		$ShoppingCart = &Session::getReference('ShoppingCart');
		$userAccount = &Session::getReference('userAccount');
		if ($_POST['redeem_points'][0] > 0) {
			$QlastOrder = Doctrine_Query::create()
					->select('o.orders_id')
					->from('Orders o')
					->where('o.customers_id = ?', (int)$userAccount->getCustomerId())
					->orderBy('o.orders_id desc')
					->limit(1)
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			foreach ($ShoppingCart->getProducts() as $cartProduct) {
				$pID_string = $cartProduct->getIdString();
				$purchaseType = $cartProduct->getPurchaseType();
				$productPrice = $cartProduct->getPrice();
				$productQuantity = $cartProduct->getQuantity();
				$purchaseTypeInfo = Doctrine_Core::getTable('pointsRewardsPurchaseTypes')
						->findOneByPurchaseType($purchaseType);
				if ((int)$userAccount->getCustomerId() && isset($purchaseTypeInfo->percentage) && (int)$purchaseTypeInfo->percentage > 0) {
					$pointsRewardsEarned = new  pointsRewardsPointsDeducted;
					$pointsRewardsEarned->customers_id = (int)$userAccount->getCustomerId();
					$pointsRewardsEarned->points = $_POST['redeem_points'][0];
					$pointsRewardsEarned->date = date('Y-m-d');
					$pointsRewardsEarned->purchase_type = $purchaseType;
					$pointsRewardsEarned->orders_id = $QlastOrder[0]['orders_id'];
					$pointsRewardsEarned->products_id = $pID_string;
					$pointsRewardsEarned->save();
				}
			}
		} else {
			$QlastOrder = Doctrine_Query::create()
					->select('o.orders_id')
					->from('Orders o')
					->where('o.customers_id = ?', (int)$userAccount->getCustomerId())
					->orderBy('o.orders_id desc')
					->limit(1)
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			foreach ($ShoppingCart->getProducts() as $cartProduct) {
				$pID_string = $cartProduct->getIdString();
				$purchaseType = $cartProduct->getPurchaseType();
				$productPrice = $cartProduct->getPrice();
				$productQuantity = $cartProduct->getQuantity();
				$purchaseTypeInfo = Doctrine_Core::getTable('pointsRewardsPurchaseTypes')
						->findOneByPurchaseType($purchaseType);
				if ((int)$userAccount->getCustomerId() && isset($purchaseTypeInfo->percentage) && (int)$purchaseTypeInfo->percentage > 0) {
					$pointsRewardsEarned = new  pointsRewardsPointsEarned;
					$pointsRewardsEarned->customers_id = (int)$userAccount->getCustomerId();
					$pointsRewardsEarned->points = $productQuantity * (($productPrice / 100) * $purchaseTypeInfo->percentage);
					$pointsRewardsEarned->date = date('Y-m-d');
					$pointsRewardsEarned->purchase_type = $purchaseType;
					$pointsRewardsEarned->orders_id = $QlastOrder[0]['orders_id'];
					$pointsRewardsEarned->products_id = $pID_string;
					$pointsRewardsEarned->save();
				}
			}
		}
	}*/
}
?>