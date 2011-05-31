<?php
/*
	Inventory Centers Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class inventoryCenters_admin_products_new_product extends Extension_inventoryCenters {

	public function __construct(){
		parent::__construct();
	
		$this->inventoryCenterArray = array(
			array('id' => 'none', 'text' => 'None')
		);
	}
	
	public function load(){
		global $appExtension;
		if ($this->enabled === false) return;
		
		$multiStore = $appExtension->getExtension('multiStore');
		$this->multiStoreEnabled = ($multiStore !== false && $multiStore->isEnabled() === true);
		
		if ($this->stockMethod == 'Store' && $this->multiStoreEnabled === true){
			$stores = $multiStore->getStoresArray();
			foreach($stores as $sInfo){
				$this->inventoryCenterArray[] = array(
					'id'   => $sInfo['stores_id'],
					'text' => $sInfo['stores_name']
				);
			}
		}else{
			$QinventoryCenters = Doctrine_Query::create()
			->select('inventory_center_id, inventory_center_name')
			->from('ProductsInventoryCenters')
			->orderBy('inventory_center_name')
			->execute();
			if ($QinventoryCenters->count() > 0){
				foreach($QinventoryCenters->toArray() as $cInfo){
					$this->inventoryCenterArray[] = array(
						'id'   => $cInfo['inventory_center_id'],
						'text' => $cInfo['inventory_center_name']
					);
				}
			}
		}
		
		$this->selectBox = htmlBase::newElement('selectbox');
		if ($this->stockMethod == 'Store' && $this->multiStoreEnabled === true){
			$this->selectBox->addClass('invStore')->setName('invStore');
		}else{
			$this->selectBox->addClass('invCenter')->setName('invCenter');
		}
		foreach($this->inventoryCenterArray as $cInfo){
			$this->selectBox->addOption($cInfo['id'], $cInfo['text']);
		}
		
		if ($this->stockMethod == 'Store' && $this->multiStoreEnabled === true){
			$this->stockMethodText = 'Store';
		}else{
			$this->stockMethodText = 'Inventory Center';
		}
		
		EventManager::attachEvents(array(
			'NewProductAddTrackMethods',
			'NewProductAddQuantityRows',
			'NewProductAddBarcodeOptionsHeader',
			'NewProductAddBarcodeOptionsBody',
			'NewProductAddBarcodeListingHeader',
			'NewProductAddBarcodeListingBody',
			'ProductBarcodeNewBeforeExecute',
			'NewProductLoadProductInventory',
			'SaveProductInventoryQuantity',
			'NewProductAddAttributeTrackMethods',
			'NewProductAddAttributeQuantityRows'
		), null, $this);
	}
	
	public function allowTableAddition(){
		$add = true;
		/*if (array_key_exists('useCenter', $_POST)){
			$add = ($_POST['useCenter'] == 'true');
		}*/
		return $add;
	}
	
	public function SaveProductInventoryQuantity(&$ProductsInventory, $controller, $purchaseType, $postedQty){
		global $appExtension;
		if (isset($_POST['use_center'][$controller])){
			if (isset($_POST['use_center'][$controller][$purchaseType])){
				$ProductsInventory->use_center = '1';
			}else{
				$ProductsInventory->use_center = '0';
			}
			$ProductsInventory->save();

			if (isset($postedQty[$purchaseType]['inventory_centers'])){
				if ($controller == 'normal'){
					foreach($postedQty[$purchaseType]['inventory_centers'] as $centerID => $qtyInfo){
						$QProductsInventoryQuantity = Doctrine_Query::create()
						->select('quantity_id')
						->from('ProductsInventoryQuantity')
						->where('inventory_id = ?', $ProductsInventory->inventory_id);
						if ($this->stockMethod == 'Store'){
							$QProductsInventoryQuantity->andWhere('inventory_store_id = ?', $centerID);
						}else{
							$QProductsInventoryQuantity->andWhere('inventory_center_id = ?', $centerID);
						}
						$Result = $QProductsInventoryQuantity->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

						if (!$Result){
							$ProductsInventoryQuantity = new ProductsInventoryQuantity();
							$ProductsInventoryQuantity->inventory_id = $ProductsInventory->inventory_id;
							if ($this->stockMethod == 'Store'){
								$ProductsInventoryQuantity->inventory_store_id = $centerID;
							}else{
								$ProductsInventoryQuantity->inventory_center_id = $centerID;
							}
						}else{
							$ProductsInventoryQuantity = Doctrine_Core::getTable('ProductsInventoryQuantity')
							->find($Result[0]['quantity_id']);
						}
						$ProductsInventoryQuantity->available = $qtyInfo['A'];
						$ProductsInventoryQuantity->save();
					}
				}
			}elseif ($controller == 'attribute'){
				foreach($postedQty as $aID_string => $aInfo){
					if (!isset($postedQty[$aID_string][$purchaseType])) continue;
					if (!isset($postedQty[$aID_string][$purchaseType]['inventory_centers'])) continue;
					
					$attributePermutations = attributesUtil::permutateAttributesFromString($aID_string);
					foreach($postedQty[$aID_string][$purchaseType]['inventory_centers'] as $centerID => $qtyInfo){
						$QProductsInventoryQuantity = Doctrine_Query::create()
						->select('quantity_id')
						->from('ProductsInventoryQuantity')
						->where('inventory_id = ?', $ProductsInventory->inventory_id)
						->andWhereIn('attributes', $attributePermutations);
						if ($this->stockMethod == 'Store'){
							$QProductsInventoryQuantity->andWhere('inventory_store_id = ?', $centerID);
						}else{
							$QProductsInventoryQuantity->andWhere('inventory_center_id = ?', $centerID);
						}
						$Result = $QProductsInventoryQuantity->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

						if (!$Result){
							$ProductsInventoryQuantity = new ProductsInventoryQuantity();
							$ProductsInventoryQuantity->inventory_id = $ProductsInventory->inventory_id;
							$ProductsInventoryQuantity->attributes = $aID_string;
							if ($this->stockMethod == 'Store'){
								$ProductsInventoryQuantity->inventory_store_id = $centerID;
							}else{
								$ProductsInventoryQuantity->inventory_center_id = $centerID;
							}
						}else{
							$ProductsInventoryQuantity = Doctrine_Core::getTable('ProductsInventoryQuantity')->find($Result[0]['quantity_id']);
						}
						$ProductsInventoryQuantity->available = $qtyInfo['A'];
						$ProductsInventoryQuantity->save();
					}
				}
			}
		}
	}
	
	public function NewProductLoadProductInventory(&$inventory, &$pInfo, &$productInventory){
		if (!isset($this->use_center)){
			$this->use_center = array();
		}
		$this->use_center[$inventory['controller']][$inventory['type']] = $inventory['use_center'];
	}
	
	public function ProductBarcodeNewBeforeExecute(&$Barcode){
		if ($this->stockMethod == 'Store' && $this->multiStoreEnabled === true){
			$invStore = $_POST['invStore'];
			if (!empty($invStore) && $invStore != 'none'){
				$InventoryCenter =& $Barcode->ProductsInventoryBarcodesToStores;
				$InventoryCenter->inventory_store_id = $invStore;
			}
		}else{
			$invCenter = $_POST['invCenter'];
			if (!empty($invCenter) && $invCenter != 'none'){
				$InventoryCenter =& $Barcode->ProductsInventoryBarcodesToInventoryCenters;
				$InventoryCenter->inventory_center_id = $invCenter;
			}
		}
	}
	
	public function NewProductAddAttributeTrackMethods(&$purchaseType, &$pInfo, &$trackMethodTable){
		$this->NewProductAddTrackMethods(&$purchaseType, &$pInfo, &$trackMethodTable);
	}
	
	public function NewProductAddTrackMethods($controller, &$purchaseType, &$pInfo, &$trackMethodTable){
		$inputName = 'use_center[' . $controller . '][' . $purchaseType . ']';
		
		$checkbox = htmlBase::newElement('checkbox')
		->setName($inputName)
		->setValue('1')
		->setChecked(($this->use_center[$controller][$purchaseType] == '1'))
		->setLabelPosition('after');
		if ($this->stockMethod == 'Store' && $this->multiStoreEnabled === true){
			$checkbox->setLabel('Use Stores');
		}else{
			$checkbox->setLabel('Use Inventory Centers');
		}
		
		$trackMethodTable->addBodyRow(array(
			'columns' => array(
				array('text' => '<br />' . $checkbox->draw())
			)
		));
	}
	
	public function NewProductAddBarcodeOptionsHeader(&$barcodeTableHeaders){
		if ($this->allowTableAddition() === false) return;

		$barcodeTableHeaders[] = array(
			'addCls' => 'centerAlign main',
			'text' => $this->stockMethodText
		);
	}
	
	public function NewProductAddBarcodeOptionsBody(&$barcodeTableBody){
		if ($this->allowTableAddition() === false) return;

		$barcodeTableBody[] = array(
			'addCls' => 'centerAlign main',
			'text' => $this->selectBox
		);
	}
	
	public function NewProductAddBarcodeListingHeader(&$currentBarcodesTableHeaders){
		if ($this->allowTableAddition() === false) return;

		$currentBarcodesTableHeaders[] = array(
			'addCls' => 'ui-widget-content ui-state-default ui-grid-cell',
			'text' => $this->stockMethodText
		);
	}
	
	public function NewProductAddBarcodeListingBody(&$barcodes, &$currentBarcodesTableBody){
		if ($this->allowTableAddition() === false) return;

		$QinventoryCenter = Doctrine_Query::create();
		if ($this->stockMethod == 'Store' && $this->multiStoreEnabled === true){
			$QinventoryCenter->select('s.stores_id as id, s.stores_name as name')
			->from('Stores s')
			->leftJoin('s.ProductsInventoryBarcodesToStores b2s')
			->where('b2s.barcode_id = ?', $barcodes['barcode_id']);
		}else{
			$QinventoryCenter->select('ic.inventory_center_id as id, ic.inventory_center_name as name')
			->from('ProductsInventoryCenters ic')
			->leftJoin('ic.ProductsInventoryBarcodesToInventoryCenters b2c')
			->where('b2c.barcode_id = ?', $barcodes['barcode_id']);
		}
		
		$Result = $QinventoryCenter->execute(array(), Doctrine::HYDRATE_ARRAY);

		$box = clone $this->selectBox;
		if ($Result) {
			$box->selectOptionByValue($Result[0]['id']);
		}
		$colText = $box;

		
		$currentBarcodesTableBody[] = array(
			'addCls' => 'ui-widget-content ui-grid-cell centerAlign',
			'text' => $colText
		);
	}
	
	public function NewProductAddAttributeQuantityRows($settings, $inventoryColumns, &$pInfo, &$quantityTable){
		$this->NewProductAddQuantityRows($settings, $inventoryColumns, &$pInfo, &$quantityTable);
	}
	
	public function NewProductAddQuantityRows($settings, $inventoryColumns, &$pInfo, &$quantityTable){
		if ($this->allowTableAddition() === false) return;

		$purchaseType = $settings['purchaseType'];
		$dataSet = $settings['dataSet'];
		$aID_string = (isset($settings['attributeString']) ? $settings['attributeString'] : null);
		$output = '';
		if (sizeof($this->inventoryCenterArray) > 0){
			$quantityTable->addBodyRow(array(
				'columns' => array(
					array(
						'addCls' => 'ui-widget-content ui-state-default ui-grid-cell',
						'colspan' => (sizeof($inventoryColumns)+1),
						'align' => 'center',
						'text' => '<b>' . $this->stockMethodText . '</b>'
					)
				)
			));
			
			$totalCols = sizeof($inventoryColumns);
			for($i=0, $n=sizeof($this->inventoryCenterArray); $i<$n; $i++){
				if (!empty($this->inventoryCenterArray[$i]['id']) && $this->inventoryCenterArray[$i]['id'] != 'none'){
					$centerId = $this->inventoryCenterArray[$i]['id'];
					$centerName = $this->inventoryCenterArray[$i]['text'];
					
					$rowColumns = array(
						array(
							'addCls' => 'ui-widget-content ui-state-default ui-grid-cell ui-grid-cell-first',
							'text' => '<b>' . $centerName . '</b>'
						)
					);
					
					$col = 0;
					foreach($inventoryColumns as $short => $long){
						if (($purchaseType == 'new' || $purchaseType == 'used') && ($short == 'O' || $short == 'B' || $short == 'R')) continue;
						if ($purchaseType == 'reservation' && $short == 'P') continue;
						
						$lastCell = false;
						if ($col == ($totalCols-1)){
							$lastCell = true;
						}

						$invQty = '0';
						if (isset($dataSet[$centerId])){
							$invQty = $dataSet[$centerId][$long];
						}
						
						if (is_null($aID_string) === false){
							$inputName = 'inventory_quantity[attribute][' . $aID_string . '][' . $purchaseType . '][inventory_centers][' . $centerId . '][' . $short . ']';
						}else{
							$inputName = 'inventory_quantity[normal][' . $purchaseType . '][inventory_centers][' . $centerId . '][' . $short . ']';
						}
						
						if ($short == 'A'){
							$input = htmlBase::newElement('input')
							->attr('size', '5')
							->setName($inputName)
							->setValue($invQty);
							
							if ($purchaseType == 'rental'){
								$input->attr('disabled', 'disabled');
							}
							$colText = '&nbsp;' . $input->draw() . '&nbsp;';
						}else{
							$colText = '&nbsp;' . $invQty . '&nbsp;';
						}
						
						$rowColumns[] = array(
							'addCls' => 'ui-widget-content ui-grid-cell ' . ($lastCell === true ? 'ui-grid-cell-last ': '') . 'centerAlign',
							'text' => $colText
						);
						$col++;
					}
					
					$quantityTable->addBodyRow(array(
						'columns' => $rowColumns
					));
				}
			}
		}
	}
}
?>