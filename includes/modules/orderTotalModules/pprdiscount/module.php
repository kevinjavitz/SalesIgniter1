<?php
class OrderTotalPprdiscount extends OrderTotalModuleBase
{

	public function __construct() {
		/*
		 * Default title and description for modules that are not yet installed
		 */
		$this->setTitle('PPR Discount');
		$this->setDescription('PPR Discount');

		$this->init('pprdiscount');

		if ($this->isInstalled() === true){

			$this->allowpprDiscount = $this->getConfigData('MODULE_ORDER_TOTAL_PPR_DISCOUNT_ENABLE');
			$this->showpprDiscount = $this->getConfigData('STATUS');
			$this->pprDiscountAmount = $this->getConfigData('MODULE_ORDER_TOTAL_PPR_DISCOUNT_MAX_AMOUNT');
			$this->pprDiscountDays = $this->getConfigData('MODULE_ORDER_TOTAL_PPR_DISCOUNT_MAX_DAYS');
		}
	}

	public function process() {
		global $order, $appExtension, $userAccount, $ShoppingCart;
		$pprExt = $appExtension->getExtension('payPerRentals');

		if ($this->allowpprDiscount == 'True' && ($pprExt !== false && $pprExt->isEnabled() === true)){
			$prodIds = array();
			$prodIds[] = -1;
			foreach($ShoppingCart->getProducts() as $cartProduct){
				$pID_string = $cartProduct->getIdString();
				$purchaseType = $cartProduct->getPurchaseType();
				if ($purchaseType == 'new' || $purchaseType == 'used'){
					$prodIds[] = $pID_string;
				}
			}
			$Qorders = Doctrine_Query::create()
			->from('Orders o')
			->leftJoin('o.OrdersProducts op')
			->leftJoin('op.OrdersProductsReservation opr')
			->leftJoin('o.OrdersTotal ot')
			->andWhereIn('ot.module_type', array('total', 'ot_total'))
			->andWhereIn('op.products_id', $prodIds)
			->andWhere('o.date_purchased > FROM_DAYS(TO_DAYS(NOW())-'.$this->pprDiscountDays.')')
			->orderBy('o.date_purchased desc')
			->andWhere('o.customers_id = ?',$userAccount->getCustomerId());

			EventManager::notify('OrdersListingBeforeExecute', &$Qorders);

			$Qorders = $Qorders->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			$discount = 0;
			foreach($Qorders as $oInfo){
				foreach($oInfo['OrdersProducts'] as $opInfo){
					foreach($opInfo['OrdersProductsReservation'] as $oprInfo){
						$product = new product($opInfo['products_id']);
						$purchaseTypeClass = $product->getPurchaseType('reservation');
						$discount += tep_add_tax($opInfo['final_price'], $opInfo['products_tax']) * $opInfo['products_quantity']; //this will need a revise to take into account only reservation products price
						$discount -= $purchaseTypeClass->getDepositAmount();
						break;
					}
				}
			}
			if ($discount > $this->pprDiscountAmount){
				$discount = $this->pprDiscountAmount;
			}
			if ($order->info['total'] - $discount > 0){
				$order->info['total'] -= $discount;
			}
			else {
				$order->info['total'] = 0;
			}
			if ($discount > 0 && ($this->showpprDiscount == 'True')){
				$this->addOutput(array(
						'title' => $this->getTitle() . ':',
						'text' => '-' . $this->formatAmount($discount),
						'value' => -$discount
					));
			}
		}
	}
}

?>