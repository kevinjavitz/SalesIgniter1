<script>
$(document).ready(function (){
	$('.inventoryCalander .ui-datepicker-calendar a').each(function (){
		var dayNum = $(this).html();
		$(this).parent().html(dayNum);
	});
});
</script>
<?php
	$inventoryTypes = array(
		'new'         => 'New',
		'used'        => 'Used',
		'rental'      => 'Member Rental'
	);

	if ($appExtension->isInstalled('payPerRentals')){
		$inventoryTypes['reservation'] = 'Pay Per Rental';
	}

	$inventoryTrackMethods = array(
		'quantity' => 'Quantity',
		'barcode'  => 'Barcode'
	);

	// Could come from the inventory controllers themselves?????
	$inventoryControllers = array(
		'normal'   => array(
			'text'                  => 'Product',
			'inventoryTabFunction'  => 'getNormalInventoryTabContent',
			'showLabelPrint'        => true,
			'showInvCalander'       => true,
			'calanderPurchaseTypes' => array('rental', 'reservation'),
			'purchaseTypes'         => $inventoryTypes,
			'trackMethods'          => $inventoryTrackMethods
		)
	);

	if ($appExtension->isInstalled('attributes')){
		$inventoryControllers['attribute'] = array(
			'text'                  => 'Attribute',
			'inventoryTabFunction'  => 'getAttributeInventoryTabContent',
			'showLabelPrint'        => false,
			'showInvCalander'       => false,
			'purchaseTypes'         => $inventoryTypes,
			'trackMethods'          => $inventoryTrackMethods
		);
	}

	$productInventory = array();
	foreach($inventoryControllers as $k1 => $v1){
		foreach($inventoryTypes as $k2 => $v2){
			foreach($inventoryTrackMethods as $k3 => $v3){
				$productInventory[$k1][$k2][$k3] = array(
					'inventoryId' => '',
					'inventoryItems' => array()
				);
			}
		}
	}

	$Qinventory = Doctrine_Query::create()
	->select('type, track_method, inventory_id, controller')
	->from('ProductsInventory')
	->where('products_id = ?', $Product['products_id']);
	if ($appExtension->isInstalled('inventoryCenters')){
		$Qinventory->addSelect('use_center');
	}
	$Result = $Qinventory->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if ($Result){
		$invCentersInstalled = $appExtension->isInstalled('inventoryCenters');
		$multiStoreInstalled = $appExtension->isInstalled('multiStore');
		$extInvCenter = $appExtension->getExtension('inventoryCenters');
		foreach($Result as $inventory){
			$invController = $inventory['controller'];
			$invTrackMethod = $inventory['track_method'];
			$invType = $inventory['type'];
			$invId = $inventory['inventory_id'];

			$productInventory[$invController][$invType][$invTrackMethod] = array(
				'inventoryId' => $invId,
				'inventoryItems' => array()
			);

			$track_method[$invController][$inventory['type']] = $inventory['track_method'];

			$QinventoryQuantity = Doctrine_Query::create()
			->from('ProductsInventoryQuantity')
			->where('inventory_id = ?', $invId);
			if ($invController == 'attribute'){
				$QinventoryQuantity->andWhere('attributes IS NOT NULL');
			}else{
				$QinventoryQuantity->andWhere('attributes IS NULL');
			}

			$Result = $QinventoryQuantity->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if ($Result){
				foreach($Result as $invItemInfo){
					$centerId = 0;
					if ($invCentersInstalled === true){
						if ($multiStoreInstalled === true && $extInvCenter->stockMethod == 'Store'){
							$centerId = $invItemInfo['inventory_store_id'];
						}else{
							$centerId = $invItemInfo['inventory_center_id'];
						}
					}

					if ($invController == 'attribute'){
						$productInventory[$invController][$invType]['quantity']['inventoryItems'][$invItemInfo['attributes']][$centerId] = $invItemInfo;
					}else{
						$productInventory[$invController][$invType]['quantity']['inventoryItems'][$centerId] = $invItemInfo;
					}
				}
			}

			$Qbarcodes = Doctrine_Query::create()
			->from('ProductsInventoryBarcodes')
			->where('inventory_id = ?', $invId);
			if ($invController == 'attribute'){
				$QinventoryQuantity->andWhere('attributes IS NOT NULL');
			}else{
				$QinventoryQuantity->andWhere('attributes IS NULL');
			}

			$Result = $Qbarcodes->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if ($Result){
				foreach($Result as $invItemInfo){
					if ($invController == 'attribute'){
						$productInventory[$invController][$invType]['barcode']['inventoryItems'][$invItemInfo['attributes']][] = $invItemInfo;
					}else{
						$productInventory[$invController][$invType]['barcode']['inventoryItems'][] = $invItemInfo;
					}
				}
			}

			EventManager::notify('NewProductLoadProductInventory', &$inventory, &$pInfo, &$productInventory);
		}
	}

	foreach($inventoryTypes as $typeId => $typeName){
		foreach($inventoryControllers as $controller => $cInfo){
			if (!isset($track_method[$invController])){
				$track_method[$invController] = array();
			}

			if (!isset($track_method[$invController][$typeId])){
				$track_method[$invController][$typeId] = '';
			}

			if (empty($track_method[$invController][$typeId])){
				$track_method[$invController][$typeId] = 'barcode';
			}
		}
	}

	//echo '<pre>';print_r($productInventory);echo '</pre>';
	$labelTypes = array(
		array('id' => '5164', 'text' => 'Avery 5164'),
		array('id' => 'pinfo_html', 'text' => 'Product Info HTML'),
		array('id' => 'barcodes', 'text' => 'Barcodes')
	);
?>
 <table cellpadding="3" cellspacing="0" border="0">
  <tr>
   <td><h3>Inventory Management</h3></td>
  </tr>
  <tr>
   <td><?php
   	foreach($inventoryControllers as $k => $v){
   		echo htmlBase::newElement('radio')
   		->setName('products_inventory_controller')
   		->setChecked($Product['products_inventory_controller'] == $k)
   		->val($k)
   		->setLabel('Use ' . $v['text'] . ' Based Inventory')
   		->setLabelPosition('after')
   		->setLabelSeparator('&nbsp;')
   		->draw() . '<br />';
   	}
   ?></td>
  </tr>
 </table>
 <br />
<?php
	$inventoryControllerTabs = htmlBase::newElement('tabs')
	->setId('inventory_tabs');
	foreach($inventoryControllers as $controllerName => $controllerSettings){
		$baseTabPageContent = '';
		if ($controllerSettings['showLabelPrint'] === true){
			$labelPrintTable = buildPrintLabelTable();
			$baseTabPageContent .= $labelPrintTable->draw();
		}

		$inventoryTypeTabs = htmlBase::newElement('tabs')
		->setId('inventory_tab_' . $controllerName . '_tabs');
		foreach($controllerSettings['purchaseTypes'] as $purchaseType => $typeName){
			$purchaseTypeTabPageContent = '';
			$trackMethodTable = buildTrackMethodTable(array(
				'purchaseType' => $purchaseType,
				'controller'   => $controllerName,
				'trackMethods' => $controllerSettings['trackMethods'],
				'trackMethod'  => $track_method[$controllerName][$purchaseType]
			));

			EventManager::notify('NewProductAddTrackMethods', $controllerName, &$purchaseType, &$Product, &$trackMethodTable);

			$purchaseTypeTabPageContent .= $trackMethodTable->draw();

			if ($controllerSettings['showInvCalander'] === true){
				if (in_array($purchaseType, $controllerSettings['calanderPurchaseTypes'])){
					$calanderTable = buildInventoryCalanderTable(array(
						'purchaseType' => $purchaseType
					));
					$purchaseTypeTabPageContent .= $calanderTable->draw();
				}
			}

			$purchaseTypeTabPageContent .= '<br /><hr /><br />';

			$purchaseTypeTabPageContent .= call_user_func($controllerSettings['inventoryTabFunction'], array(
				'productId'    => $Product['products_id'],
				'purchaseType' => $purchaseType,
				'dataSet'      => $productInventory[$controllerName][$purchaseType]
			));

			$inventoryTypeTabs->addTabHeader('inventory_tab_' . $controllerName . '_tabs_' . $purchaseType, array(
				'text' => $typeName
			))->addTabPage('inventory_tab_' . $controllerName . '_tabs_' . $purchaseType, array(
				'text' => $purchaseTypeTabPageContent
			));
		}

		$inventoryControllerTabs->addTabHeader('inventory_tab_' . $controllerName, array(
			'text' => $controllerSettings['text'] . ' Based'
		))->addTabPage('inventory_tab_' . $controllerName, array(
			'text' => $baseTabPageContent . $inventoryTypeTabs->draw()
		));
	}
	echo $inventoryControllerTabs->draw();
?>
 <div id="5164_dialog" style="display:none;">
  <table cellpadding="3" cellspacing="0" border="0">
   <tr>
    <td align="right"><input type="radio" name="labelPos" value="0"></td>
    <td class="main"><?php echo sysLanguage::get('TEXT_TOP_LEFT');?></td>
    <td align="right"><input type="radio" name="labelPos" value="1"></td>
    <td class="main"><?php echo sysLanguage::get('TEXT_TOP_RIGHT');?></td>
   </tr>
   <tr>
    <td align="right"><input type="radio" name="labelPos" value="2"></td>
    <td class="main"><?php echo sysLanguage::get('TEXT_CENTER_LEFT');?></td>
    <td align="right"><input type="radio" name="labelPos" value="3"></td>
    <td class="main"><?php echo sysLanguage::get('TEXT_CENTER_RIGHT');?></td>
   </tr>
   <tr>
    <td align="right"><input type="radio" name="labelPos" value="4"></td>
    <td class="main"><?php echo sysLanguage::get('TEXT_BOTTOM_LEFT');?></td>
    <td align="right"><input type="radio" name="labelPos" value="5"></td>
    <td class="main"><?php echo sysLanguage::get('TEXT_BOTTOM_RIGHT');?></td>
   </tr>
  </table>
 </div>