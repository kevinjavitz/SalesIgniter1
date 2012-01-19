<?php
class productListing_productsPriceDownload {
	public function sortColumns(){
		$selectSortKeys = array(
								array(
									'value' => 'p.price_download',
									'name'  => sysLanguage::get('PRODUCT_LISTING_PRICE_DOWNLOAD')
								)

		);
		return $selectSortKeys;
	}
	public function show(&$productClass, &$purchaseTypesCol){
		$purchaseTypeDownload = $productClass->getPurchaseType('download', true);
		if ($purchaseTypeDownload->hasInventory()){
			$buyNowButton = htmlBase::newElement('button')
			->setText(sysLanguage::get('TEXT_BUTTON_BUY_NOW'))
			->setHref(itw_app_link(tep_get_all_get_params(array('action', 'products_id')) . 'action=buy_download_product&products_id=' . $productClass->getID()), true);
			$purchaseTypesCol = 'download';
			if ($productClass->isNotAvailable() ){
				$purchaseTypesCol = '';
				$buyNowButton->disable();
				$buyNowButton->setText(sysLanguage::get('TEXT_AVAILABLE').': '. strftime(sysLanguage::getDateFormat('short'), strtotime($productClass->getAvailableDate())));
			}
			return '<a href="Javascript:void(0)" onclick="popupWindow(\'' . itw_app_link('appExt=infoPages&dialog=true', 'show_page', 'help_download') . '\', 300, 300)">' . tep_image(DIR_WS_TEMPLATES . 'images/icon_help.png') . '</a>:' . $purchaseTypeDownload->displayPrice() . '<br />' . $buyNowButton->draw();
		}
		return false;
	}
}
?>