<?php
/*
	Categories Pages Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/
	$pageId = (isset($_GET['pages_id']) ? (int)$_GET['pages_id'] : $_GET['appPage']);
	$QCategoriesPages = Doctrine_Query::create()
	->from('CategoriesPages');
	if (is_numeric($pageId)){
		$QCategoriesPages->where('categories_pages_id = ?', $pageId);
	}else{
		$QCategoriesPages->where('page_key = ?', $pageId);
	}
	$Result = $QCategoriesPages->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	if(count($Result) > 0){
		$contentHeading = $Result[0]['page_title'];
		$categories = explode(',', $Result[0]['categories']);
		$Qproducts = Doctrine_Query::create()
		->select('p.products_id')
		->from('Products p')
		->leftJoin('p.ProductsToCategories p2c')
		->leftJoin('p.ProductsDescription pd')
		->leftJoin('p.ProductsToBox p2b')
		->where('p.products_status = ?', '1')
		->andWhere('p2b.products_id is null')
		->andWhereIn('p2c.categories_id',$categories)
		->andWhere('pd.language_id = ?', (int)Session::get('languages_id'));

		EventManager::notify('ProductListingQueryBeforeExecute', &$Qproducts);

		if(sysConfig::get('PRODUCT_LISTING_TYPE') == 'row'){
			$productListing = new productListing_row();
		} else {
			$productListing = new productListing_col();
		}
		$productListing->setQuery($Qproducts);

		$contentHtml = $productListing->draw();
	}
	$contentHeading = stripslashes($contentHeading);
	$contentHtml = stripslashes($contentHtml);

	if (isset($_GET['appPage'])){
		$breadcrumb->add($contentHeading, itw_app_link('appExt=categoriesPages', 'show_page', $_GET['appPage']));
	}else{
		$breadcrumb->add($contentHeading, itw_app_link('pages_id=' . (int)$_GET['pages_id']));
	}

	$pageTitle = $contentHeading;
	$pageContents = $contentHtml;

	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
