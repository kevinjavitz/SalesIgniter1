<?php
class productListing_productsPriceStream {

	public function sortColumns(){
		$selectSortKeys = array(
			array(
				'value' => 'p.price_stream',
				'name'  => sysLanguage::get('PRODUCT_LISTING_PRICE_STREAM')
			)
		);
		return $selectSortKeys;
	}

	public function show(&$productClass){
		$purchaseTypeStream = $productClass->getPurchaseType('stream', true);
		if ($purchaseTypeStream->hasInventory()){
			$buyNowButton = htmlBase::newElement('button')
			->setText(sysLanguage::get('TEXT_BUTTON_BUY_NOW'))
			->setHref(itw_app_link(tep_get_all_get_params(array('action', 'products_id')) . 'action=buy_stream_product&products_id=' . $productClass->getID()), true);

			if ($productClass->isNotAvailable() ){
				$buyNowButton->disable();
				$buyNowButton->setText(sysLanguage::get('TEXT_AVAILABLE').': '. strftime(sysLanguage::getDateFormat('short'), strtotime($productClass->getAvailableDate())));
			}
			return '<a href="Javascript:void(0)" onclick="popupWindow(\'' . itw_app_link('appExt=infoPages&dialog=true', 'show_page', 'help_stream') . '\', 300, 300)">' . tep_image(DIR_WS_TEMPLATES . 'images/icon_help.png') . '</a>:' . $purchaseTypeStream->displayPrice() . '<br />' . $buyNowButton->draw();
		}
		return false;
	}
}
?>