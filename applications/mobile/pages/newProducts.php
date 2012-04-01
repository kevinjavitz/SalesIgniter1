<?php
$ListView = htmlBase::newElement('list')
	->attr('data-role', 'listview')
	->attr('data-theme', 'c');

$QproductsNew = Doctrine_Query::create()
	->select('p.products_id, p.products_image, pd.products_name')
	->from('Products p')
	->leftJoin('p.ProductsDescription pd')
	->leftJoin('p.ProductsToBox p2b')
	->where('p.products_status = ?', '1')
	->andWhere('p2b.products_id is null')
	->andWhere('pd.language_id = ?', (int)Session::get('languages_id'))
	->orderBy('p.products_date_added DESC, pd.products_name');

EventManager::notify('ProductListingQueryBeforeExecute', &$QproductsNew);

$Products = $QproductsNew->execute();
foreach($Products as $Product){
	$imageHtml = htmlBase::newElement('image')
		->setSource('/' . sysConfig::get('DIR_WS_IMAGES') . $Product->products_image)
		->thumbnailImage(true)
		->setWidth(100)
		->setHeight(100);

	$ListView->addItem('', '<a data-theme="etvideo-blue" href="' . itw_app_link('products_id=' . $Product->products_id, 'mobile', 'productInfo') . '">' . $imageHtml->draw() . '<h3>' . $Product->ProductsDescription[Session::get('languages_id')]->products_name . '</h3></a>');
}

$pageContent->set('pageTitle', 'New Products');
$pageContent->set('pageContent', $ListView->draw());
