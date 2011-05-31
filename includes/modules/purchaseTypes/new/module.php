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
class PurchaseType_new extends PurchaseTypeAbstract
{

	public function __construct($ProductCls, $forceEnable = false) {
		$this->setTitle('New');
		$this->setDescription('New Products In Retail Or Oem Packaging');

		$this->init('new', $ProductCls, $forceEnable);

		if ($this->isInstalled() === true){
			if ($this->isEnabled() === true){
				$this->setProductInfo('price', $ProductCls->productInfo['products_price']);

				if (isset($productInfo['Specials']) && !empty($productInfo['Specials'])){
					$this->setProductInfo('special_price', $ProductCls->productInfo['Specials']['specials_new_products_price']);
				}
			}
		}
	}

	public function processRemoveFromCart() {
		return null;
	}

	public function processAddToOrder(&$pInfo) {
		$this->processAddToCart($pInfo);
	}

	public function processAddToCart(&$pInfo) {
		if (isset($this->productInfo['special_price'])){
			$pInfo['price'] = $this->productInfo['special_price'];
			$pInfo['final_price'] = $this->productInfo['special_price'];
		}
		else {
			$pInfo['price'] = $this->productInfo['price'];
			$pInfo['final_price'] = $this->productInfo['price'];
		}
	}

	public function onInsertOrderedProduct($cartProduct, $orderId, &$orderedProduct, &$products_ordered) {
	}

	public function getPurchaseHtml($key) {
		$return = null;
		switch($key){
			case 'product_info':
				$button = htmlBase::newElement('button')
					->setType('submit')
					->setName('buy_' . $this->typeLong . '_product')
					->setText(sysLanguage::get('TEXT_BUTTON_BUY'));

				if ($this->hasInventory() === false){
					$button->disable();
				}

				$content = htmlBase::newElement('span')
					->css(array(
						'font-size' => '1.5em',
						'font-weight' => 'bold'
					))
					->html($this->displayPrice());

				$return = array(
					'form_action' => itw_app_link(tep_get_all_get_params(array('action'))),
					'purchase_type' => $this->typeLong,
					'allowQty' => true,
					'header' => 'Buy ' . $this->typeShow,
					'content' => $content->draw(),
					'button' => $button
				);
				break;
		}
		return $return;
	}
}

?>