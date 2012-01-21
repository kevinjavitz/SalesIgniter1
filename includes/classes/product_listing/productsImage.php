<?php
class productListing_productsImage {

	public function sortColumns(){
		$selectSortKeys = array(
		);
		return $selectSortKeys;
	}

	public function show(&$productClass){
		global $cPath;
		if (isset($_GET['manufacturers_id'])) {
			$addedGetVar = '&manufacturers_id=' . $_GET['manufacturers_id'];
		} else {
			$addedGetVar = ($cPath ? '&cPath=' . $cPath : '');
		}

		if ($productClass->hasImage()){
			$image = $productClass->getImage();
			EventManager::notify('ProductListingProductsImageShow', &$image, &$productClass);
			
			$imageHtml = htmlBase::newElement('image')
			->setSource($image)
			->setWidth(SMALL_IMAGE_WIDTH)
			->setHeight(SMALL_IMAGE_HEIGHT)
			->thumbnailImage(true);
			
			return '<a style="text-decoration:none;" href="' . htmlspecialchars(itw_app_link('products_id=' . $productClass->getID() . $addedGetVar, 'product', 'info')) . '">' . $imageHtml->draw() .'<br/><span style="color:red;text-decoration:none;">Click here to select product</span>' . '</a>';
		}
		return false;
	}
}
?>