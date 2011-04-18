<?php
	$product = new product((int)$_GET['products_id']);
	$productsTypes = array();
	foreach($typeNames as $nameShort => $nameLong){
		if ($product->canBuy($nameShort) === true){
			$productsTypes[] = array(
				'id'   => $nameShort,
				'text' => $nameLong
			);
		}
	}

	if (sizeof($productsTypes) <= 0){
		$html = 'No Purchase Types Available';
	}else{
		$table = htmlBase::newElement('table')
		->setCellPadding(2)
		->setCellSpacing(0);
		
		$table->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => 'Purchase Type:'),
				array('addCls' => 'main', 'text' => tep_draw_pull_down_menu('purchase_type', $productsTypes, '', 'id="purchaseType"'))
			)
		));

		if ($product->canBuy('reservation')){
			$table->addBodyRow(array(
				'addCls'  => 'reservationRow',
				'css'     => array(
					'display' => 'none'
				),
				'columns' => array(
					array(
						'addCls' => 'main',
						'text'   => 'Start Date:'
					),
					array(
						'addCls' => 'main',
						'text'   => '<input type="text" name="start_date" id="start_date">'
					)
				)
			));
			$table->addBodyRow(array(
				'addCls'  => 'reservationRow',
				'css'     => array(
					'display' => 'none'
				),
				'columns' => array(
					array(
						'addCls' => 'main',
						'text'   => 'End Date:'
					),
					array(
						'addCls' => 'main',
						'text'   => '<input type="text" name="end_date" id="end_date">'
					)
				)
			));
			$table->addBodyRow(array(
				'addCls'  => 'reservationRow',
				'css'     => array(
					'display' => 'none'
				),
				'columns' => array(
					array(
						'addCls'  => 'main',
						'attr'    => array(
							'colspan' => '2'
						),
						'text'    => trim(str_replace("\n", '', $product->buildShippingTable(true, true)))
					)
				)
			));
		}

		if ($product->getTrackMethod($productsTypes[0]['id']) == 'barcode'){
			$barcodeArray = array();
			$barcodes = $product->getInventoryItems($productsTypes[0]['id']);
			foreach($barcodes as $barcode){
				$barcodeArray[] = array(
					'id'   => $barcode['id'],
					'text' => $barcode['barcode']
				);
			}

			$barcodeHtml = tep_draw_pull_down_menu('products_barcode', $barcodeArray, '', 'id="barcodes"');
		}else{
			$barcodeHtml = '<span id="barcodes">Purchase type does not use barcodes</span>';
		}
		$table->addBodyRow(array(
			'columns' => array(
				array(
					'addCls' => 'main',
					'text'   => 'Products Barcode:'
				),
				array(
					'addCls' => 'main',
					'text'   => $barcodeHtml
				)
			)
		));

		if ($product->canBuy('reservation')){
			$table->addBodyRow(array(
				'addCls'  => 'reservationRow',
				'columns' => array(
					array(
						'addCls' => 'main',
						'text'   => 'Reservation Pricing:'
					),
					array(
						'addCls' => 'main',
						'text'   => $product->getPricingTable()
					)
				)
			));
			
			$priceField = 'Please select start and end dates';
		}else{
			$priceField = $currencies->format($product->getPrice($productsTypes[0]['id']));
		}
		$table->addBodyRow(array(
			'columns' => array(
				array(
					'addCls' => 'main',
					'text'   => 'Products Price:'
				),
				array(
					'addCls' => 'main',
					'text'   => '<span id="pricing">' . $priceField . '</span>'
				)
			)
		));

		$table->addBodyRow(array(
			'columns' => array(
				array(
					'addCls' => 'main',
					'attr'   => array(
						'colspan' => 2
					),
					'text'   => '<hr>'
				)
			)
		));

		/*
		 * @TODO: Move this into extension
		 */
		if (isset($product->productInfo['ProductsAttributes'])) {
			$Attributes = $appExtension->getExtension('attributes');
			$table->addBodyRow(array(
				'columns' => array(
					array(
						'addCls' => 'main',
						'attr' => array('colspan' => '2'),
						'text'   => $Attributes->drawAttributes(array(
							'template_dir' => DIR_FS_CATALOG . 'extensions/attributes/admin/applications/point_of_sale/template/',
							'template_file' => 'attributes_table.tpl'
						))
					)
				)
			));
			
			$table->addBodyRow(array(
				'columns' => array(
					array(
						'addCls' => 'main',
						'attr'   => array(
							'colspan' => 2
						),
						'text'   => '<hr>'
					)
				)
			));
		}
	}
	EventManager::attachActionResponse(array(
		'success'   => true,
		'tableHtml' => str_replace("\n", '', (isset($html) ? $html : $table->draw()))
	), 'json');
?>