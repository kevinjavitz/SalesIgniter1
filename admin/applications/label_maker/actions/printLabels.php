<?php
	$products = $_GET['print'];
	
	$labelMaker = new labelMaker();
	foreach($products as $product){
		$pInfo = explode('_', $product);
		$labelMaker->addProduct($pInfo[1], $pInfo[0]);
	}
	
if ($_GET['printMethod'] == 'dymo'){
	$labelType = $_GET['labelType'];

	$labelInfo = array(
		'xmlData' => file_get_contents(sysConfig::getDirFsCatalog() . 'ext/dymo_labels/' . $labelType . '.label'),
		'data'    => array()
	);

	foreach($labelMaker->getData() as $rInfo){
		if ($labelType == '8160-b'){
			$labelInfo['data'][] = array(
				'Barcode'     => $rInfo['barcode'],
				'BarcodeType' => sysConfig::get('SYSTEM_BARCODE_FORMAT')
			);
		}
		elseif ($labelType == '8160-s') {
			$Address = $rInfo['customers_address'];
			$labelInfo['data'][] = array(
				'Address' => strip_tags(str_replace('&nbsp;', ' ', tep_address_format(tep_get_address_format_id($Address['entry_country_id']), $Address, false,'','')))
			);
		}
		elseif ($labelType == '8164') {
			$labelInfo['data'][] = array(
				'ProductsName'         => $rInfo['products_name'],
				'Barcode'              => $rInfo['barcode'],
				'BarcodeType'          => sysConfig::get('SYSTEM_BARCODE_FORMAT'),
				'ProductsDescription'  => $rInfo['products_description'],
			);
			//print_r($labelInfo);
		}
	}

	EventManager::attachActionResponse(array(
		'success'   => true,
		'labelInfo' => $labelInfo
	), 'json');
}
else {
	foreach($labelMaker->getData() as $rInfo){
		$labelInfo['data'][] = array(
			'products_name'        => $rInfo['products_name'],
			'barcode'              => $rInfo['barcode'],
			'barcode_type'         => sysConfig::get('SYSTEM_BARCODE_FORMAT'),
			'barcode_id'           => $rInfo['barcode_id'],
			'products_description' => $rInfo['products_description'],
			'customers_address'    => $rInfo['customers_address']
		);
	}

	if ($_GET['printMethod'] == 'spreadsheet'){
		require(sysConfig::getDirFsCatalog() . 'includes/classes/FileParser/csv.php');
		$File = new FileParserCsv('temp');
		$File->addRow(array(
			'ProductsName',
			'Barcode',
			'BarcodeType',
			'BarcodeId',
			'ProductsDescription',
			'Address'
		));
		$sep = ';';
		switch($_GET['field_separator']){
			case 'tab'       : $sep = '	';
			case 'semicolon' : $sep = ';';
			case 'colon'     : $sep = ':';
			case 'comma'     : $sep = ',';
		}
		$File->setCsvControl($sep);
		foreach($labelInfo['data'] as $lInfo){
			$lInfo['customers_address'] = strip_tags(str_replace('&nbsp;', ' ', tep_address_format(tep_get_address_format_id($lInfo['customers_address']['entry_country_id']), $lInfo['customers_address'], false)));
			$lInfo['products_description'] = wordwrap(strip_tags(str_replace('&nbsp;', ' ', $lInfo['products_description'])), 70);
			$File->addRow($lInfo);
		}
		$File->output();
	}else{
		require(sysConfig::getDirFsAdmin() . 'includes/classes/pdf_labels.php');
		$LabelMaker = new PDF_Labels();
		$LabelMaker->setData($labelInfo['data']);
		$LabelMaker->setLabelsType($_GET['labelType']);
		$LabelMaker->setStartLocation($_GET['row_start'], $_GET['col_start']);
		$LabelMaker->buildPDF();
	}
}

