<?php
	$Qfeatured = Doctrine_Query::create()
	->select('p.products_id')
	->from('Products p')
	->leftJoin('p.ProductsDescription pd')
	->leftJoin('p.ProductsToBox p2b')
	->where('p.products_status = ?', '1')
	->andWhere('p.is_hidden = ?', '0')
	->andWhere('p.products_featured = ?', '1')
	->andWhere('p2b.products_id is null')
	->andWhere('pd.language_id = ?', (int)Session::get('languages_id'));

 	EventManager::notify('ProductListingQueryBeforeExecute', &$Qfeatured);

	if(sysConfig::get('PRODUCT_LISTING_TYPE') == 'row'){
		$productListing = new productListing_row();
	} else {
		$productListing = new productListing_col();
	}
	$productListing->setQuery($Qfeatured);
	
	$pageContent->set('pageTitle', sysLanguage::get('HEADING_TITLE_FEATURED'));
	$pageContent->set('pageContent', $productListing->draw());
