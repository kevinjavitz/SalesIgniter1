<?php
	if (isset($_POST['attributes_prefix'])){
		$prefix = $_POST['attributes_prefix'];
		$prices = $_POST['attributes_price'];
		$useInventory = (isset($_POST['attributes_inventory']) ? $_POST['attributes_inventory'] : '');
		$purchaseTypes = (isset($_POST['attributes_purchase_types']) ? $_POST['attributes_purchase_types'] : '');
		
		if (isset($_POST['attributes_previous_images'])){
			$previousImages = $_POST['attributes_previous_images'];
		}
		
		if (isset($_POST['attributes_view_image_name'])){
			$viewNames = $_POST['attributes_view_image_name'];
			$viewImages = $_FILES['attributes_view_image_file'];
		}
		
		if (isset($_FILES['attributes_value_image'])){
			$valueImages = $_FILES['attributes_value_image'];
		}
		
		$ProductAttributes =& $Product->ProductsAttributes;
		$ProductAttributes->delete();

		$counter = 0;
		foreach($prefix as $groupId => $options){
			foreach($options as $optionId => $values){
				foreach($values as $valueId => $prefixValue){
					$valuesPrice = $prices[$groupId][$optionId][$valueId];
					if ($groupId > 0){
						$ProductAttributes[$counter]->groups_id = $groupId;
					}
					$ProductAttributes[$counter]->options_id = $optionId;
					$ProductAttributes[$counter]->options_values_id = $valueId;
					$ProductAttributes[$counter]->options_values_price = $valuesPrice;
					$ProductAttributes[$counter]->price_prefix = $prefixValue;
					$ProductAttributes[$counter]->sort_order = isset($_POST['option_' . $optionId . '_sort'])?(int)$_POST['option_' . $optionId . '_sort']:0;
					
					$ProductAttributes[$counter]->purchase_types = '';
					if (is_array($purchaseTypes) && isset($purchaseTypes[$groupId][$optionId][$valueId])){
						$ProductAttributes[$counter]->purchase_types = implode(',', $purchaseTypes[$groupId][$optionId][$valueId]);
					}
					
					$ProductAttributes[$counter]->use_inventory = '0';
					if (is_array($useInventory) && isset($useInventory[$groupId][$optionId][$valueId])){
						$ProductAttributes[$counter]->use_inventory = '1';
					}
					
					if (isset($valueImages) && isset($valueImages['name'][$groupId][$optionId][$valueId])){
						$imageUpload = new upload(array(
							'name'     => $valueImages['name'][$groupId][$optionId][$valueId],
							'size'     => $valueImages['size'][$groupId][$optionId][$valueId],
							'tmp_name' => $valueImages['tmp_name'][$groupId][$optionId][$valueId],
							'error'    => $valueImages['error'][$groupId][$optionId][$valueId],
							'type'     => $valueImages['type'][$groupId][$optionId][$valueId]
						));
						$imageUpload->set_extensions(array('jpg', 'gif', 'png'));
						$imageUpload->set_destination(sysConfig::get('DIR_FS_CATALOG_IMAGES'));
						if ($imageUpload->parse() && $imageUpload->save()){
							$imageName = $imageUpload->filename;
						}else{
							if (isset($previousImages[$groupId][$optionId][$valueId])){
								$imageName = $previousImages[$groupId][$optionId][$valueId];
							}
						}
						$ProductAttributes[$counter]->options_values_image = $imageName;
					}
				
					if (isset($viewNames) && isset($viewNames[$groupId][$optionId][$valueId])){
						$counter2 = 0;
						foreach($viewNames[$groupId][$optionId][$valueId] as $idx => $viewName){
							$ProductAttributes[$counter]->ProductsAttributesViews[$counter2]->view_name = $viewName;

							$imageUpload = new upload(array(
								'name'     => $viewImages['name'][$groupId][$optionId][$valueId][$idx],
								'size'     => $viewImages['size'][$groupId][$optionId][$valueId][$idx],
								'tmp_name' => $viewImages['tmp_name'][$groupId][$optionId][$valueId][$idx],
								'error'    => $viewImages['error'][$groupId][$optionId][$valueId][$idx],
								'type'     => $viewImages['type'][$groupId][$optionId][$valueId][$idx]
							));
							$imageUpload->set_extensions(array('jpg', 'gif', 'png'));
							$imageUpload->set_destination(sysConfig::get('DIR_FS_CATALOG_IMAGES'));
							if ($imageUpload->parse() && $imageUpload->save()){
								$imageName = $imageUpload->filename;
							}else{
								if (isset($previousImages[$groupId][$optionId][$valueId])){
									$imageName = $previousImages[$groupId][$optionId][$valueId];
								}
							}
							$ProductAttributes[$counter]->ProductsAttributesViews[$counter2]->view_image = $imageName;
							$counter2++;
						}
					}
					$counter++;
				}
			}
		}
		$Product->save();
	}
	
	$extAttributes = $appExtension->getExtension('attributes');
	$postedQty = (isset($_POST['inventory_quantity']['attribute']) ? $_POST['inventory_quantity']['attribute'] : false);
	foreach($inventoryTypes as $typeShort => $typeName){
		$QproductInventory = Doctrine_Query::create()
		->select('inventory_id')
		->from('ProductsInventory')
		->where('type = ?', $typeShort)
		->andWhere('controller = ?', 'attribute')
		->andWhere('products_id = ?', $Product->products_id)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if (!$QproductInventory){
			$ProductsInventory = new ProductsInventory();
			$ProductsInventory->products_id = $Product->products_id;
			$ProductsInventory->type = $typeShort;
			$ProductsInventory->controller = 'attribute';
		}else{
			$ProductsInventory = Doctrine_Core::getTable('ProductsInventory')->find($QproductInventory[0]['inventory_id']);
		}
		if ($appExtension->isInstalled('inventoryCenters')){
			if (isset($_POST['use_center']['attribute'][$typeShort])){
				$ProductsInventory->use_center = '1';
			}else{
				$ProductsInventory->use_center = '0';
			}
		}
		if (isset($_POST['track_method']['attribute'][$typeShort])){
			$ProductsInventory->track_method = $_POST['track_method']['attribute'][$typeShort];
		}
		$ProductsInventory->save();
		
		if ($postedQty !== false){
			$invId = $ProductsInventory->inventory_id;
		
			foreach($postedQty as $aID_string => $tInfo){
				if (isset($tInfo[$typeShort])){
					$attributePermutations = attributesUtil::permutateAttributesFromString($aID_string);
					foreach($tInfo[$typeShort] as $invInfo){
						$QProductsInventoryQuantity = Doctrine_Query::create()
						->from('ProductsInventoryQuantity')
						->where('inventory_id = ?', $invId)
						->andWhereIn('attributes', $attributePermutations);

						if ($appExtension->isInstalled('inventoryCenters')){
							$invExt = $appExtension->getExtension('inventoryCenters');
							if ($appExtension->isInstalled('multiStore') && $invExt->stockMethod == 'Store'){
								$QProductsInventoryQuantity->andWhere('inventory_store_id = ?', '0');
							}else{
								$QProductsInventoryQuantity->andWhere('inventory_center_id = ?', '0');
							}
						}

						$Result = $QProductsInventoryQuantity->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

						if (!$Result){
							$ProductsInventoryQuantity = new ProductsInventoryQuantity();
							$ProductsInventoryQuantity->inventory_id = $invId;
							$ProductsInventoryQuantity->attributes = $aID_string;
						}else{
							$ProductsInventoryQuantity = Doctrine_Core::getTable('ProductsInventoryQuantity')->find($Result[0]['quantity_id']);
						}
                        if (isset($invInfo['A'])){
						    $ProductsInventoryQuantity->available = $invInfo['A'];
                        }else{
                            $ProductsInventoryQuantity->available = 0;
                        }
						$ProductsInventoryQuantity->save();
		
						EventManager::notify(
							'SaveProductInventoryQuantity',
							&$ProductsInventory,
							'attribute',
							$typeShort,
							$postedQty
						);
					}
				}
			}
		}
	}
?>