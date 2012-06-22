<?php
$tagName = urldecode($_GET['tag_name']);
$Qproducts = Doctrine_Query::create()
	->select('DISTINCT p.products_id')
	->from('Products p')
	->leftJoin('p.TagsToProducts tp')
	->leftJoin('tp.CustomTags ct')
	->leftJoin('p.ProductsDescription pd')
	->leftJoin('p.ProductsToBox p2b')
	->where('p.products_status = ?', '1')
	->andWhere('p2b.products_id is null')
	->andWhere('ct.tag_name = ?', $tagName)
	->andWhere('pd.language_id = ?', (int)Session::get('languages_id'));

EventManager::notify('ProductSearchQueryBeforeExecute', &$Qproducts);

if(sysConfig::get('PRODUCT_LISTING_TYPE') == 'row'){
	$productListing = new productListing_row();
} else {
	$productListing = new productListing_col();
}
$productListing->setQuery($Qproducts);

$pageContent->set('pageTitle', sprintf(sysLanguage::get('HEADING_TITLE_SEARCH_RESULT'), $tagName));
$pageContent->set('pageContent', $productListing->draw());
//$pageContent->set('pageButtons', $pageButtons);

?>