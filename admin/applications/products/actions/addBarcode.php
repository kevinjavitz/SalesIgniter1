<?php
	$pID = (int)$_GET['pID'];
	$barcode = (isset($_POST['barcodeNumber']) ? $_POST['barcodeNumber'] : false);
	$type = $_GET['purchaseType'];
    $supplier_id = $_GET['suppliersId'];

    if($_GET['acquisitionCost'] != '')
        $cost = $_GET['acquisitionCost'];
    else
        $cost = 0;

	$status = 'A';
	if (array_key_exists('aID_string', $_GET)){
		$aID_string = $_GET['aID_string'];
	}
	$controller = (isset($aID_string) ? 'attribute' : 'normal');
	$newBarcodes = array();

	$Qinventory = Doctrine_Query::create()
	->from('ProductsInventory i')
	->where('products_id = ?', $pID)
	->andWhere('type = ?', $type)
	->andWhere('controller = ?', $controller);
	$ProductsInventory = $Qinventory->fetchOne();
	if (!$ProductsInventory){
		$ProductsInventory = new ProductsInventory();
		$ProductsInventory->products_id = $pID;
		$ProductsInventory->type = $type;
		$ProductsInventory->track_method = 'barcode';
		$ProductsInventory->controller = $controller;
	}
	
	$Barcodes = $ProductsInventory->ProductsInventoryBarcodes;
	$nextIndex = $Barcodes->key() + 1;
	if (isset($_POST['autogen'][$type])){
		$productName = tep_get_products_name($pID);
		$nameFix = strtolower(substr(str_replace(' ', '_', strip_tags($productName)), 0, 4));
		if (substr($nameFix, -1) == '_'){
			while(substr($nameFix, -1) == '_'){
				$nameFix = substr($nameFix, 0, -1);
			}
		}
		$nameFix .= '_' . $pID;
		$Qcheck = Doctrine_Query::create()
		->select('barcode')
		->from('ProductsInventoryBarcodes')
		->where('barcode like ?', $nameFix . '_' . $type . '_%')
		->orderBy('barcode desc')
		->limit('1')
		->execute(array(), Doctrine::HYDRATE_ARRAY);
		if ($Qcheck){
			$bCode = $Qcheck[0]['barcode'];
			$start = (int)substr($bCode, strrpos($bCode, '_')+1) + 1;
		}else{
			$start = 1;
		}

		$total = (int)$_POST['autogenTotal'][$type];
		$endNumber = $start;
		
		for($i=0; $i<$total; $i++){
			$numberString = $endNumber;
			if ($numberString < 100){
				if (strlen($numberString) == 2){
					$numberString = '0' . $numberString;
				}elseif (strlen($numberString) == 1){
					$numberString = '00' . $numberString;
				}
			}
			$genBarcode = $nameFix . '_' . $type . '_' . $numberString;
			if(sysConfig::get('SYSTEM_BARCODE_FORMAT') == 'Code 39'){
				$genBarcode = strtoupper($genBarcode);
				$genBarcode = str_replace('_','-', $genBarcode);
			}
			if(sysConfig::get('SYSTEM_BARCODE_FORMAT') == 'Code 25' || sysConfig::get('SYSTEM_BARCODE_FORMAT') == 'Code 25 Interleaved'){
				$genBarcode = strtotime(date('Y-m-d H:i:s')).$endNumber;
				if(strlen($genBarcode) % 2 == 1){
					$genBarcode = '0'.$genBarcode;
				}

			}
			$endNumber++; 
			
			$Barcodes[$nextIndex]->barcode = $genBarcode;
			$Barcodes[$nextIndex]->status = $status;
            $Barcodes[$nextIndex]->suppliers_id = $supplier_id;
            $Barcodes[$nextIndex]->acquisition_cost = $cost;
			
			/* ????Put in extension???? */
			if (isset($aID_string)){
				$Barcodes[$nextIndex]->attributes = $aID_string;
			}
			
			EventManager::notify('ProductBarcodeNewBeforeExecute', &$Barcodes[$nextIndex]);
			
			$newBarcodes[] = $nextIndex;
			$nextIndex++;
		}
	}else{
		$Qcheck = Doctrine_Query::create()
		->select('barcode')
		->from('ProductsInventoryBarcodes')
		->where('barcode = ?', $barcode)
		->execute(array(), Doctrine::HYDRATE_ARRAY);
		if ($Qcheck){
			$Qproduct = Doctrine_Query::create()
			->select('products_id')
			->from('ProductsInventory')
			->where('inventory_id = ?', $Qcheck[0]['inventory_id'])
			->execute(array(), Doctrine::HYDRATE_ARRAY);
			$json = array(
				'success' => true,
				'errorMsg' => 'This barcode already exists under product "' . tep_get_products_name($Qproduct[0]['products_id']) . '"'
			);
		}else{
			if(sysConfig::get('SYSTEM_BARCODE_FORMAT') == 'Code 39'){
				if(preg_match('/[^0-9A-Z]/', $barcode)){
					$json = array(
						'success' => true,
						'errorMsg' => 'This barcode is not Code 39'
					);
				}
			}else
			if(sysConfig::get('SYSTEM_BARCODE_FORMAT') == 'Code 25' || sysConfig::get('SYSTEM_BARCODE_FORMAT') == 'Code 25 Interleaved'){
				if(preg_match('/[^0-9]/', $barcode)){
					$json = array(
						'success' => true,
						'errorMsg' => 'This barcode is not Code 25 or code 25 Interleaved'
					);
				}
				if(strlen($barcode) % 2 == 1 && sysConfig::get('SYSTEM_BARCODE_FORMAT') == 'Code 25 Interleaved'){
					$json = array(
						'success' => true,
						'errorMsg' => 'This barcode is not Code 25 Interleaved'
					);
				}

			}
			if(!isset($json['errorMsg'])){
				$Barcodes[$nextIndex]->barcode = $barcode;
				$Barcodes[$nextIndex]->status = $status;
                $Barcodes[$nextIndex]->suppliers_id = $supplier_id;
                $Barcodes[$nextIndex]->acquisition_cost = $cost;

				/* ????Put in extension???? */
				if (isset($aID_string)){
					$Barcodes[$nextIndex]->attributes = $aID_string;
				}

				EventManager::notify('ProductBarcodeNewBeforeExecute', &$Barcodes[$nextIndex]);

				$newBarcodes[] = $nextIndex;
			}
		}
	}
	$ProductsInventory->save();

    $QSuppliers = Doctrine_Query::create()
        ->from('Suppliers')
        ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

    $suppliers = htmlBase::newElement('selectbox')
        ->addClass('ui-widget-content')
        ->setName('suppliersId')
        ->addClass('suppliersId')
        ->selectOptionByValue($supplier_id);

    foreach($QSuppliers as $supplier){
        $suppliers->addOption($supplier['suppliers_id'], $supplier['suppliers_name']);
    }

	$tableRowHtml = '';
	if (sizeof($newBarcodes) > 0){
		$deleteButton = htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_DELETE'))->addClass('deleteBarcode');
		$updateButton = htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_UPDATE'))->addClass('updateBarcode');
		foreach($newBarcodes as $key){
			$barcode = $Barcodes->get($key);
			$tableRow = array();
			$tableRow[] = array(
				'addCls' => 'ui-widget-content ui-grid-cell ui-grid-cell-first centerAlign',
				'css' => array('width' => '25px'),
				'text' => '<input type="checkbox" name="barcodes[]" class="barcode_' . $type . '" value="' . $barcode->barcode_id . '">'
			);
			$tableRow[] = array(
				'addCls' => 'ui-widget-content ui-grid-cell',
				'text' => $barcode->barcode
			);
			$tableRow[] = array(
				'addCls' => 'ui-widget-content ui-grid-cell',
				'text' => $inventoryTypes[$type]
			);
			$tableRow[] = array(
				'addCls' => 'ui-widget-content ui-grid-cell centerAlign',
				'text' => $barcodeStatuses[$status]
			);
            $tableRow[] = array(
                'addCls' => 'ui-widget-content ui-grid-cell centerAlign',
                'text' => $suppliers->draw()
            );
            $tableRow[] = array(
                'addCls' => 'ui-widget-content ui-grid-cell',
                'text' => $barcode->acquisition_cost
            );
			
		 EventManager::notify('NewProductAddBarcodeListingBody', &$barcode, &$tableRow);
			
			$buttonData = array(
				'data-barcode_id' => $barcode->barcode_id,
				'data-purchase_type' => $purchaseType
			);
				
			if (isset($aID_string)){
				$buttonData['data-attribute_string'] = $aID_string;
			}
				
			$deleteButton->attr($buttonData);
			$updateButton->attr($buttonData);
			$lastColHtml = $deleteButton->draw() . ' ' . $updateButton->draw();
			
			$tableRow[] = array(
				'addCls' => 'ui-widget-content ui-grid-cell ui-grid-cell-last rightAlign',
				'css' => array('width' => '145px'),
				'text' => $lastColHtml
			);
		
			$newTr = htmlBase::newElement('tr')
			->addClass('ui-grid-row');
			foreach($tableRow as $rowInfo){
				$newTd = htmlBase::newElement('td')
				->addClass($rowInfo['addCls'])
				->html($rowInfo['text']);
			
				if (isset($rowInfo['css'])){
					foreach($rowInfo['css'] as $k => $v){
						$newTd->css($k, $v);
					}
				}
				$newTr->append($newTd);
			}
			$tableRowHtml .= $newTr->draw();
		}
		$json = array(
			'success' => true,
			'tableRow' => $tableRowHtml
		);
	}
	
	EventManager::attachActionResponse($json, 'json');
?>