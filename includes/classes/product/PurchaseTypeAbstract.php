<?php
	abstract class PurchaseTypeAbstract {
		public $productInfo;
		public $inventoryCls;
		public $enabled = false;

		abstract function getPurchaseHtml($key);
		
		public function shoppingCartAfterProductName(&$cartProduct){
			return '';
		}

		public function checkoutAfterProductName(&$cartProduct){
			return '';
		}

		public function orderAfterEditProductName(&$orderedProduct){
			return '';
		}

		public function orderAfterProductName(&$orderedProduct){
			return '';
		}
		
		public function processAddToOrder(&$pInfo){
		}
		
		public function processAddToCart(&$pInfo){
		}
		
		public function processUpdateCart(&$pInfo){
		}
		
		public function processRemoveFromCart(){
		}
		
		public function onInsertOrderedProduct($cartProduct, $orderId, &$orderedProduct, &$products_ordered){
		}

		public function &getInventoryClass(){
			return $this->inventoryCls;
		}
		
		public function getProductId(){
			return $this->productInfo['id'];
		}

		public function getPrice(){
			if ($this->enabled === false || is_null($this->inventoryCls)) return null;
			if (isset($this->productInfo['special_price'])){
				return $this->productInfo['special_price'];
			}
			return $this->productInfo['price'];
		}

		public function displayPrice(){
			global $currencies, $appExtension;
			if ($this->enabled === false || is_null($this->inventoryCls)) return null;
			if (isset($this->productInfo['special_price'])){
				$extSpecials = $appExtension->getExtension('specials');
				$display = $currencies->display_price($this->productInfo['price'], $this->productInfo['taxRate']);
				$extSpecials->ProductNewPriceBeforeDisplay($this->productInfo['special_price'], $display);
				return $display;
			}else{
				return $currencies->display_price($this->productInfo['price'], $this->productInfo['taxRate']);
			}
		}

		public function canUseSpecial(){
			if ($this->enabled === false || is_null($this->inventoryCls)) return false;
			return true;
		}

		public function updateStock($orderId, $orderProductId, &$cartProduct){
			if ($this->enabled === false || is_null($this->inventoryCls)) return true;
			return $this->inventoryCls->updateStock($orderId, $orderProductId, &$cartProduct);
		}

		public function getTrackMethod(){
			if ($this->enabled === false || is_null($this->inventoryCls)) return null;
			return $this->inventoryCls->getTrackMethod();
		}

		public function getCurrentStock(){
			if ($this->enabled === false || is_null($this->inventoryCls)) return null;
			return $this->inventoryCls->getCurrentStock();
		}

		public function getTotalStock(){
			if ($this->enabled === false || is_null($this->inventoryCls)) return null;
			return $this->inventoryCls->getTotalStock();
		}
		public function hasInventory(){
			if ($this->enabled === false) return false;
			if (is_null($this->inventoryCls)) return true;
			return ($this->inventoryCls->hasInventory());
		}

		public function getInventoryItems(){
			if ($this->enabled === false) return false;
			if (is_null($this->inventoryCls)) return true;
			return $this->inventoryCls->getInventoryItems();
		}
	}
?>