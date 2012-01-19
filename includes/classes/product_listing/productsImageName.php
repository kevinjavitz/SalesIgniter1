<?php
class productListing_productsImageName {

	public function sortColumns(){
		$selectSortKeys = array(
								array(
									'value' => 'pd.products_name',
									'name'  => sysLanguage::get('PRODUCT_LISTING_NAME')
								)
		);
		return $selectSortKeys;
	}

	public function show(&$productClass, &$purchaseTypesCol){
		global $cPath;
		if (isset($_GET['manufacturers_id'])) {
			$addedGetVar = '&manufacturers_id=' . $_GET['manufacturers_id'];
		} else {
			$addedGetVar = ($cPath ? '&cPath=' . $cPath : '');
		}
		$retuned = '<div style="text-align:center;width:110px;">';
		$products_series = '';
		if ($productClass->hasImage()){
			$image = $productClass->getImage();
			EventManager::notify('ProductListingProductsImageNameShow', &$image, &$productClass);

			$imageHtml = htmlBase::newElement('image')
			->setSource($image)
			->setWidth(sysConfig::get('SMALL_IMAGE_WIDTH'))
			->setHeight(sysConfig::get('SMALL_IMAGE_HEIGHT'))
			->thumbnailImage(true);
			if (isset($_GET['manufacturers_id'])) {
				$addedGetVar = '&manufacturers_id=' . $_GET['manufacturers_id'];
			} else {
				$addedGetVar = ($cPath ? '&cPath=' . $cPath : '');
			}

	//		if ($includeBoxInfo === true){
				if ($productClass->isInBox()){
					$products_series ='<br /><small><i>'.sprintf(
					sysLanguage::get('TEXT_BS_SERIES'),
					$productClass->getDiscNumber($productClass->getID()),
					$productClass->getTotalDiscs(),
					htmlspecialchars($productClass->getBoxName())
					) . '</i></small>';
				}
	//		}
			$ratingsBar = rating_bar($productClass->getName(), $productClass->getID());

			$retuned .=  '<a href="' . htmlspecialchars(itw_app_link('products_id=' . $productClass->getID() . $addedGetVar, 'product', 'info')) . '">' . $imageHtml->draw() . '</a><br/>';
		}
		$retuned .= '<a href="' . htmlspecialchars(itw_app_link('products_id=' . $productClass->getID() . $addedGetVar, 'product', 'info')) . '">' . htmlspecialchars($productClass->getName()) . '</a>' . $products_series .'</div>';
		return $retuned;
	}
}
?>