<?php
	function buildPrintLabelTable(){
		global $labelTypes;
		$printButton = htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_PRINT'))->attr('id', 'printLabels');
		
		$labelTableContainer = htmlBase::newElement('table')
		->setCellPadding(3)
		->setCellSpacing(0)
		->css('width', '95%');
		
		$labelTable = htmlBase::newElement('table')
		->setCellPadding(3)
		->setCellSpacing(0);
		
		if (!isset($_GET['pID'])){
			$labelTable->disable(true);
		}
		
		$labelTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'colspan' => 2, 'text' => '<b>Print Labels</b>')
			)
		));
		
 		$labelTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => 'Label Type:'),
				array('addCls' => 'main', 'text' => tep_draw_pull_down_menu('label_type', $labelTypes, '', 'id="labelsType"'))
			)
		));
		
		$labelTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'colspan' => 2, 'text' => $printButton)
			)
		));
		
		$labelTableContainer->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => ''),
				array('addCls' => 'main', 'align' => 'right', 'text' => $labelTable)
			)
		));
		return $labelTableContainer;
	}
	
	function buildTrackMethodTable($settings){
		global $track_method;
		$invController = $settings['controller'];
		$purchaseType = $settings['purchaseType'];
		$trackMethods = $settings['trackMethods'];
		$purchaseTypeClass = PurchaseTypeModules::getModule($purchaseType);

		$trackMethodTable = htmlBase::newElement('table')
		->setCellPadding(3)->setCellSpacing(0)->css('width', '98%');
		foreach($trackMethods as $id => $text){

			$radioField = htmlBase::newElement('radio')
			->addClass('trackMethodButton')
			->setName('track_method[' . $invController . '][' . $purchaseType . ']')
			->val($id)
			->setLabel('Use ' . $text . ' Tracking')
			->setLabelPosition('after')
			->setLabelSeparator('&nbsp;');
			if ($purchaseTypeClass->getConfigData('INVENTORY_' . strtoupper($id) . '_ENABLED') == 'False'){
				$radioField->disable(true);
			}elseif (isset($track_method) && $track_method[$invController][$purchaseType] == $id){
				$radioField->setChecked(true);
			}

			$trackMethodTable->addBodyRow(array(
				'columns' => array(
					array('addCls' => 'main', 'text' => $radioField)
				)
			));
		}
		return $trackMethodTable;
	}
	
	function buildInventoryCalanderTable($settings){
		global $appExtension;
		
		$purchaseType = $settings['purchaseType'];
      	
      	$calanderTable = htmlBase::newElement('table')
      	->setCellPadding(2)
      	->setCellSpacing(0)
      	->css(array(
      		'margin-left' => 'auto',
      		'margin-right' => 'auto'
      	));

		$inventoryReports = htmlBase::newElement('button')
							->setHref(itw_app_link('appExt=payPerRentals&purchase_type_field='. $purchaseType . '&productsID=' . $_GET['pID'],'reservations_reports','default'),false,'_blank')
							->setText('View Inventory Calendar');


      	$calanderTable->addBodyRow(array(
      		'columns' => array(
      			array('addCls' => 'main', 'text' => $inventoryReports->draw())
      		)
      	));
      	return $calanderTable;
	}
	
	function buildBarcodeEntryTable($settings){
		global $barcodeStatuses;
		$addButton = htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_ADD'))->addClass('addBarcode');
		
		$barcodeTableHeaders = array(
			array('addCls' => 'main', 'text' => 'Barcode'),
			array('addCls' => 'main', 'text' => 'Type'),
			array('addCls' => 'main', 'text' => 'Status'),
		);

		EventManager::notify('NewProductAddBarcodeOptionsHeader', &$barcodeTableHeaders);

		$barcodeTableHeaders[] = array('addCls' => 'rightAlign main', 'text' => 'Action');

		$barcodeInput = htmlBase::newElement('input')
		->setName('barcodeNumber')
		->addClass('barcodeNumber');

		$autoGenTextInput = htmlBase::newElement('input')
		->setSize(3)
		->setName('autogenTotal')
		->addClass('autogenTotal')
		->disable(true);

		$autoGenCheckboxInput = htmlBase::newElement('checkbox')
		->addClass('autogen')
		->setName('autogen')
		->setLabel('Auto Generate')
		->setLabelPosition('after')
		->setLabelSeparator('&nbsp;');

		$barcodeTableBody = array(
			array(
				'addCls' => 'main',
				'text' => $barcodeInput->draw() . '<br />' . $autoGenTextInput->draw() . $autoGenCheckboxInput->draw()
			),
			array(
				'addCls' => 'centerAlign main',
				'text' => $settings['purchaseType']
			),
			array(
				'addCls' => 'centerAlign main',
				'text' => $barcodeStatuses['A']
			)
		);

		EventManager::notify('NewProductAddBarcodeOptionsBody', &$barcodeTableBody);

		if (isset($settings['attributeString'])){
			$addButton->attr('data-attribute_string', $settings['attributeString']);
		}
		$addButton->attr('data-purchase_type', $settings['purchaseType']);
		
		$barcodeTableBody[] = array('addCls' => 'rightAlign main', 'text' => $addButton);

		$barcodeTable = htmlBase::newElement('table')
		->setCellPadding(3)
		->setCellSpacing(0)
		->css('width', '95%')
		->addHeaderRow(array(
			'columns' => $barcodeTableHeaders
		))
		->addBodyRow(array(
			'columns' => $barcodeTableBody
		));
		return $barcodeTable;
	}
	
	function buildCurrentBarcodesTable($settings){
		global $barcodeStatuses;
		$dataSet = $settings['dataSet'];
		$purchaseType = $settings['purchaseType'];
		
		$deleteButton = htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_DELETE'))->addClass('deleteBarcode');
		$updateButton = htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_UPDATE'))->addClass('updateBarcode');
		$commentButton = htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_COMMENT'))->addClass('commentBarcode');

		$checkAllBox = htmlBase::newElement('checkbox')
		->addClass('checkAll')
		->val($purchaseType);

		$currentBarcodesTableHeaders = array(
			array(
				'addCls' => 'ui-widget-content ui-state-default ui-grid-cell ui-grid-cell-first centerAlign',
				'text' => $checkAllBox
			),
			array(
				'addCls' => 'ui-widget-content ui-state-default ui-grid-cell',
				'text' => 'Barcode'
			),
			array(
				'addCls' => 'ui-widget-content ui-state-default ui-grid-cell',
				'text' => 'Type'
			),
			array(
				'addCls' => 'ui-widget-content ui-state-default ui-grid-cell',
				'text' => 'Status'
			)
		);

		EventManager::notify('NewProductAddBarcodeListingHeader', &$currentBarcodesTableHeaders);

		$currentBarcodesTableHeaders[] = array(
			'css' => array(
				'width' => '145px'
			),
			'addCls' => 'ui-widget-content ui-state-default ui-grid-cell ui-grid-cell-last',
			'text' => 'Action'
		);

		$currentBarcodesTable = htmlBase::newElement('table')
		->setCellPadding(3)
		->setCellSpacing(0)
		->css('width', '100%')
		->addClass('currentBarcodeTable')
		->addHeaderRow(array(
			'addCls' => 'ui-grid-row ui-grid-heading-row',
			'columns' => $currentBarcodesTableHeaders
		));
		
		$row = 0;
		if (!empty($dataSet)){
			foreach($dataSet as $bInfo){
				$currentBarcodesTableBody = array(
					array(
						'addCls' => 'ui-widget-content ui-grid-cell ui-grid-cell-first centerAlign',
						'text' => '<input type="checkbox" name="barcodes[]" value="' . $bInfo['barcode_id'] . '" class="barcode_' . $purchaseType . '">'
					),
					array(
						'addCls' => 'ui-widget-content ui-grid-cell',
						'text' => $bInfo['barcode']
					),
					array(
						'addCls' => 'ui-widget-content ui-grid-cell',
						'text' => $purchaseType
					),
					array(
						'addCls' => 'ui-widget-content ui-grid-cell',
						'text' => $barcodeStatuses[$bInfo['status']]
					)
				);

				EventManager::notify('NewProductAddBarcodeListingBody', &$bInfo, &$currentBarcodesTableBody);

				if ($bInfo['status'] == 'R' || $bInfo['status'] == 'O'){
					$lastColHtml = '&nbsp;';
				}else{
					$buttonData = array(
						'data-barcode_id' => $bInfo['barcode_id'],
						'data-purchase_type' => $purchaseType
					);
				
					if (isset($settings['attribute_string'])){
						$buttonData['data-attribute_string'] = $settings['attributeString'];
					}
				
					$deleteButton->attr($buttonData);
					$updateButton->attr($buttonData);
					$lastColHtml = $deleteButton->draw() . ' ' . $updateButton->draw();
				}

				$buttonData = array(
						'data-barcode_id' => $bInfo['barcode_id']
				);
				$commentButton->attr($buttonData);
				$lastColHtml .= ' ' . $commentButton->draw();

				$currentBarcodesTableBody[] = array(
					'css' => array(
						'padding' => '4px',
						'white-space' => 'nowrap',
						'font-size' => '.75em'
					),
					'addCls' => 'ui-widget-content ui-grid-cell ui-grid-cell-last centerAlign',
					'text' => $lastColHtml
				);

				$currentBarcodesTable->addBodyRow(array(
					'addCls' => 'ui-grid-row noHover',
					'columns' => $currentBarcodesTableBody
				));
				$row ++;
			}
		}
		return $currentBarcodesTable;
	}
	
	function buildQuantityTable($settings){
		global $inventoryColumns, $barcodeStatuses, $pInfo;
		
		$dataSet = $settings['dataSet'];
//		echo '<pre>';print_r($dataSet);echo '</pre>';
		$purchaseType = $settings['purchaseType'];
		$aID_string = (isset($settings['attributeString']) ? $settings['attributeString'] : null);
		
		$quantityTableHeaders = array(
			array('addCls' => 'ui-widget-content ui-state-default ui-grid-cell ui-grid-cell-first', 'text' => '')
		);
		
		$quantityTableBody = array(
			array('addCls' => 'ui-widget-content ui-state-default ui-grid-cell ui-grid-cell-first', 'text' => '<b>Standard</b>')
		);

		foreach($inventoryColumns as $short => $long){
			if (($purchaseType == 'new' || $purchaseType == 'used') && ($short == 'O' || $short == 'B' || $short == 'R')) continue;
			if ($purchaseType == 'reservation' && $short == 'P') continue;

			if (is_null($aID_string) === false){
				$inputName = 'inventory_quantity[attribute][' . $aID_string . '][' . $purchaseType . '][' . $short . ']';
			}else{
				$inputName = 'inventory_quantity[normal][' . $purchaseType . '][' . $short . ']';
			}
			
			$invQty = '0';
			if (isset($dataSet[0][$long])){
				$invQty = $dataSet[0][$long];
			}
			
			if ($short == 'A'){
				$inputObj = htmlBase::newElement('input')
				->setSize(5)
				->setName($inputName)
				->addClass('quantityInput')
				->attr('data-purchase_type', $purchaseType)
				->attr('data-availability', $short)
				->val($invQty);
				if (is_null($aID_string) === false){
					$inputObj->attr('data-attribute_string', $aID_string);
				}
				if ($purchaseType == 'rental'){
					$inputObj->disable(true);
				}
				$inputHtml = $inputObj->draw();
			}else{
				$inputHtml = $invQty;
			}
			
			$quantityTableHeaders[] = array(
				'addCls' => 'ui-widget-content ui-state-default ui-grid-cell',
				'text' => '<b>' . $barcodeStatuses[$short] . '</b>'
			);
			
			$quantityTableBody[] = array(
				'attr' => array(
					'data-availability' => $short
				),
				'addCls' => 'ui-widget-content ui-grid-cell centerAlign',
				'text' => '&nbsp;' . $inputHtml . '&nbsp;'
			);
		}
		
		$quantityTable = htmlBase::newElement('table')
		->setCellPadding(3)
		->setCellSpacing(0)
		->addClass('ui-grid')
		->addHeaderRow(array(
			'addCls' => 'ui-grid-row ui-grid-heading-row',
			'columns' => $quantityTableHeaders
		))
		->addBodyRow(array(
			'columns' => $quantityTableBody
		));
		
		EventManager::notify('NewProductAddQuantityRows', $settings, $inventoryColumns, &$pInfo, &$quantityTable);
		return $quantityTable;
	}
	
	function getNormalInventoryTabContent($settings){
		$dataSet = $settings['dataSet'];
		$purchaseType = $settings['purchaseType'];
		$productId = $settings['productId'];
		$purchaseTypeClass = PurchaseTypeModules::getModule($purchaseType);

		$useQuantityTable = $purchaseTypeClass->getConfigData('INVENTORY_QUANTITY_ENABLED') == 'True';
		$useBarcodeTable = $purchaseTypeClass->getConfigData('INVENTORY_BARCODE_ENABLED') == 'True';

		$contentContainer = htmlBase::newElement('div')
		->addClass('main');

		if ($useQuantityTable === true){
			$quantityTable = buildQuantityTable(array(
					'purchaseType' => $purchaseType,
					'inventoryId' => $dataSet['quantity']['inventoryId'],
					'dataSet' => $dataSet['quantity']['inventoryItems']
					));

			$quantityTableHeader = htmlBase::newElement('div')
				->addClass('ui-widget ui-widget-header ui-corner-top centerAlign')
				->css(array(
					'padding' => '.5em'
				))
				->html('Quantity');
		}

		if ($useBarcodeTable === true){
			$barcodeTableContainer = htmlBase::newElement('div')
				->addClass('ui-widget ui-widget-content ui-corner-all')
				->attr('data-purchase_type', $purchaseType)
				->css(array(
					'width'     => '100%',
					'padding'   => '.3em',
					'font-size' => '12px'
				));

			if (!isset($_GET['pID'])){
				$barcodeTableContainer->disable(true);
			}

			$barcodeTable = buildBarcodeEntryTable(array(
					'purchaseType' => $purchaseType
				));

			$ajaxNotice = htmlBase::newElement('div')
				->addClass('main')
				->html('<small>*Barcodes are dynamically added and do not require the product to be updated</small>');

			$currentBarcodesHeader = htmlBase::newElement('div')
				->addClass('ui-widget ui-widget-header ui-corner-top centerAlign')
				->css(array(
					'padding' => '.5em',
					'text-align' => 'center'
				))
				->html('Current Barcodes');

			$currentBarcodesTable = buildCurrentBarcodesTable(array(
					'purchaseType' => $purchaseType,
					'inventoryId' => $dataSet['barcode']['inventoryId'],
					'dataSet' => $dataSet['barcode']['inventoryItems']
				));

			$barcodeTableContainer->append($barcodeTable)
				->append($ajaxNotice)
				->append(htmlBase::newElement('hr'))
				->append($currentBarcodesHeader)
				->append($currentBarcodesTable);
		}

		if ($useQuantityTable === true){
			$contentContainer->append($quantityTableHeader)
				->append($quantityTable)
				->append(htmlBase::newElement('br'))
				->append(htmlBase::newElement('hr'))
				->append(htmlBase::newElement('br'));
		}

		if ($useBarcodeTable === true){
			$contentContainer->append($barcodeTableContainer);
		}
		
		return $contentContainer->draw();
	}
	
	function getAttributeInventoryTabContent($settings){
		global $appExtension;
		$dataSet = $settings['dataSet'];
		$purchaseType = $settings['purchaseType'];
		$productId = $settings['productId'];
		$tabContent = '';
		$extAttributes = $appExtension->getExtension('attributes');
		
		if (isset($_GET['pID'])){
			$attributesContainer = htmlBase::newElement('div')
			->addClass('main');
			
			$ProductsAttributes = attributesUtil::getAttributes((int)$_GET['pID'], null, null, $purchaseType, null);
			//print_r($ProductsAttributes);
			$Attributes = attributesUtil::organizeAttributeArray($ProductsAttributes);
		    //echo 'ooo'.print_r($Attributes);
			//itwExit();
			$hasOptions = false;
			foreach($Attributes as $optionId => $aInfo){
				$input = htmlBase::newElement('selectbox')
				->css('margin-right', '.75em')
				->setName('attribute_inventory_option[' . $optionId . ']')
				->addClass('attributeStockOption')
				->setLabel($aInfo['options_name'])
				->setLabelPosition('before')
				->setLabelSeparator(':&nbsp;');
			
				foreach($aInfo['ProductsOptionsValues'] as $options){
					$input->addOption($options['options_values_id'], $options['options_values_name']);
					$hasOptions = true;
				}
				$attributesContainer->append($input);

			}
			
			$addButton = htmlBase::newElement('button')
			->attr('data-purchase_type', $purchaseType)
			->usePreset('install')
			->setText('Add')
			->addClass('attributeStockAddButton');
			if($hasOptions){
				$attributesContainer->append($addButton);
			}
		}
		
		if (isset($attributesContainer)){
			$tabContent .= $attributesContainer->draw() . 
						   htmlBase::newElement('hr')->draw() . 
						   htmlBase::newElement('br')->draw();
		}
		
		$attributeInventoryTables = array();
		if (!empty($dataSet)){
			if (isset($dataSet['quantity'])){
				foreach($dataSet['quantity']['inventoryItems'] as $aID_string => $aInfo){
					$attributeInventoryTables[] = $extAttributes->pagePlugin->getInventoryTable(array(
						'productId'    => $productId,
						'purchaseType' => $purchaseType,
						'trackMethod'  => 'quantity',
	    		 		'dataSet'      => $dataSet['quantity']['inventoryItems'][$aID_string],
						'options'      => attributesUtil::splitStringToArray($aID_string)
					));
				}
			}
			
			if (isset($dataSet['barcode'])){
				foreach($dataSet['barcode']['inventoryItems'] as $aID_string => $aInfo){
					$attributeInventoryTables[] = $extAttributes->pagePlugin->getInventoryTable(array(
						'productId'    => $productId,
						'purchaseType' => $purchaseType,
						'trackMethod'  => 'barcode',
		    	 		'dataSet'      => $dataSet['barcode']['inventoryItems'][$aID_string],
						'options'      => attributesUtil::splitStringToArray($aID_string)
					));
				}
			}
		}
		return $tabContent . '<div class="attributesInventoryTables">' . implode('', $attributeInventoryTables) . '</div>';
	}
?>