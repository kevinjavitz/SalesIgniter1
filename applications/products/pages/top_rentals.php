<?php
$datePast = date('Y-m-d H:i:s', mktime(0,
									   0,
									   0,
									   date("m")-2,
									   date("d"),
									   date("Y")
							  )
);
$QtopRentals = Doctrine_Query::create()
	->select('p.products_id,pd.products_name,rt.*')
	->from('Products p')
	->leftJoin('p.ProductsDescription pd')
	->leftJoin('p.RentalTop rt')
	->where('p.products_status = ?', '1')
	->where('pd.language_id = ?', (int)Session::get('languages_id'))
	//->andWhere('rt.date_modified > ?', $datePast)
	->limit(10)
	->orderBy('rt.top desc');

EventManager::notify('ProductListingQueryBeforeExecute', &$QtopRentals);

if(sysConfig::get('PRODUCT_LISTING_TYPE') == 'row'){
	$productListing = new productListing_row();
} else {
	$productListing = new productListing_col();
}
$productListing->setQuery($QtopRentals);

$pageContent->set('pageTitle', sysLanguage::get('HEADING_TITLE_TOP_RENTALS'));
$pageContent->set('pageContent', $productListing->draw());
