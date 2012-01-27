<?php
/*
	Rental Store Version 2

	I.T. Web Experts
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	class ShoppingCartProduct implements Serializable {
		private $pInfo = array();
		public function __construct($pInfo){
			$this->productClass = new Product((int) $pInfo['id_string']);
			$this->uniqID = $pInfo['uniqID'];
			$this->purchaseTypeClass = $this->productClass->getPurchaseType($pInfo['purchase_type']);
			$this->purchaseTypeClass->processAddToCart(&$pInfo);
			
			EventManager::notify('ShoppingCartProduct\AddToCart', &$pInfo, &$this->productClass, &$this->purchaseTypeClass);
			
			$this->pInfo = $pInfo;
		}
		
		public function serialize(){
			return serialize($this->pInfo);
		}
		
		public function unserialize($data){
			$this->pInfo = unserialize($data);
		}

		public function getUniqID(){
			return $this->uniqID;
		}
		
		public function init(){
			$this->productClass = new Product((int) $this->pInfo['id_string']);
			$this->purchaseTypeClass = $this->productClass->getPurchaseType($this->pInfo['purchase_type']);
			$this->uniqID = $this->pInfo['uniqID'];
			if (isset($this->pInfo['aID_string'])){
				$this->purchaseTypeClass->inventoryCls->invMethod->trackMethod->aID_string = $this->pInfo['aID_string'];
			}
		}
		
		public function getName(){
			return $this->productClass->getName();
		}
		
		public function getImage(){
			return $this->productClass->getImage();
		}
		
		public function getModel(){
			return $this->productClass->getModel();
		}
		
		public function getWeight(){
			return $this->productClass->getWeight();
		}
		
		public function getQuantity(){
			return $this->pInfo['quantity'];
		}
		
		public function getIdString(){
			return $this->pInfo['id_string'];
		}
		
		public function getPurchaseType(){
			return $this->pInfo['purchase_type'];
		}
		
		public function getTaxClassId(){
			return $this->productClass->getTaxClassID();
		}
		
		private function getTaxAddressInfo(){
			global $order, $userAccount;
			$zoneId = null;
			$countryId = null;

			if (sysConfig::get('BASE_TAX_RATE') == 'Billing') {
				$taxAddress = $userAccount->plugins['addressBook']->getAddress('billing');
			} else {
				$taxAddress = $userAccount->plugins['addressBook']->getAddress('delivery');
			}			
			$zoneId = $taxAddress['entry_zone_id'];
			$countryId = $taxAddress['entry_country_id'];
			EventManager::notify('ProductBeforeTaxAddress', &$zoneId, &$countryId, $this, $order, $userAccount);
			return array(
				'zoneId'    => $zoneId,
				'countryId' => $countryId
			);
		}
		
		public function getTaxRate($countryId = null, $zoneId = null){
			if (is_null($countryId) && is_null($zoneId)){
				$taxAddress = $this->getTaxAddressInfo();
				$countryId = $taxAddress['countryId'];
				$zoneId = $taxAddress['zoneId'];
			}
			return tep_get_tax_rate($this->getTaxClassId(), $countryId, $zoneId);
		}
		
		public function getTaxDescription($countryId = null, $zoneId = null){
			if (is_null($countryId) && is_null($zoneId)){
				$taxAddress = $this->getTaxAddressInfo();
				$countryId = $taxAddress['countryId'];
				$zoneId = $taxAddress['zoneId'];
			}
			return tep_get_tax_description($this->getTaxClassId(), $countryId, $zoneId);
		}
		
		public function getPrice($wTax = false){
			if ($wTax === true){
				return tep_add_tax($this->pInfo['price'], $this->getTaxRate());
			}
			return $this->pInfo['price'];
		}
		
		public function setPrice($val){
			$this->pInfo['price'] = $val;
		}
		
		public function addToPrice($val){
			$this->pInfo['price'] += $val;
		}
		
		public function subtractFromPrice($val){
			$this->pInfo['price'] -= $val;
		}
		
		public function getFinalPrice($wTax = false){
			if ($wTax === true){
				return tep_add_tax($this->pInfo['final_price'], $this->getTaxRate());
			}
			return $this->pInfo['final_price'];
		}
		
		public function setFinalPrice($val){
			$this->pInfo['final_price'] = $val;
		}
		
		public function addToFinalPrice($val){
			$this->pInfo['final_price'] += $val;
		}
		
		public function subtractFromFinalPrice($val){
			$this->pInfo['final_price'] -= $val;
		}
		
		public function getNameHtml(){
			$nameHref = htmlBase::newElement('a')
			->setHref(itw_app_link('products_id=' . $this->pInfo['id_string'], 'product', 'info'))
			->css(array(
				'font-weight' => 'bold'
			))
			->html($this->getName());
			
			$purchaseTypeHtml = htmlBase::newElement('span')
			->css(array(
				'font-size' => '.8em',
				'font-style' => 'italic'
			));
			$showType = '';
			switch($this->pInfo['purchase_type']){
				case 'new':
					$showType = sysLanguage::get('PURCHASE_TYPE_NEW_SHOW_SHOPPING_CART');
					break;
				case 'rental':
					$showType = sysLanguage::get('PURCHASE_TYPE_RENTAL_SHOW_SHOPPING_CART');
					break;
				case 'used':
					$showType = sysLanguage::get('PURCHASE_TYPE_USED_SHOW_SHOPPING_CART');
					break;
				case 'reservation':
					$showType = sysLanguage::get('PURCHASE_TYPE_RESERVATION_SHOW_SHOPPING_CART');
					break;
				case 'stream':
					$showType = sysLanguage::get('PURCHASE_TYPE_STREAM_SHOW_SHOPPING_CART');
					break;
				case 'download':
					$showType = sysLanguage::get('PURCHASE_TYPE_DOWNLOAD_SHOW_SHOPPING_CART');
					break;

			}
			$purchaseTypeHtml->html(' - '. sysLanguage::get('TEXT_SHOPPING_CART_PURCHASE_TYPE'). $showType);
			
			$name = $nameHref->draw() . 
					'<br />' . 
					$purchaseTypeHtml->draw();

			if ($this->hasInfo('download_type')){
				$downloadTypeHtml = htmlBase::newElement('span')
				->css(array(
					'font-size' => '.8em',
					'font-style' => 'italic'
				))
				->html(' - '. sysLanguage::get('TEXT_SHOPPING_CART_VIEW_TYPE') . $showType);
				
				$name .= '<br />' . $downloadTypeHtml->draw();
			}
			
			$Result = EventManager::notifyWithReturn('ShoppingCartProduct\ProductNameAppend', &$this);
			foreach($Result as $html){
				$name .= $html;
			}
			
			return $name;
		}
		
		public function getImageHtml(){
			$image = $this->getImage();
			
			EventManager::notify('ShoppingCartProduct\ProductImageBeforeShow', &$image, &$this);
		
			$imageHtml = htmlBase::newElement('image')
			->setSource($image)
			->setWidth(sysConfig::get('SMALL_IMAGE_WIDTH'))
			->setHeight(sysConfig::get('SMALL_IMAGE_HEIGHT'))
			->thumbnailImage(true);
			
			$imageHref = htmlBase::newElement('a')
			->setHref(itw_app_link('products_id=' . $this->pInfo['id_string'], 'product', 'info'))
			->css(array(
				'font-weight' => 'bold'
			))
			->append($imageHtml);
			return $imageHref->draw();
		}
		
		public function hasInfo($key){
			return (isset($this->pInfo[$key]));
		}
		
		public function getInfo($key = null){
			if (is_null($key)){
				return $this->pInfo;
			}else{
				if (isset($this->pInfo[$key])){
					return $this->pInfo[$key];
				}else{
					return false;
				}
			}
		}
		
		public function updateInfo($newInfo){
			$newProductInfo = $this->pInfo;
			foreach($newInfo as $k => $v){
				$newProductInfo[$k] = $v;
			}
			$this->pInfo = $newProductInfo;
			$this->purchaseTypeClass->processUpdateCart(&$this->pInfo);
		}
		
		public function onInsertOrderedProduct($orderID, &$orderedProduct, &$products_ordered){
			$this->purchaseTypeClass->onInsertOrderedProduct($this, $orderID, &$orderedProduct, &$products_ordered);
		}
	}
?>