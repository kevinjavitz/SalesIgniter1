<?php
require(sysConfig::getDirFsCatalog() . 'extensions/orderCreator/admin/classes/product/Base.php');

class OrderCreatorProduct extends OrderProduct implements Serializable {

	public function __construct($pInfo = null){
		parent::__construct();
		if (is_null($pInfo) === false){
			$this->pInfo = $pInfo;
			$this->productClass = new OrderCreatorProductProduct((int) $this->pInfo['products_id']);
			$this->pInfo['products_weight'] = $this->productClass->getWeight();
			$this->purchaseTypeClass = $this->productClass->getPurchaseType($this->pInfo['purchase_type']);
			$this->purchaseTypeClass->processAddToOrder($this->pInfo);

			EventManager::notify('OrderEditorProductAddToCart', $this->pInfo, $this->productClass, $this->purchaseTypeClass);
		}
	}

	public function init(){
		$this->productClass = new OrderCreatorProductProduct((int) $this->pInfo['products_id']);
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
	
	public function setProductsId($pID){
		global $Editor;
		$this->pInfo['products_id'] = $pID;
		$this->productClass = new OrderCreatorProductProduct($pID);
		$this->pInfo['products_name'] = $this->productClass->getName();
		$this->pInfo['products_weight'] = $this->productClass->getWeight();
		$this->pInfo['products_model'] = $this->productClass->getModel();
		
		$taxAddress = $Editor->AddressManager->getAddress('billing');
		$this->setTaxRate(tep_get_tax_rate(
			$this->productClass->getTaxClassId(),
			(is_object($taxAddress) ? $taxAddress->getCountryId() : -1),
				(is_object($taxAddress) ? $taxAddress->getZoneId() : -1)
		));
	}
	
	public function setPInfo($pInfo){
		$this->pInfo = $pInfo;
	}
	
	public function getPInfo(){
	    return $this->pInfo;
	}
	
	public function setPurchaseType($val){
		$this->pInfo['purchase_type'] = $val;
		
		$this->purchaseTypeClass = $this->productClass->getPurchaseType($val);

		$this->pInfo['products_price'] = $this->purchaseTypeClass->getPrice();
		$this->pInfo['final_price'] = $this->purchaseTypeClass->getPrice();

		$this->purchaseTypeClass->processAddToOrder(&$this->pInfo);
		
		EventManager::notify('OrderEditorProductAddToCart', $this->pInfo, $this->productClass, $this->purchaseTypeClass);
	}

	public function getProductsBarcode(){
		if($this->purchaseTypeClass->getTrackMethod() == 'barcode'){
			return $this->purchaseTypeClass->inventoryCls->getInventoryItems($this->purchaseTypeClass->typeLong);
		}else{
			return array();
		}
	}

	public function setTaxRate($val){
		$this->pInfo['products_tax'] = $val;
	}

	public function setQuantity($val){
		$this->pInfo['products_quantity'] = $val;
	}
	
	public function setPrice($val){
		$this->pInfo['products_price'] = $val;
		$this->pInfo['final_price'] = $val;
	}

	public function setBarcodeId($val){
		$this->pInfo['barcode_id'] = $val;
	}

	public function getBarcodeId(){
		return $this->pInfo['barcode_id'];
	}

	public function hasBarcodeId(){
		return (isset($this->pInfo['barcode_id']));
	}

	public function getTaxRateEdit(){
		return '<input type="text" size="5" class="ui-widget-content taxRate" name="product[' . $this->id . '][tax_rate]" value="' . $this->getTaxRate() . '">%';
	}

	public function getStartDateEdit(){
		return '<input type="text"  class="ui-widget-content start_date" name="product[' . $this->id . '][start_date]" value="' . $this->getStartDate() . '">';
	}

	public function getEndDateEdit(){
		return '<input type="text"  class="ui-widget-content end_date" name="product[' . $this->id . '][end_date]" value="' . $this->getEndDate() . '">';
	}

	public function getStartTimeEdit(){
		$startTime = htmlBase::newElement('selectbox')
		->setName('product[' . $this->id . '][start_time]')
		->addClass('start_time');


		for($i = sysConfig::get('EXTENSION_PAY_PER_RENTALS_START_TIME');$i <= sysConfig::get('EXTENSION_PAY_PER_RENTALS_END_TIME');$i++){
			if($i == 12){
				$startTime->addOption($i,'12:00 PM');
			}else{
				$startTime->addOption($i,($i % 12) . ($i<12?':00 AM':':00 PM'));
			}
		}
		$startTime->selectOptionByValue($this->getStartTime());
		return $startTime->draw();
		//return '<input type="text"  class="ui-widget-content start_date" name="product[' . $this->id . '][start_date]" value="' . $this->getStartDate() . '">';
	}

	public function getEndTimeEdit(){
		$endTime = htmlBase::newElement('selectbox')
				->setName('product[' . $this->id . '][end_time]')
				->addClass('end_time');


		for($i = sysConfig::get('EXTENSION_PAY_PER_RENTALS_START_TIME');$i <= sysConfig::get('EXTENSION_PAY_PER_RENTALS_END_TIME');$i++){
			if($i == 12){
				$endTime->addOption($i,'12:00 PM');
			}else{
				$endTime->addOption($i,($i % 12) . ($i<12?':00 AM':':00 PM'));
			}
		}

		$endTime->selectOptionByValue($this->getEndTime());
		return $endTime->draw();
		//return '<input type="text"  class="ui-widget-content start_date" name="product[' . $this->id . '][start_date]" value="' . $this->getStartDate() . '">';
	}



	public function getBarcodeEdit(){
        $content = $this->getBarcodeEdit2(0);
        if($content){
             return '<input type="text" size="10" class="ui-widget-content barcodeName" name="product[' . $this->id . '][barcode]" barid="" value="">';
        }
        else{
            return '<input type="text" size="10" class="ui-widget-content barcodeName" name="product[' . $this->id . '][barcode]" barid="' . $this->getBarcodeId2() . '" value="' . $this->getBarcode() . '">';
        }


    }

    public function getBarcodeEditRemove(){
        return '<input type="text" size="10" class="ui-widget-content barcodeName" name="product[' . $this->id . '][barcode]" barid="' . $this->getBarcodeId2() . '" value="' . $this->getBarcode() . '">'.htmlBase::newElement('icon')->setType('delete')->addClass('removeBarcode')->draw();
    }


	public function getPriceEdit($incQty = false, $incTax = false){
		global $Editor, $currencies;
		$html = '';
		if ($incQty === false && $incTax === false){
			$html = '<input type="text" size="5" class="ui-widget-content priceEx" name="product[' . $this->id . '][price]" value="' . $this->getFinalPrice($incQty, $incTax) . '">';
		}elseif ($incQty === true && $incTax === false){
			$html = '<b class="priceExTotal">' . $currencies->format($this->getFinalPrice($incQty, $incTax), true, $Editor->getCurrency(), $Editor->getCurrencyValue()) . '</b>';
		}elseif ($incQty === false && $incTax === true){
			$html = '<b class="priceIn">' . $currencies->format($this->getFinalPrice($incQty, $incTax), true, $Editor->getCurrency(), $Editor->getCurrencyValue()) . '</b>';
		}elseif ($incQty === true && $incTax === true){
			$html = '<b class="priceInTotal">' . $currencies->format($this->getFinalPrice($incQty, $incTax), true, $Editor->getCurrency(), $Editor->getCurrencyValue()) . '</b>';
		}
		return $html;
	}

	public function getQuantityEdit(){
		return '<input type="number" size="3" class="ui-widget-content productQty" name="product[' . $this->id . '][qty]" value="' . $this->getQuantity() . '">';
	}

	public function getNameEdit($excludedPurchaseTypes = array()){
		global $typeNames;
		$productsName = '<span class="productName">'.$this->getName().'</span>';

		if ($this->getPurchaseType() != 'membership'){
			$PurchaseTypes = Doctrine_Core::getTable('Products')
			->getRecordInstance()
			->getPurchaseTypes((int) $this->getProductsId(), true);

			$purchaseTypeInput = htmlBase::newElement('selectbox')
			->addClass('ui-widget-content purchaseType')
			->setName('product[' . $this->id . '][purchase_type]');
			foreach($PurchaseTypes as $typeName){
				//if (!in_array($typeName, $excludedPurchaseTypes)){
					$attr = array();
					$purchaseTypeInput->addOptionWithAttributes($typeName, $typeNames[$typeName],$attr);
				//}
			}
			$purchaseTypeInput->selectOptionByValue($this->getPurchaseType());
			//$productsName .= ' ' . $purchaseTypeInput->draw() . '';
		}

		$productsName .= $this->purchaseTypeClass->orderAfterEditProductName($this);

		$contents = EventManager::notifyWithReturn('OrderProductAfterProductNameEdit', $this);
		foreach($contents as $content){
			$productsName .= $content;
		}
		return $productsName;
	}

	public function updateInfo($newInfo){
		$newProductInfo = $this->pInfo;
		foreach($newInfo as $k => $v){
			$newProductInfo[$k] = $v;
		}
		$this->pInfo = $newProductInfo;
		$this->purchaseTypeClass->processUpdateCart(&$this->pInfo);
	}

	public function onAddToCollection(&$OrderedProduct){
		$this->purchaseTypeClass->addToOrdersProductCollection($this, $OrderedProduct);
		
		EventManager::notify('OrderCreatorProductAddToCollection', $this, $OrderedProduct);
	}
}
?>