<?php

	$barcodeId = $_POST['barcode_id'];
	$comments = $_POST['comments'];
	$barcodeCommentTable = Doctrine_Core::getTable('ProductsInventoryBarcodesComments');
	$barcodeComment = $barcodeCommentTable->findOneByBarcodeId($barcodeId);

	if ($barcodeComment){
		$barcodeComment->comments = $comments;
		$barcodeComment->save();
	}else{
		$barcodeComment = new ProductsInventoryBarcodesComments();
		$barcodeComment->barcode_id = $barcodeId;
		$barcodeComment->comments = $comments;
		$barcodeComment->save();
	}     


	$json = array(
			'success' => true
	);

	EventManager::attachActionResponse($json, 'json');
?>