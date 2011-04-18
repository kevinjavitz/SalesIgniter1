<?php
	$productRows = array();
	foreach($ShoppingCart->getProducts() as $cartProduct) {
		$pID_string = $cartProduct->getIdString();
		$purchaseType = $cartProduct->getPurchaseType();
		$productPrice = $cartProduct->getPrice();
		$productFinalPrice = $cartProduct->getFinalPrice();
		$productTax = $cartProduct->getTaxRate();
		$productQuantity = $cartProduct->getQuantity();
		$productsName = $cartProduct->getName();
		$productsModel = $cartProduct->getModel();

		if ($cartProduct->hasInfo('download_type')){
			$productsName .= '<br><nobr><small>&nbsp;<i> - View Type: ' . ($cartProduct->getInfo('download_type') == 'stream' ? 'Stream' : 'Download') . '</i></small></nobr>';
		}

		$contents = EventManager::notifyWithReturn('CheckoutProductAfterProductName', &$cartProduct);
		if (!empty($contents)){
			foreach($contents as $content){
				$productsName .= $content;
			}
		}

		/* @TODO: Get into pay per rental extension */
		$qtyInput = htmlBase::newElement('input')
		->setName('qty[' . $pID_string . '][' . $purchaseType . ']')
		->val($productQuantity);

		if ($purchaseType == 'reservation'){
			$qtyInput->setType('hidden');
			$qty = $qtyInput->draw() . $productQuantity;
		}else{
			$qtyInput->setSize(3);
			$qty = $qtyInput->draw();
		}

		$productRows[] = array(
			array('text' => $productsModel, 'align' => 'left'),
			array('text' => $productsName, 'align' => 'left'),
			array('text' => ucfirst($purchaseType), 'align' => 'left'),
			array('text' => $qty, 'align' => 'left'),
			array('text' => $currencies->display_price($productPrice, $productTax), 'align' => 'right'),
			array('text' => $currencies->display_price($productFinalPrice, $productTax, $productQuantity), 'align' => 'right'),
			array('text' => '<a href="' . itw_app_link('action=removeProduct&pID=' . $pID_string . '&type=' . $purchaseType, 'checkout', 'default') . '" class="removeFromCart"><img border="0" src="' . DIR_WS_IMAGES . 'icons/cross.gif"></a>', 'align' => 'right')
		);

		EventManager::notify('ShoppingCartListingAddBodyColumn', &$productRows, $cartProduct);
	}

	if (isset($_GET['rType']) && $_GET['rType'] == 'ajax'){
		echo json_encode(array(
			'success' => true,
			'productRows' => $productRows
		));
	}else{
?>
<div id="shoppingCart"><?php
	$productTable = htmlBase::newElement('table')->setCellPadding(3)->setCellSpacing(0)->attr('width', '100%');

	$shoppingCartHeader = array(
		array('addCls' => 'main', 'align' => 'left', 'text' => '<b>' . sysLanguage::get('TABLE_HEADING_PRODUCTS_MODEL') . '</b>'),
		array('addCls' => 'main', 'align' => 'left', 'text' => '<b>' . sysLanguage::get('TABLE_HEADING_PRODUCTS_NAME') . '</b>'),
		array('addCls' => 'main', 'align' => 'left', 'text' => '<b>' . 'Purchase Type' . '</b>'),
		array('addCls' => 'main', 'align' => 'left', 'text' => '<b>' . sysLanguage::get('TABLE_HEADING_PRODUCTS_QTY') . '</b>'),
		array('addCls' => 'main', 'align' => 'right', 'text' => '<b>' . sysLanguage::get('TABLE_HEADING_PRODUCTS_PRICE') . '</b>'),
		array('addCls' => 'main', 'align' => 'right', 'text' => '<b>' . sysLanguage::get('TABLE_HEADING_PRODUCTS_FINAL_PRICE') . '</b>'),
		array('addCls' => 'main', 'text' => '&nbsp;')
	);

	EventManager::notify('ShoppingCartListingAddHeaderColumn', &$shoppingCartHeader);

	$productTable->addHeaderRow(array(
		'columns' => $shoppingCartHeader
	));

	foreach($productRows as $i => $rInfo){
		$shoppingCartBodyRow = array();
		foreach($rInfo as $colInfo){
			$shoppingCartBodyRow[] = array(
				'addCls' => 'main',
				'align'  => $colInfo['align'],
				'valign' => 'top',
				'text'   => $colInfo['text']
			);
		}

		EventManager::notify('ShoppingCartListingAddBodyColumn', &$shoppingCartBodyRow, $cartProduct);

		$productTable->addBodyRow(array(
			'columns' => $shoppingCartBodyRow
		));
	}

	echo $productTable->draw();
?></div>
<?php
	}
?>