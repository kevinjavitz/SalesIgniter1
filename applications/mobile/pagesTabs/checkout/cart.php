<?php
$productRows = array();

$CartProducts = $ShoppingCart->getProducts()->getIterator();
while($CartProducts->valid() === true){
	$CartProduct = $CartProducts->current();

	$pID_string = $CartProduct->getIdString();
	$productPrice = $CartProduct->getPrice();
	$productFinalPrice = $CartProduct->getFinalPrice();
	$productTax = $CartProduct->getTaxRate();
	$productQuantity = $CartProduct->getQuantity();
	$productsName = $CartProduct->getNameHtml();
	$productsModel = $CartProduct->getModel();
	$quantity = $CartProduct->getCartQuantityHtml();

	$productRows[] = array(
		array('text' => $productsName, 'align' => 'left'),
		array('text' => $quantity, 'align' => 'left'),
		array('text' => $currencies->display_price($productPrice, $productTax), 'align' => 'right'),
		array('text' => $currencies->display_price($productFinalPrice, $productTax, $productQuantity), 'align' => 'right'),
		array('text' => '<a pID="'.$pID_string.'" href="#" class="ui-icon ui-icon-closethick removeFromCart"></a>', 'align' => 'right')
	);

	EventManager::notify('ShoppingCartListingAddBodyColumn', &$productRows, $CartProduct);

	$CartProducts->next();
}
?>
<div id="checkoutShoppingCart" style="padding:.3em;"><?php
	$productTable = htmlBase::newElement('table')
		->setCellPadding(3)
		->setCellSpacing(0)
		->attr('width', '100%')
		->stripeRows('ui-bar-c', 'ui-bar-d');

	$shoppingCartHeader = array(
		array('align' => 'left', 'text' => '<b>' . sysLanguage::get('TABLE_HEADING_PRODUCTS_NAME') . '</b>'),
		array('align' => 'left', 'text' => '<b>' . sysLanguage::get('TABLE_HEADING_PRODUCTS_QTY') . '</b>'),
		array('align' => 'right', 'text' => '<b>' . sysLanguage::get('TABLE_HEADING_PRODUCTS_PRICE') . '</b>'),
		array('align' => 'right', 'text' => '<b>' . sysLanguage::get('TABLE_HEADING_PRODUCTS_FINAL_PRICE') . '</b>'),
		array('text' => '&nbsp;')
	);

	EventManager::notify('ShoppingCartListingAddHeaderColumn', &$shoppingCartHeader);

	$productTable->addHeaderRow(array(
		'addCls' => 'ui-bar-b',
		'columns' => $shoppingCartHeader
	));

	foreach($productRows as $i => $rInfo){
		$shoppingCartBodyRow = array();
		foreach($rInfo as $colInfo){
			$shoppingCartBodyRow[] = array(
				'align'  => $colInfo['align'],
				'valign' => 'top',
				'text'   => $colInfo['text']
			);
		}

		foreach($shoppingCartBodyRow as $k => $rInfo){
			$shoppingCartBodyRow[$k]['addCls'] = 'ui-widget-content';
			$shoppingCartBodyRow[$k]['css'] = array(
				'border-top' => 'none'
			);
			if ($k > 0){
				$shoppingCartBodyRow[$k]['css']['border-left'] = 'none';
			}
		}

		$productTable->addBodyRow(array(
			'columns' => $shoppingCartBodyRow
		));
	}

	echo $productTable->draw();
	?></div>
