<?php

if ($category_depth == 'products' || isset($_GET['manufacturers_id'])) {
	ob_start();
	$new_products_category_id = $current_category_id; //For new products module
	if (RENTAL_SHOW_FEATURED_TITLE == 'true'){
		$QfeaturedProducts = Doctrine_Query::create()
		->select('p.products_id')
		->from('Products p')
		->leftJoin('p.ProductsDescription pd')
		->leftJoin('p.ProductsToBox p2b')
		->where('p.products_status = ?', '1')
		->andWhere('p.products_featured = ?', '1')
		->andWhere('p2b.products_id is null')
		->andWhere('pd.language_id = ?', (int)Session::get('languages_id'))
		->limit(10)
		->orderBy('RAND()');

		if (isset($current_category_id) && $current_category_id > 0){
			$QfeaturedProducts->leftJoin('p.ProductsToCategories p2c')
			->andWhere('p2c.categories_id = ?', (int)$current_category_id);
		}

		// create column list
		if(sysConfig::get('PRODUCT_LISTING_TYPE') == 'row'){
			$featuredProductListing = new productListing_row();
		} else {
			$featuredProductListing = new productListing_col();
		}
		$featuredProductListing->disableSorting()->disablePaging()->dontShowWhenEmpty()
		->setQuery($QfeaturedProducts);

		if (($content = $featuredProductListing->draw()) !== false){
			echo '<div class="pageHeading">' . sysLanguage::get('PAGE_HEADING_FEATURED') . '</div><br />' . $content . '<br />';
		}
	}

	$Qcategory = Doctrine_Query::create()
	->select('c.categories_image, cd.categories_name, cd.categories_description')
	->from('Categories c')
	->leftJoin('c.CategoriesDescription cd')
	->where('c.categories_id = ?', $current_category_id)
	->andWhere('cd.language_id = ?', Session::get('languages_id'));

	EventManager::notify('CategoryQueryBeforeExecute', &$Qcategory);

	$Result = $Qcategory->execute(array(), Doctrine::HYDRATE_ARRAY);
	$categoryName = $Result[0]['CategoriesDescription'][0]['categories_name'];
	$categoryDescription = $Result[0]['CategoriesDescription'][0]['categories_description'];
	$categoryImage = $Result[0]['categories_image'];

	$pageTitle = $categoryName;
	if (!empty($categoryImage)){
		//echo '<div>' . tep_image(DIR_WS_IMAGES . $categoryImage, $categoryName) . '</div>';
	}
	if (!empty($categoryDescription)){
		echo '<div class="pageHeadingSub">' . $categoryDescription . '</div>';
	}

	$Qchildren = Doctrine_Query::create()
	->select('c.categories_id, cd.categories_name, c.categories_image')
	->from('Categories c')
	->leftJoin('c.CategoriesDescription cd')
	->where('c.parent_id = ?', (int)$current_category_id)
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if ($Qchildren){
		echo '<table border="0" width="100%" cellspacing="0" cellpadding="2"><tr>';
		$col = 0;
		foreach($Qchildren as $cInfo){
			$categoryId = $cInfo['categories_id'];
			$categoryImage = $cInfo['categories_image'];
			if(!empty($categoryImage)){
				$categoryImageLink = '<img src="imagick_thumb.php?path=rel&imgSrc=' . 'images/'. $categoryImage . '&width=100&height=100" alt="' . $categoryName . '" />' . '<br />';
			}

			$categoryName = $cInfo['CategoriesDescription'][0]['categories_name'];

			$cPath_new = tep_get_path($categoryId);
			$width = (int)(100 / sysConfig::get('MAX_DISPLAY_CATEGORIES_PER_ROW')) . '%';
			echo '  <td align="center" class="smallText" width="' . $width . '" valign="top"><a href="' . itw_app_link($cPath_new, 'index', 'default') . '">' .
			$categoryImageLink . $categoryName .'</a></td>' . "\n";

			$col++;
			if ($col >= sysConfig::get('MAX_DISPLAY_CATEGORIES_PER_ROW')){
				echo ' </tr>' . "\n";
				echo ' <tr>' . "\n";
				$col = 0;
			}
		}
		echo'</tr></table><br />';
	}
	$Qproducts = Doctrine_Query::create()
	->select('p.products_id')
	->from('Products p')
	->leftJoin('p.ProductsDescription pd')
	->leftJoin('p.ProductsToBox p2b')
	->where('p.products_status = ?', '1')
	//->andWhere('p.products_featured = ?', '0')
	->andWhere('p2b.products_id is null')
	->andWhere('pd.language_id = ?', (int)Session::get('languages_id'));

	if (isset($_GET['manufacturers_id'])){
		$Qproducts->addFrom('p.Manufacturers m')->andWhere('m.manufacturers_id = ?', (int)$_GET['manufacturers_id']);

		if (isset($_GET['filter_id']) && tep_not_null($_GET['filter_id'])){
			$Qproducts->leftJoin('p.ProductsToCategories p2c')->andWhere('p2c.categories_id = ?', (int)$_GET['filter_id']);
		}
	}else{
		$Qproducts->leftJoin('p.ProductsToCategories p2c')->andWhere('p2c.categories_id = ?', (int)$current_category_id);

		if (isset($_GET['filter_id']) && tep_not_null($_GET['filter_id'])){
			$Qproducts->addFrom('Manufacturers m')->andWhere('m.manufacturers_id = ?', (int)$_GET['filter_id']);;
		}else{
			$Qproducts->leftJoin('p.Manufacturers m');
		}
	}

	if (isset($ids)){
		$Qproducts->andWhereNotIn('p.products_id', $ids);
	}

	if (isset($_GET['sort'])){
		$column = substr($_GET['sort'], 0, strpos($_GET['sort'], '_'));
		$order = substr($_GET['sort'], -1);
		switch($column){
			case 'price':
				if ($order == 'd'){
					$Qproducts->orderBy('p.products_price desc');
				}else{
					$Qproducts->orderBy('p.products_price asc');
				}
				break;
			case 'name':
				if ($order == 'd'){
					$Qproducts->orderBy('pd.products_name desc');
				}else{
					$Qproducts->orderBy('pd.products_name asc');
				}
				break;
		}
	}

	EventManager::notify('ProductListingQueryBeforeExecute', &$Qproducts);

	$contents = EventManager::notifyWithReturn('IndexProductsListingBeforeListing', $current_category_id);
	if (!empty($contents)){
		foreach($contents as $content){
			echo $content;
		}
	}

	if(sysConfig::get('PRODUCT_LISTING_TYPE') == 'row'){
		$productListing = new productListing_row();
	} else {
		$productListing = new productListing_col();
	}

	$productListing->setQuery(&$Qproducts);
	echo $productListing->draw();

	$contents = EventManager::notifyWithReturn('IndexProductsListingAfterListing', $current_category_id);
	if (!empty($contents)){
		foreach($contents as $content){
			echo $content;
		}
	}
	
	$pageContents = ob_get_contents();
	ob_end_clean();

	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
}
