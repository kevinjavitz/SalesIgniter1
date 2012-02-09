<?php
	ob_start();

	$Qcategory = Doctrine_Query::create()
		->from('Categories c')
		->leftJoin('c.CategoriesDescription cd')
		->where('c.categories_id = ?', (int)$current_category_id)
		->andWhere('cd.language_id = ?', (int)Session::get('languages_id'))
		->orderBy('cd.categories_name')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

     //print_r($Qcategory);

	$pageTitle = $Qcategory[0]['CategoriesDescription'][0]['categories_htc_title_tag'];
	if (tep_not_null($Qcategory[0]['CategoriesDescription'][0]['categories_description'])){
		echo '<div class="pageHeadingSub">' . $Qcategory[0]['CategoriesDescription'][0]['categories_description'] . '</div><br />';
	}

	EventManager::notify('IndexNestedListingBeforeListing');

	$Qchildren = Doctrine_Query::create()
		->from('Categories c')
		->leftJoin('c.CategoriesDescription cd')
		->where('c.parent_id = ?', (int)$current_category_id)
		->andWhere('cd.language_id=?', Session::get('languages_id'))
		->orderBy('cd.categories_name')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	if ($Qchildren){
		$categoryTable = htmlBase::newElement('table')
			->setCellPadding(2)
			->setCellSpacing(0)
			->css(array(
				'width' => '100%'
			));

		$col = 0;
		$tableColumns = array();
		foreach($Qchildren as $cInfo){
			$categoryId = $cInfo['CategoriesDescription'][0]['categories_seo_url'];
			$categoryImage = $cInfo['categories_image'];
			$categoryName = $cInfo['CategoriesDescription'][0]['categories_name'];
			$img = '';
			if(!empty($categoryImage)) {
				$img = '<img src="imagick_thumb.php?path=rel&imgSrc=' . 'images/'. $categoryImage . '&width=180&height=180" alt="' . $categoryName . '" />' ;
			}
			$tableColumns[] = array(
					'addCls' => 'main catName',
					'align' => 'center',
					'text' => '<a href="' .  itw_app_link(null, 'index', $categoryId) . '">' .
						$img .
						'<br />' . $categoryName . '</a>'
			);

			$col++;
			if ($col > sysConfig::get('MAX_DISPLAY_CATEGORIES_PER_ROW')){
				$categoryTable->addBodyRow(array(
						'columns' => $tableColumns
					));
				$tableColumns = array();
				$col = 0;
			}
		}
		if (sizeof($tableColumns) > 0){
			$categoryTable->addBodyRow(array(
					'columns' => $tableColumns
				));
		}
		echo $categoryTable->draw() . '<br />';
	}
	EventManager::notify('IndexNestedListingAfterListing');

	echo '<div>';

	echo '</div>';

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
//$qp = $Qproducts->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
//print_r($qp);
//itwExit();
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
