<?php
	$sortingSettings = array(
		'actor' => array(
			'select' => '(SELECT sqt2.value FROM ProductsCustomFields sqt1 LEFT JOIN sqt1.ProductsCustomFieldsToProducts sqt2 WHERE sqt2.product_id = p.products_id AND sqt1.search_key="actor") as Actor',
			'orderby' => 'Actor ASC'
		)
	);
	
	$Qproducts = Doctrine_Query::create()
	->select('p.products_id')
	->from('Products p')
	->leftJoin('p.ProductsDescription pd')
	->leftJoin('p.ProductsToBox p2b')
	->where('p.products_status = ?', '1')
	->andWhere('p2b.products_id is null')
	->andWhere('pd.language_id = ?', (int)Session::get('languages_id'));

	EventManager::notify('ProductListingQueryBeforeExecute', &$Qproducts);
	
	if (isset($_GET['sortBy'])){
		$Qproducts->addSelect($sortingSettings[$_GET['sortBy']]['select']);
		//$Qproducts->andWhere($sortingSettings[$_GET['sortBy']]['where']);
		$Qproducts->orderBy($sortingSettings[$_GET['sortBy']]['orderby']);
	}
	if(sysConfig::get('PRODUCT_LISTING_TYPE') == 'row'){
		$productListing = new productListing_row();
	} else {
		$productListing = new productListing_col();
	}
	$productListing->setQuery($Qproducts);
	
	$pageContent->set('pageTitle', sysLanguage::get('HEADING_TITLE_ALL'));
	$pageContent->set('pageContent', $productListing->draw());
