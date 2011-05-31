<?php
/*
	Product Purchase Type: Rental

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

/**
 * Rental Membership Stream Purchase Type
 * @package ProductPurchaseTypes
 */
class PurchaseType_Rental extends PurchaseTypeAbstract
{

	public function __construct($ProductCls, $forceEnable = false) {
		$this->setTitle('Rental');
		$this->setDescription('Rentals Which Mimic A Retail Rental Store');

		$this->init('rental', $ProductCls, $forceEnable);

		if ($this->isInstalled() === true){
			if ($this->isEnabled() === true){
				$this->setProductInfo('price', $ProductCls->productInfo['products_price_rental']);
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
		$pInfo['price'] = $this->productInfo['price'];
		$pInfo['final_price'] = $this->productInfo['price'];
	}

	public function hasInventory() {
		return true;
	}

	public function canUseSpecial() {
		return false;
	}

	public function updateStock($orderId, $orderProductId, &$cartProduct) {
		return false;
	}

	public function getPurchaseHtml($key) {
		$return = null;
		switch($key){
			case 'product_info':
				$button = htmlBase::newElement('button')
					->setType('submit')
					->setName('rent_product')
					->setText(sysLanguage::get('TEXT_BUTTON_RENT'));

				$return = array(
					'form_action' => itw_app_link(tep_get_all_get_params(array('action'))),
					'purchase_type' => $this->getCode(),
					'allowQty' => false,
					'header' => $this->getTitle(),
					'content' => '',
					'button' => $button
				);
				break;
		}
		return $return;
	}
}

?>