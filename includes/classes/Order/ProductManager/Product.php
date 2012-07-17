<?php
/**
 * Product for the order product manager
 *
 * @package OrderManager
 * @author Stephen Walker <stephen@itwebexperts.com>
 * @copyright Copyright (c) 2010, I.T. Web Experts
 */

class OrderProduct {
	protected $pInfo = array();
	protected $id = null;

	public function __construct($pInfo = null){
		$this->regenerateId();
		if (is_null($pInfo) === false){
			$this->pInfo = $pInfo;
			//$this->id = $pInfo['orders_products_id'];

			$this->productClass = new Product((int) $this->pInfo['products_id']);
			$this->pInfo['products_weight'] = $this->productClass->getWeight();

			$this->purchaseTypeClass = $this->productClass->getPurchaseType($this->pInfo['purchase_type']);
		}
	}

	public function regenerateId(){
		$this->id = tep_rand(5555, 99999);
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

	public function getIdString(){
		return $this->pInfo['orders_products_id'];
	}

	public function getPurchaseType(){
		return $this->pInfo['purchase_type'];
	}

	public function getTaxRate(){
		return $this->pInfo['products_tax'];
	}

	public function getStartDate(){
		if(isset($this->pInfo['reservationInfo']['start_date'])){
			return date('m/d/Y', strtotime($this->pInfo['reservationInfo']['start_date']));
		}

		return date('m/d/Y');

	}

	public function getStartTime(){
		if(isset($this->pInfo['reservationInfo']['start_date'])){
			return date('H', strtotime($this->pInfo['reservationInfo']['start_date']));
		}

		return sysConfig::get('EXTENSION_PAY_PER_RENTALS_START_TIME');
	}

	public function getEndDate(){
		if(isset($this->pInfo['reservationInfo']['end_date'])){
			return date('m/d/Y', strtotime($this->pInfo['reservationInfo']['end_date']));
		}

		return date('m/d/Y');
	}

	public function getEndTime(){
		if(isset($this->pInfo['reservationInfo']['end_date'])){
			return date('H', strtotime($this->pInfo['reservationInfo']['end_date']));
		}

		return sysConfig::get('EXTENSION_PAY_PER_RENTALS_START_TIME');
	}

	public function getQuantity(){
		return $this->pInfo['products_quantity'];
	}

	public function getModel(){
		return $this->pInfo['products_model'];
	}

	public function getName(){
		return $this->pInfo['products_name'];
	}

    public function isConsumption(){
        return $this->purchaseTypeClass->consumptionAllowed();
    }

    public function getBarcodeId2($i = 0){
        if ($this->pInfo['purchase_type'] == 'reservation'){
            if (!isset($this->pInfo['OrdersProductsReservation'][$i]['barcode_id'])){
                return false;
            }
            $bID = $this->pInfo['OrdersProductsReservation'][$i]['barcode_id'];
            return $bID;
        }else{
            if($i > 0){
                return false;
            }
        }
    }

    public function getBarcodeEdit2($i = 0){
        if ($this->pInfo['purchase_type'] == 'reservation'){
            $barcodes = count($this->pInfo['OrdersProductsReservation']);
            $content = 'Add Barcode '.htmlBase::newElement('icon')->setType('insert')->addClass('addBarcode')->draw().'<br/>';
            for($i=0;$i<$barcodes;$i++){
                $content = $content.'<div><input type="text" size="10" class="ui-widget-content barcodeName" name="product[' . $this->id . '][barcode]" barid="' . $this->getBarcodeId2($i) . '" value="' . $this->getBarcode($i) . '">'.htmlBase::newElement('icon')->setType('delete')->addClass('removeBarcode')->draw().'</div>';
                if($i + 1 < $barcodes)
                     $content = $content.'<br/>';
            }
            return $content;
        }else{
            if($i > 0){
                return false;
            }
        }
    }

	public function hasBarcode(){
		if ($this->pInfo['purchase_type'] == 'reservation'){
			return (!empty($this->pInfo['OrdersProductsReservation'][0]['barcode_id']));			
		}
		return (!empty($this->pInfo['barcode_id']));
	}

	public function getBarcode($i = 0){
		if ($this->pInfo['purchase_type'] == 'reservation'){
			if (!isset($this->pInfo['OrdersProductsReservation'][$i]['barcode_id'])){
				return false;
			}
			$bID = $this->pInfo['OrdersProductsReservation'][$i]['barcode_id'];
			$ProductsInventoryBarcodes = Doctrine_Core::getTable('ProductsInventoryBarcodes')->find($bID);
			if ($ProductsInventoryBarcodes){
				return $ProductsInventoryBarcodes->barcode;
			}
		}else{
			if($i > 0){
				return false;
			}
		}
		if(isset($this->pInfo['barcode_id'])){
			$ProductsInventoryBarcodes = Doctrine_Core::getTable('ProductsInventoryBarcodes')->find($this->pInfo['barcode_id']);
			if ($ProductsInventoryBarcodes){
				return $ProductsInventoryBarcodes->barcode;
			}
		}
		return false;

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

	public function getPrice($wTax = false){
		$price = $this->pInfo['products_price'];

		if ($wTax === true){
			$price = tep_add_tax($price, $this->getTaxRate());
		}
		return $price;
	}

	public function getWeight(){
		return $this->pInfo['products_weight'] * $this->getQuantity();
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

	public function getNameHtml($showExtraInfo = true) {
		$nameHref = htmlBase::newElement('a')
		->setHref(itw_catalog_app_link('products_id=' . $this->getProductsId(), 'product', 'info'))
		->css(array(
			'font-weight' => 'bold'
		))
		->attr('target', '_blank')
		->html($this->getName());

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

		$name .= $this->purchaseTypeClass->orderAfterProductName($this, $showExtraInfo);

		$Result = EventManager::notifyWithReturn('OrderProductAfterProductName', &$this, $showExtraInfo);
		foreach($Result as $html){
			$name .= $html;
		}

		return $name;
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
}
?>