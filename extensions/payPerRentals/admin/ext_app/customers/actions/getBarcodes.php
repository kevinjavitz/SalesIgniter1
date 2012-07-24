<?php
	require('../includes/classes/product.php');
	$product = new product((int)$_GET['pID']);
	
	$array = array();
	$barcodes = $product->getInventoryItems('reservation');
	foreach($barcodes as $bInfo){
		$array[] = array(
			'id'   => $bInfo['id'],
			'text' => $bInfo['barcode']
		);
	}
	
	$html = tep_draw_pull_down_menu('barcode', $array, $_GET['barcode'], 'id="barcodesDrop"');
	EventManager::attachActionResponse($html, 'html');
?>