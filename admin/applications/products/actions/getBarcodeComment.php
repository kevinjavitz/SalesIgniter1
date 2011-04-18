<?php

	$barcodeID = $_GET['barcode_id'];
	$QbarcodeComment = Doctrine_Query::create()
						->select('comments')
						->from('ProductsInventoryBarcodesComments')
						->where('barcode_id = ?', $barcodeID)
						->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	$html = '';
    if(count($QbarcodeComment)>0){
		$html = $QbarcodeComment[0]['comments'];
    }

	$json = array(
			'success' => true,
			'html' => $html
		);

	EventManager::attachActionResponse($json, 'json');
?>