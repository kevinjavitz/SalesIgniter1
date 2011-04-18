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

		$productsName = $productsName . ' ( ' . $productsModel . ' ) ';

		if ($cartProduct->hasInfo('download_type')){
			$productsName .= '<br><nobr><small>&nbsp;<i> - View Type: ' . ($cartProduct->getInfo('download_type') == 'stream' ? 'Stream' : 'Download') . '</i></small></nobr>';
		}

		$contents = EventManager::notifyWithReturn('CheckoutProductAfterProductName', &$cartProduct);
		if (!empty($contents)){
			foreach($contents as $content){
				$productsName .= $content;
			}
		}
		
		$productRows[] = array(
			array('text' => $productQuantity . ' x ' . $productsName, 'align' => 'left'),
			array('text' => ucfirst($purchaseType), 'align' => 'left'),
			array('text' => $currencies->display_price($productFinalPrice, $productTax, $productQuantity), 'align' => 'right')
		);
	}
	
	EventManager::attachActionResponse(array(
		'success' => true,
		'productRows' => $productRows
	), 'json');
?>