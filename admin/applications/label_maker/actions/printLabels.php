<?php
	$products = explode(',', $_GET['checked']);
	
	$labelMaker = new labelMaker();
	foreach($products as $product){
		$pInfo = explode('_', $product);
		$labelMaker->addProduct($pInfo[1], $pInfo[0]);
	}
	
	switch($_GET['labelType']){
		case 'avery_5160':
		case 'avery_5164':
			$labelMaker->setType($_GET['labelType']);
			echo $labelMaker->draw('pdf');
			break;
		case 'avery_5160_html':
		case 'avery_5164_html':
			$labelMaker->setType(substr($_GET['labelType'], 0, -5));
			echo $labelMaker->draw('html');
			break;
	}
/*
	$PDF_Labels = new PDF_Labels();
	$PDF_Labels->setLabelsType($labelType);
	foreach($rentals as $rental){
		$type = substr($rental, 0, 1);
		$PDF_Labels->loadLabelInfo(str_replace($type . '_', '', $rental), $type);
	}

	//print_r($PDF_Labels->labels);
	switch($labelType){
		case '5160':
		case '5164':
			echo $PDF_Labels->buildPDF();
			break;
		case 'ship_html':
		case 'pinfo_html':
			echo $PDF_Labels->buildHTML();
			break;
	}
*/
	itwExit();
?>