<?php
	include_once("includes/functions/barcode.php" );
	if(!class_exists('Product')){
 		require('../includes/classes/product.php');
	}
	require('includes/classes/pdf_labels.php');

	$labelType = $_GET['labelType'];
	$PDF_Labels = new PDF_Labels();
	$PDF_Labels->setLabelsType($labelType);

    if(isset($_GET['pID']) && isset($_GET['barcodes']))
	    $PDF_Labels->loadBarcodes($_GET['pID'], explode(',', $_GET['barcodes']));
    if(isset($_GET['print']) && $_GET['print'] == 'all')
        $PDF_Labels->loadAllBarcodes();

	if (isset($_GET['loc'])){
		$PDF_Labels->setLabelLocation((int)$_GET['loc']);
	}

	switch($labelType){
		case '5160':
		case '5164':
			$html = $PDF_Labels->buildPDF();
			break;
		case 'ship_html':
		case 'pinfo_html':
			$html = $PDF_Labels->buildHTML();
			break;
		case 'barcodes':
			$html = $PDF_Labels->buildHTML();
			break;
	}
	EventManager::attachActionResponse($html, 'html');
?>