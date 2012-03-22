<?php
/*
	Product Purchase Type: New

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

/**
 * New Purchase Type
 * @package ProductPurchaseTypes
 */
class PurchaseType_new extends PurchaseTypeAbstract {
	public $typeLong = 'new';
	public $typeName;
	public $typeShow;

	public function __construct($ProductCls, $forceEnable = false, $aID = false){

		$this->typeName = sysLanguage::get('PURCHASE_TYPE_NEW_NAME');
		$this->typeShow = sysLanguage::get('PURCHASE_TYPE_NEW_SHOW');

		$productInfo = $ProductCls->productInfo;
		$this->enabled = ($forceEnable === true ? true : (in_array($this->typeLong, $productInfo['typeArr'])));

		if ($this->enabled === true){
			$this->productInfo = array(
				'id'      => $productInfo['products_id'],
				'price'   => $productInfo['products_price'],
				'taxRate' => $productInfo['taxRate']
			);

			if (isset($productInfo['Specials']) && !empty($productInfo['Specials'])){
				$this->productInfo['special_price'] = $productInfo['Specials']['specials_new_products_price'];
			}

			EventManager::notify('PurchaseTypeAfterSetup', &$this->productInfo);

			$this->inventoryCls = new ProductInventory(
				$this->productInfo['id'],
				$this->typeLong,
				$productInfo['products_inventory_controller'],
				in_array('new', $productInfo['allow_overbooking']),
				$aID
			);
		}
	}

	public function processRemoveFromCart(){
		return null;
	}

	public function processAddToOrder(&$pInfo){
		$this->processAddToCart($pInfo);
	}

	public function processAddToCart(&$pInfo){
		if (isset($this->productInfo['special_price'])){
			$pInfo['price'] = $this->productInfo['special_price'];
			$pInfo['final_price'] = $this->productInfo['special_price'];
		}else{
			$pInfo['price'] = $this->productInfo['price'];
			$pInfo['final_price'] = $this->productInfo['price'];
		}
	}

	public function onInsertOrderedProduct($cartProduct, $orderId, &$orderedProduct, &$products_ordered){
	}

	public function getPurchaseHtml($key){
		$return = null;
		switch($key){
			case 'product_info':
				$button = htmlBase::newElement('button')
				->setType('submit')
				->setName('buy_' . $this->typeLong . '_product')
				->setText(sysLanguage::get('TEXT_BUTTON_BUY'));
				$pClass = new product($this->productInfo['id']);
				if ($this->hasInventory() === false || $pClass->isNotAvailable() ){
					$button->disable();
					if($pClass->isNotAvailable()){
						$button->setText(sysLanguage::get('TEXT_AVAILABLE').': '. strftime(sysLanguage::getDateFormat('short'), strtotime($pClass->getAvailableDate())));
					}else{
						$button->setText(sysLanguage::get('TEXT_OUT_OF_STOCK'));
					}
				}

				$content = htmlBase::newElement('span')
				->css(array(
					'font-size' => '1.5em',
					'font-weight' => 'bold'
				))
				->html($this->displayPrice());

				$return = array(
					'form_action'   => itw_app_link(tep_get_all_get_params(array('action'))),
					'purchase_type' => $this->typeLong,
					'allowQty'      => true,
					'header'        =>  sysLanguage::get('BUY_NEW'),
					'content'       => $content->draw(),
					'button'        => $button
				);
				break;
		}
		return $return;
	}
}
?>