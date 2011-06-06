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

	public function __construct($ProductCls = false, $forceEnable = false) {
		$this->setTitle('New');
		$this->setDescription('New Products In Retail Or Oem Packaging');

		$this->init('new', $ProductCls, $forceEnable);

		if ($this->isEnabled() === true){
			if ($ProductCls !== false){
				$this->setProductInfo('price', $ProductCls->productInfo['products_price']);

				EventManager::notify('PurchaseTypeConstruct', $this->getCode(), $ProductCls, $this->configData);
			}
		}
	}

	public function getPriceFromQuery($ProductQuery){
		return $ProductQuery['products_price'];
	}

	public function processRemoveFromCart() {
		return null;
	}

	public function processAddToOrder(&$pInfo) {
		$this->processAddToCart($pInfo);
	}

	public function processAddToCart(&$pInfo) {
		$pInfo['price'] = $this->productInfo['price'];
		$pInfo['final_price'] = $this->productInfo['price'];

		EventManager::notify('PurchaseTypeAddToCart', $this->getCode(), &$pInfo, $this->productInfo);
	}

	public function onInsertOrderedProduct($cartProduct, $orderId, &$orderedProduct, &$products_ordered) {
	}

	public function getPurchaseHtml($key) {
		$return = null;
		switch($key){
			case 'product_info':
				$button = htmlBase::newElement('button')
					->setType('submit')
					->setName('buy_' . $this->getCode() . '_product')
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
					'purchase_type' => $this->getCode(),
					'allowQty' => true,
					'header' => $this->getTitle(),
					'content' => $content->draw(),
					'button' => $button
				);
				break;
		}
		return $return;
	}
}

?>