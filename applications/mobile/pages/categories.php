<?php
$ListView = htmlBase::newElement('list')
	->attr('data-role', 'listview')
	->attr('data-theme', 'c');

$ListView->addItem('', '<a href="' . itw_app_link('rType=ajax', 'mobile', 'newProducts') . '">Newest</a>');

$Qcategories = Doctrine_Query::create()
	->select('c.categories_id, cd.categories_name')
	->from('Categories c')
	->leftJoin('c.CategoriesDescription cd')
	->where('cd.language_id = ?', Session::get('languages_id'))
	->orderBy('cd.categories_name')
	->execute();
foreach($Qcategories as $Category){
	$cID = $Category->categories_id;
	$CategoryName = $Category->CategoriesDescription[Session::get('languages_id')]->categories_name;
	$ListView->addItem(
		'category-' . $cID,
		'<a href="' . itw_app_link('rType=ajax&cPath=' . $cID, 'mobile', 'products') . '">' . $CategoryName . '</a>'
	);
}

$pageContent->set('pageTitle', 'Categories');
$pageContent->set('pageContent', $ListView->draw());
