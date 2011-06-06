<?php
$pricingTabsObj = htmlBase::newElement('tabs')
	->setId('pricingTabs');

foreach(PurchaseTypeModules::getModules() as $purchaseType){
	if ($purchaseType->getConfigData('PRICING_ENABLED') == 'True'){
		$pricingTypeName = $purchaseType->getCode();
		$pricingTypeText = $purchaseType->getTitle();
		$productsPrice = $purchaseType->getPriceFromQuery($Product);

		$inputNet = htmlBase::newElement('input')
			->addClass('netPricing')
			->setName('products_price_' . $pricingTypeName)
			->setId('products_price_' . $pricingTypeName)
			->val($productsPrice);

		$inputGross = htmlBase::newElement('input')
			->addClass('grossPricing')
			->setName('products_price_' . $pricingTypeName . '_gross')
			->setId('products_price_' . $pricingTypeName . '_gross')
			->val($productsPrice);

		$inputTable = htmlBase::newElement('table')
			->setCellPadding(2)
			->setCellSpacing(0);

		$inputTable->addBodyRow(array(
				'columns' => array(
					array('text' => 'Price Net:'),
					array('text' => $inputNet->draw())
				)
			));
		$inputTable->addBodyRow(array(
				'columns' => array(
					array('text' => 'Price Gross:'),
					array('text' => $inputGross->draw())
				)
			));

		EventManager::notify('NewProductPricingTabBottom', $Product, &$inputTable, &$purchaseType);

		$pricingTabsObj->addTabHeader('productPricingTab_' . $pricingTypeName, array('text' => $pricingTypeText))
			->addTabPage('productPricingTab_' . $pricingTypeName, array('text' => $inputTable));
	}
}

$contents = EventManager::notifyWithReturn('NewProductPricingTabTop', $Product);
if (!empty($contents)){
	foreach($contents as $content){
		echo $content;
	}
}
?>
<div class="main"><?php
	echo sysLanguage::get('TEXT_PRODUCTS_TAX_CLASS') .
	tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .
	tep_draw_pull_down_menu('products_tax_class_id', $tax_class_array, $Product['products_tax_class_id'], ' id="tax_class_id"');
	?></div>
<hr>
<?php
 	echo $pricingTabsObj->draw();
?>