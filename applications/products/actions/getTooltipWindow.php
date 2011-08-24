<?php
	$product = new Product((int)$_POST['pID']);
	$pageButtons = '';
	if(sysConfig::get('TOOLTIP_DESCRIPTION_BUTTONS') == 'true') {
		$purchaseBoxes = array();
		$purchaseTypes = array();
		foreach($product->productInfo['typeArr'] as $typeName){
			$purchaseTypes[$typeName] = $product->getPurchaseType($typeName);
			if ($purchaseTypes[$typeName]){
				$settings = $purchaseTypes[$typeName]->getPurchaseHtml('product_info');
				if (is_null($settings) === false){
					EventManager::notify('ProductInfoPurchaseBoxOnLoad', &$settings, $typeName, $purchaseTypes);
					$purchaseBoxes[] = $settings;
				}
			}
		}

		$extDiscounts = $appExtension->getExtension('quantityDiscount');
		$extAttributes = $appExtension->getExtension('attributes');

		$purchaseTable = htmlBase::newElement('table')
				->addClass('ui-widget')
				->css('width', '100%')
				->setCellPadding(5)
				->setCellSpacing(0);

		$columns = array();
		foreach($purchaseBoxes as $boxInfo){
			if ($extAttributes !== false && $extAttributes->pagePlugin !== null){
				$boxInfo['content'] .= $extAttributes->pagePlugin->drawAttributes(array(
																					   'productClass' => $product,
																					   'purchase_type' => $boxInfo['purchase_type']
																				  ));
			}

			if ($extDiscounts !== false && $extDiscounts->showQuantityTable !== null && $purchaseTypes[$boxInfo['purchase_type']]->hasInventory()){
				$boxInfo['content'] .= $extDiscounts->showQuantityTable(array(
																			 'productClass' => $product,
																			 'purchase_type' => $boxInfo['purchase_type'],
																			 'product_id' => $product->getId()
																		));
			}

			$boxInfo['content'] .= tep_draw_hidden_field('products_id', $productID);

			$boxObj = htmlBase::newElement('infobox')
					->setForm(array(
								   'name' => 'cart_quantity',
								   'action' => $boxInfo['form_action']
							  ))
					->css('width', 'auto')->removeCss('margin-left')->removeCss('margin-right')
					->setHeader($boxInfo['header'])
					->setButtonBarLocation('bottom');

			if ($boxInfo['allowQty'] === true){
				$qtyInput = htmlBase::newElement('input')
						->css('margin-right', '1em')
						->setSize(3)
						->setName('quantity[' . $boxInfo['purchase_type'] . ']')
						->setLabel('Quantity:')
						->setValue(1)
						->setLabelPosition('before');

				$boxObj->addButton($qtyInput);
			}
			if(isset($boxInfo['button']) && is_object($boxInfo['button'])){
				$boxObj->addButton($boxInfo['button']);
			}

			EventManager::notifyWithReturn('ProductInfoTabImageBeforeDrawPurchaseType', &$product, &$boxObj, &$boxInfo);
	
			$boxObj->addContentRow($boxInfo['content']);

			$columns[] = array(
				'align' => 'center',
				'valign' => 'top',
				'text' => $boxObj->draw()
			);

			if (sizeof($columns) > 1){
				$purchaseTable->addBodyRow(array(
												'columns' => $columns
										   ));
				$columns = array();
			}
		}

		if (sizeof($columns) > 0){
			$columns[0]['colspan'] = 2;
			$purchaseTable->addBodyRow(array(
											'columns' => $columns
									   ));
		}


		$pageButtons = '<div style="text-align:center;">' .
			 $purchaseTable->draw() .
			 '<div style="clear:both;"></div>' .
			 '</div>' .
			 '<div style="clear:both;"></div>';
	}

	$pageHtml = '<div id="toolTipTitle">' . $product->getName() . '</div><br>' ;
	$pageHtml .= '<div id="toolTipText">' . substr(strip_tags($product->getDescription()), 0, 300) . '</div>';
	$pageHtml .= $pageButtons;


	EventManager::attachActionResponse(array(
		'success' => true,
		'pageHtml' => $pageHtml
	), 'json');
?>