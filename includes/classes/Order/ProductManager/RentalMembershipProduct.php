<?php
/**
 * Product for the order product manager
 *
 * @package OrderManager
 * @author Stephen Walker <stephen@itwebexperts.com>
 * @copyright Copyright (c) 2010, I.T. Web Experts
 */

class OrderRentalMembershipProduct implements Serializable {
	private $pInfo = array();
	private $id = null;

	public function __construct($pInfo = null){
		$this->regenerateId();
		if (is_null($pInfo) === false){
			$this->pInfo = $pInfo;
		}
	}

	public function regenerateId(){
		$this->id = tep_rand(5555, 99999);
	}

	public function init(){
		$this->productClass = new Product((int) $this->pInfo['products_id']);
		$this->purchaseTypeClass = $this->productClass->getPurchaseType($this->pInfo['purchase_type']);
	}

	public function serialize(){
		$data = array(
			'id' => $this->id,
			'pInfo' => $this->pInfo
		);
		return serialize($data);
	}

	public function unserialize($data){
		$data = unserialize($data);
		foreach($data as $key => $dInfo){
			$this->$key = $dInfo;
		}
	}

	public function getId(){
		return $this->id;
	}

	public function getOrderedProductId(){
		return $this->pInfo['orders_products_id'];
	}

	public function getProductsId(){
		return $this->pInfo['products_id'];
	}
	
	public function setProductsId($pID){
		$this->pInfo['products_id'] = $pID;
		$this->productClass = new Product($pID);
		$this->pInfo['products_name'] = $this->productClass->getName();
		$this->pInfo['products_model'] = $this->productClass->getModel();
		$this->pInfo['products_tax'] = 0;
	}

	public function getIdString(){
		return $this->pInfo['orders_products_id'];
	}

	public function getPurchaseType(){
		return $this->pInfo['purchase_type'];
	}

	public function setPurchaseType($val){

	}

	public function getTaxRate(){
		return $this->pInfo['products_tax'];
	}

	public function setTaxRate($val){
		$this->pInfo['products_tax'] = $val;
	}

	public function getQuantity(){
		return $this->pInfo['products_quantity'];
	}

	public function setQuantity($val){
		$this->pInfo['products_quantity'] = $val;
	}

	public function getModel(){
		return $this->pInfo['products_model'];
	}

	public function hasBarcode(){
		return (!empty($this->pInfo['barcode_id']));
	}

	public function getBarcode(){
		return $this->pInfo['barcode_id'];
	}

	public function getName(){
		return $this->pInfo['products_name'];
	}

	public function getFinalPrice($wQty = false, $wTax = false){
		$price = $this->pInfo['final_price'];
		if ($wQty === true){
			$price *= $this->getQuantity();
		}

		if ($wTax === true){
			$price = tep_add_tax($price, $this->getTaxRate());
		}
		return $price;
	}

	public function getWeight(){
		return $this->productClass->getWeight();
	}

	private function getTaxAddressInfo(){
		global $order, $userAccount;
		$zoneId = null;
		$countryId = null;
		if (is_object($order)){
			$taxAddress = $userAccount->plugins['addressBook']->getAddress($order->taxAddress);
			$zoneId = $taxAddress['entry_zone_id'];
			$countryId = $taxAddress['entry_country_id'];
		}
		return array(
			'zoneId' => $zoneId,
			'countryId' => $countryId
		);
	}

	public function getTaxRateEdit(){
		return '<input type="text" size="5" class="ui-widget-content taxRate" name="product[' . $this->id . '][tax_rate]" value="' . $this->getTaxRate() . '">%';
	}

	public function getPriceEdit($incQty = false, $incTax = false){
		global $currencies;
		$html = '';
		if ($incQty === false && $incTax === false){
			$html = '<input type="text" size="5" class="ui-widget-content priceEx" name="product[' . $this->id . '][price]" value="' . $this->getFinalPrice($incQty, $incTax) . '">';
		}elseif ($incQty === true && $incTax === false){
			//$html = '<b class="priceExTotal">' . $currencies->format($this->getFinalPrice($incQty, $incTax), true, $order->info['currency'], $order->info['currency_value']) . '</b>';
			$html = '<b class="priceExTotal">' . $currencies->format($this->getFinalPrice($incQty, $incTax)) . '</b>';
		}elseif ($incQty === false && $incTax === true){
			//$html = '<b class="priceIn">' . $currencies->format($this->getFinalPrice($incQty, $incTax), true, $order->info['currency'], $order->info['currency_value']) . '</b>';
			$html = '<b class="priceIn">' . $currencies->format($this->getFinalPrice($incQty, $incTax)) . '</b>';
		}elseif ($incQty === true && $incTax === true){
			//$html = '<b class="priceInTotal">' . $currencies->format($this->getFinalPrice($incQty, $incTax), true, $order->info['currency'], $order->info['currency_value']) . '</b>';
			$html = '<b class="priceInTotal">' . $currencies->format($this->getFinalPrice($incQty, $incTax)) . '</b>';
		}
		return $html;
	}

	public function getQuantityEdit(){
		return '<input type="text" size="3" class="ui-widget-content productQty" name="product[' . $this->id . '][qty]" value="' . $this->getQuantity() . '">&nbsp;x';
	}

	public function getNameHtml(){

		$nameHref = htmlBase::newElement('a')
		//->setHref(itw_catalog_app_link('products_id=' . $this->getProductsId(), 'product', 'info'))
		->css(array(
			'font-weight' => 'bold'
		))
		->attr('target', '_blank')
		->html($this->getName());
		if ($this->getProductsId() > 0){
			$nameHref->setHref(itw_catalog_app_link('products_id=' . $this->getProductsId(), 'product', 'info'));
		}
		$purchaseTypeHtml = '';
		if ($this->pInfo['purchase_type'] != 'membership'){
			$purchaseTypeHtml = htmlBase::newElement('span')
			->css(array(
				'font-size' => '.8em',
				'font-style' => 'italic'
			))
			->html(' - Purchase Type: ' . ucfirst($this->pInfo['purchase_type']))
			->draw();
		}

		$name = $nameHref->draw() .
			'<br />' .
			$purchaseTypeHtml;
		if ($this->pInfo['purchase_type'] != 'membership'){
			$name .= $this->purchaseTypeClass->orderAfterProductName($this);

			$Result = EventManager::notifyWithReturn('OrderProductAfterProductName', &$this);
			foreach($Result as $html){
				$name .= $html;
			}
		}

		return $name;
	}

	public function getNameEdit(){
		global $typeNames;
		$productsName = $this->getName();
		if ($this->getPurchaseType() != 'membership'){
			$PurchaseTypes = Doctrine_Core::getTable('Products')
				->getRecordInstance()
				->getPurchaseTypes((int) $this->getProductsId(), true);

			$purchaseTypeInput = htmlBase::newElement('selectbox')
					->addClass('ui-widget-content purchaseType')
					->setName('product[' . $this->id . '][purchase_type]');
			foreach($PurchaseTypes as $typeName){
				$purchaseTypeInput->addOption($typeName, $typeNames[$typeName]);
			}
			$purchaseTypeInput->selectOptionByValue($this->getPurchaseType());

			$productsName .= '<br><nobr><small>&nbsp;<i> - Purchase Type: ' . $purchaseTypeInput->draw() . '</i></small></nobr>';
		}

		$productsName .= $this->purchaseTypeClass->orderAfterEditProductName($this);

		$contents = EventManager::notifyWithReturn('OrderProductAfterProductNameEdit', $this);
		foreach($contents as $content){
			$productsName .= $content;
		}
		return $productsName;
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