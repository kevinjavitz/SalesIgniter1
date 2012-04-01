<?php
$ListView = htmlBase::newElement('list')
	->attr('data-role', 'listview');

$Qproducts = Doctrine_Query::create()
	->select('p.products_id, p.products_image, pd.products_name')
	->from('Products p')
	->leftJoin('p.ProductsDescription pd')
	->leftJoin('p.ProductsToCategories p2c')
	->where('p.products_status = ?', '1')
	->andWhere('p2c.categories_id = ?', (int)$_GET['cPath'])
	->andWhere('pd.language_id = ?', (int)Session::get('languages_id'))
	->orderBy('p.products_date_added DESC, pd.products_name');

EventManager::notify('ProductListingQueryBeforeExecute', &$Qproducts);

$Products = $Qproducts->execute();
foreach($Products as $Product){
	$imageHtml = htmlBase::newElement('image')
		->setSource('/' . sysConfig::get('DIR_WS_IMAGES') . $Product->products_image)
		->thumbnailImage(true)
		->setWidth(100)
		->setHeight(100);

	$ListView->addItem('', '<a data-theme="etvideo-blue" href="' . itw_app_link('products_id=' . $Product->products_id, 'mobile', 'productInfo') . '">' . $imageHtml->draw() . '<h3>' . $Product->ProductsDescription[Session::get('languages_id')]->products_name . '</h3></a>');
}

$Category = Doctrine_Query::create()
	->from('CategoriesDescription')
	->where('categories_id = ?', (int)$_GET['cPath'])
	->andWhere('language_id = ?', (int)Session::get('languages_id'))
	->fetchOne();
$pageContent->set('pageTitle', 'Category: ' . $Category->categories_name);
$pageContent->set('pageContent', $ListView->draw());
