<?php
/*
	Product Specials Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/
	$Qproducts = Doctrine_Query::create()
	->select('p.products_id')
	->from('Products p')
	->leftJoin('p.Specials s')
	->leftJoin('p.ProductsDescription pd')
	->leftJoin('p.ProductsToBox p2b')
	->where('p.products_status = ?', '1')
	->andWhere('p2b.products_id is null')
	->andWhere('pd.language_id = ?', (int)Session::get('languages_id'))
	->andWhere('s.status = ?', '1')
	->orderBy('s.specials_date_added desc');

	EventManager::notify('ProductListingQueryBeforeExecute', &$Qproducts);

	$productListing = new productListing_row();
	$productListing->setQuery($Qproducts);
	
	$pageTitle = sysLanguage::get('SPECIALS_HEADING_SPECIALS');
	$pageContents = $productListing->draw();
	
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
