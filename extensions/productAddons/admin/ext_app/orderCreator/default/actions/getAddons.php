<?php

$content = '<div class="myAddons">';

	$pID = $_POST['pID'];
	$qty = $_POST['qty'];
	$Qdata = Doctrine_Query::create()
		->from('Products')
		->where('products_id = ?', $pID)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	$addonProduct = explode(',', $Qdata[0]['addon_products']);
	$addonProduct2 = explode(',', $Qdata[0]['optional_addon_products']);
	//$addonProduct = array_merge($addonProduct1, $addonProduct2);
	foreach($addonProduct as $addon){
		$ProductName = Doctrine_Query::create()
		->from('ProductsDescription')
		->where('products_id = ?', $addon)
		->andWhere('language_id=?', Session::get('languages_id'))
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		$htmlSelectType = htmlBase::newElement('selectbox')
		->setName('addon_product_type['.$addon.']');

		//PurchaseTypeModules::loadModules();
		//foreach(PurchaseTypeModules::getModules() as $purchaseType){
			//$code = $purchaseType->getCode();
			//$purchaseType->loadProduct($addon);
		$productClass = new Product($addon);
		foreach($productClass->productInfo['typeArr'] as $typeName){

			$htmlSelectType->addOption($typeName, ucfirst($typeName));
		}


		$content .= '<input type="checkbox" readonly="readonly" checked="checked" name="addon_product['.$addon.']" value="1">'.$ProductName[0]['products_name'].'&nbsp;'.$htmlSelectType->draw().'Quantity: <input name="addon_product_qty['.$addon.']" type="text" size="3" value="'.$qty.'"/>'.'<br/>';
	}

foreach($addonProduct2 as $addon){
	$ProductName = Doctrine_Query::create()
		->from('ProductsDescription')
		->where('products_id = ?', $addon)
		->andWhere('language_id=?', Session::get('languages_id'))
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	$htmlSelectType = htmlBase::newElement('selectbox')
		->setName('addon_product_type['.$addon.']');

	$productClass = new Product($addon);
	foreach($productClass->productInfo['typeArr'] as $typeName){

		$htmlSelectType->addOption($typeName, ucfirst($typeName));
	}

	$content .= '<input type="checkbox" name="addon_product['.$addon.']" value="1">'.$ProductName[0]['products_name'].'&nbsp;'.$htmlSelectType->draw().'Quantity: <input name="addon_product_qty['.$addon.']" size="3" type="text" value="'.$qty.'"/>'.'<br/>';
}
$content .= '</div>';

EventManager::attachActionResponse(array(
		'success' => true,
		'addonProducts'  => $content
	), 'json');
	?>