<?php
	$product = new product($_POST['products_id'], $_POST['type']);
	if ($product->getTrackMethod($_POST['type']) == 'barcode'){
		$invItems = $product->getInventoryItems($_POST['type']);
		$barcodeArray = array();
		foreach($invItems as $barcode){
			$barcodeArray[] = array(
				'id'   => $barcode['id'],
				'text' => $barcode['barcode']
			);
		}
		$barcodeHtml = tep_draw_pull_down_menu('products_barcode', $barcodeArray, '', 'id="barcodes"');
	}else{
		$barcodeHtml = '<span id="barcodes">Purchase type does not use barcodes</span>';
	}

	if ($_POST['type'] == 'reservation'){
		$pricing = $product->getReservationPrice($_POST['start_date'], $_POST['end_date']);
		$price = $currencies->format($pricing['price']);
	}else{
		$price = $product->displayPrice($_POST['type']);
	}
	
	EventManager::attachActionResponse(array(
		'success'      => true,
		'productPrice' => $price,
		'barcodeHtml'  => $barcodeHtml
	), 'json');
?>