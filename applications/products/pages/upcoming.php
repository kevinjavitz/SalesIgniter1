<?php
	$QproductsUpcoming = Doctrine_Query::create()
	->select('p.products_id')
	->from('Products p')
	->leftJoin('p.ProductsDescription pd')
	->leftJoin('p.ProductsToBox p2b')
	->where('p.products_status = ?', '1')
	->andWhere('p.is_hidden = ?', '0')
	->andWhere('p.products_date_available > now()')
	->andWhere('p2b.products_id is null')
	->andWhere('pd.language_id = ?', (int)Session::get('languages_id'))
	->orderBy('p.products_date_added DESC, pd.products_name');

	if(sysConfig::get('PRODUCT_LISTING_TYPE') == 'row'){
		$productListing = new productListing_row();
	} else {
		$productListing = new productListing_col();
	}
	$productListing->setQuery($QproductsUpcoming);
	
	$pageContent->set('pageTitle', sysLanguage::get('HEADING_TITLE_NEW'));
	$pageContent->set('pageContent', $productListing->draw());
