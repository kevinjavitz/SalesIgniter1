<?php
class productListing_productsImage {

	public function sortColumns(){
		$selectSortKeys = array(
		);
		return $selectSortKeys;
	}

	public function show(&$productClass, &$purchaseTypesCol){
		global $cPath;

		$addedGetVar = ($cPath ? '&cPath=' . $cPath : '');

		if ($productClass->hasImage()){
			$image = $productClass->getImage();
			EventManager::notify('ProductListingProductsImageShow', &$image, &$productClass);
			
			$imageHtml = htmlBase::newElement('image')
			->setSource($image)
			->setWidth(sysConfig::get('SMALL_IMAGE_WIDTH'))
			->setHeight(sysConfig::get('SMALL_IMAGE_HEIGHT'))
			->thumbnailImage(true);
			
			return '<a style="text-decoration:none;" href="' . htmlspecialchars(itw_app_link('products_id=' . $productClass->getID() . $addedGetVar, 'product', 'info')) . '">' . $imageHtml->draw() .'<br/><span style="color:red;text-decoration:none;">'.sysLanguage::get('TEXT_CLICK_HERE_IMAGE').'</span>' . '</a>';
		}
		return false;
	}
}
?>