<?php
/*
	Product Purchase Type: Download

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

/**
 * Download Purchase Type
 * @package ProductPurchaseTypes
 */
class PurchaseType_download extends PurchaseTypeAbstract
{

	public function __construct($ProductCls, $forceEnable = false) {
		$this->setTitle('Download');
		$this->setDescription('Downloadable Products Such As Movie/Pdf/Image');

		$this->init('download', $ProductCls, $forceEnable);

		if ($this->isInstalled() === true){
			if ($this->isEnabled() === true){
				$this->setProductInfo('price', $ProductCls->productInfo['products_price_download']);
			}
		}
	}

	private function showViewType() {
		$viewTypeHtml = htmlBase::newElement('span')
			->css(array(
				'font-size' => '.8em',
				'font-style' => 'italic'
			))
			->html(' - View Type: Download');

		return '<br />' . $viewTypeHtml->draw();
	}

	public function orderAfterProductName(&$orderedProduct) {
		return $this->showViewType();
	}

	public function orderAfterEditProductName(&$orderedProduct) {
		return $this->showViewType();
	}

	public function checkoutAfterProductName(&$cartProduct) {
		if ($cartProduct->hasInfo('download_type')){
			if ($cartProduct->getInfo('download_type') == 'download'){
				return $this->showViewType();
			}
		}
		return '';
	}

	public function shoppingCartAfterProductName(&$cartProduct) {
		if ($cartProduct->hasInfo('download_type')){
			if ($cartProduct->getInfo('download_type') == 'download'){
				return $this->showViewType();
			}
		}
		return '';
	}

	public function processRemoveFromCart() {
		return null;
	}

	public function updateStock($orderId, $orderProductId, &$cartProduct) {
		return false;
	}

	public function processAddToOrder(&$pInfo) {
		$this->processAddToCart($pInfo);
	}

	public function processAddToCart(&$pInfo) {
		$pInfo['price'] = $this->productInfo['price'];
		$pInfo['final_price'] = $this->productInfo['price'];
	}

	public function onInsertOrderedProduct($cartProduct, $orderId, &$orderedProduct, &$products_ordered) {
		$Qdownloads = Doctrine_Query::create()
			->from('ProductsDownloads')
			->where('products_id = ?', (int)$cartProduct->getIdString())
			->execute();

		if ($Qdownloads->count() > 0){
			foreach($Qdownloads->toArray() as $dInfo){
				$Download = new OrdersProductsDownload();
				$Download->orders_id = $orderId;
				$Download->download_id = $dInfo['download_id'];
				$Download->download_maxdays = sysConfig::get('EXTENSION_DOWNLOADPRODUCTS_MAX_DAYS');
				$Download->download_maxcount = sysConfig::get('EXTENSION_DOWNLOADPRODUCTS_MAX_COUNT');
				$Download->download_count = '0';

				$orderedProduct->OrdersProductsDownload->add($Download);
			}
			$orderedProduct->save();
		}
	}

	public function hasInventory() {
		return true;
	}

	public function canUseSpecial() {
		return false;
	}

	public function getPurchaseHtml($key) {
		$return = null;

		$headerInfo = htmlBase::newElement('a')
			->setHref(itw_app_link('appExt=infoPages&dialog=true', 'show_page', 'help_download'))
			->attr('onclick', 'popupWindow(this.href, \'400\', \'400\');return false;')
			->html(tep_image(DIR_WS_TEMPLATES . 'images/icon_help.png'));

		switch($key){
			case 'product_info':
				$headerInfo->addClass('infoPopupIcon');
				$button = htmlBase::newElement('button')
					->setType('submit')
					->setName('buy_' . $this->typeLong . '_product')
					->setText(sysLanguage::get('TEXT_BUTTON_BUY'));

				$content = htmlBase::newElement('span')
					->css(array(
						'font-size' => '1.5em',
						'font-weight' => 'bold'
					))
					->html($this->displayPrice());

				$return = array(
					'form_action' => itw_app_link(tep_get_all_get_params(array('action'))),
					'purchase_type' => $this->typeLong,
					'allowQty' => false,
					'header' => $headerInfo->draw() . 'Buy ' . $this->typeShow,
					'content' => $content->draw(),
					'button' => $button
				);
				break;
			case 'product_listing_row':
				$button = htmlBase::newElement('button')
					->setText(sysLanguage::get('TEXT_BUTTON_BUY_NOW'))
					->setHref(itw_app_link(tep_get_all_get_params(array('action', 'products_id')) . 'action=buy_' . $this->typeLong . '_product&products_id=' . $this->productInfo['id']), true);

				$return = '<tr>' .
					'<td class="main">' . $headerInfo->draw() . '</td>' .
					'<td class="main">' . $this->typeShow . ':</td>' .
					'<td class="main">' . $this->displayPrice() . '</td>' .
					'<td class="main" style="font-size:.7em;">' . $button->draw() . '</td>' .
					'</tr>';
				break;
		}
		return $return;
	}
}

?>