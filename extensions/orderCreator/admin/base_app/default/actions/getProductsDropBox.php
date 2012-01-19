<?php
$Qproducts = Doctrine_Query::create()
	->select('p.products_id, pd.products_name')
	->from('Products p')
	->leftJoin('p.ProductsDescription pd')
	->where('pd.language_id = ?', Session::get('languages_id'))
	->orderBy('pd.products_name');

$MultiStore = $appExtension->getExtension('multiStore');
if ($MultiStore && $MultiStore->isEnabled()){
	$Qproducts->leftJoin('p.ProductsToStores p2s')
		->andWhereIn('p2s.stores_id', Session::get('admin_allowed_stores'));
}
$Products = $Qproducts->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

$selectBox = htmlBase::newElement('selectbox')
	->addClass('productSelectBox');
$selectBox->addOption(0, sysLanguage::get('TEXT_PLEASE_SELECT'));
foreach($Products as $pInfo){

	$QproductAddons = Doctrine_Query::create()
	->from('Products p')
	->where('FIND_IN_SET(?, addon_products)', $pInfo['products_id'])
	->orWhere('FIND_IN_SET(?, optional_addon_products)', $pInfo['products_id'])
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	if(count($QproductAddons) == 0){
		$selectBox->addOption($pInfo['products_id'], $pInfo['ProductsDescription'][0]['products_name']);
	}
}
EventManager::attachActionResponse($selectBox->draw(), 'html');
