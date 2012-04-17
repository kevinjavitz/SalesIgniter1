<?php
	$productRows = array();
	foreach($ShoppingCart->getProducts() as $cartProduct) {
		$pID_string = $cartProduct->getIdString();
		$purchaseType = $cartProduct->getPurchaseType();
		$productPrice = $cartProduct->getPrice();
		$productFinalPrice = $cartProduct->getFinalPrice();
		$productTax = $cartProduct->getTaxRate();
		$productQuantity = $cartProduct->getQuantity();
		$productsName = $cartProduct->getNameHtml();
		$productsModel = $cartProduct->getModel();

		/* @TODO: Get into pay per rental extension */
		$qtyInput = htmlBase::newElement('input')
		->setName('qty')
		->attr('pID', $cartProduct->getUniqID())
		->attr('ptype',$purchaseType)
		->val($productQuantity);

		if ($purchaseType == 'reservation'){
			$qtyInput->setType('hidden');
			$qty = $qtyInput->draw() . $productQuantity;
		}else{
			$qtyInput->setSize(3);
			$qty = $qtyInput->draw();
		}

		$productRows[] = array(			
			array('text' => $productsName, 'align' => 'left'),
			array('text' => ucfirst($purchaseType), 'align' => 'left'),
			array('text' => $qty, 'align' => 'left'),
			array('text' => $currencies->display_price($productPrice, $productTax), 'align' => 'right'),
			array('text' => $currencies->display_price($productFinalPrice, $productTax, $productQuantity), 'align' => 'right'),
			array('text' => '<a pID="'.$cartProduct->getUniqID().'" type="'.$purchaseType.'" href="#" class="ui-icon ui-icon-closethick removeFromCart"></a>', 'align' => 'right')
		);

		EventManager::notify('ShoppingCartListingAddBodyColumn', &$productRows, $cartProduct);
	}
?>
<div id="checkoutShoppingCart" style="padding:.3em;"><?php
	$productTable = htmlBase::newElement('table')
	->setCellPadding(3)
	->setCellSpacing(0)
	->attr('width', '100%');

	$shoppingCartHeader = array(
		array('addCls' => 'ui-widget-header', 'align' => 'left', 'css' => array('border-left' => 'none'), 'text' => '<b>' . sysLanguage::get('TABLE_HEADING_PRODUCTS_NAME') . '</b>'),
		array('addCls' => 'ui-widget-header', 'align' => 'left', 'css' => array('border-left' => 'none'), 'text' => '<b>' . sysLanguage::get('TABLE_HEADING_PURCHASE_TYPE') . '</b>'),
		array('addCls' => 'ui-widget-header', 'align' => 'left', 'css' => array('border-left' => 'none'), 'text' => '<b>' . sysLanguage::get('TABLE_HEADING_PRODUCTS_QTY') . '</b>'),
		array('addCls' => 'ui-widget-header', 'align' => 'right', 'css' => array('border-left' => 'none'), 'text' => '<b>' . sysLanguage::get('TABLE_HEADING_PRODUCTS_PRICE') . '</b>'),
		array('addCls' => 'ui-widget-header', 'align' => 'right', 'css' => array('border-left' => 'none'), 'text' => '<b>' . sysLanguage::get('TABLE_HEADING_PRODUCTS_FINAL_PRICE') . '</b>'),
		array('addCls' => 'ui-widget-header', 'align' => 'right','css' => array('border-left' => 'none'), 'text' => sysLanguage::get('TEXT_REMOVE_ITEM_CART'))
	);

	EventManager::notify('ShoppingCartListingAddHeaderColumn', &$shoppingCartHeader);

	$productTable->addHeaderRow(array(
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

		EventManager::notify('ShoppingCartListingAddBodyColumn', &$shoppingCartBodyRow, $cartProduct);

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
