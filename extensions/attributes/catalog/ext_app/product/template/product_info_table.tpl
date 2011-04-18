<?php
	global $currencies;
	
	$product = &$appExtension->getResource('productClass_' . $_GET['products_id']);
	
	$table = htmlBase::newElement('table')
	->setCellPadding(3)
	->setCellSpacing(0)
	->addClass('attributesTable');
		
	$table->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main', 'text' => sysLanguage::get('TEXT_PRODUCT_OPTIONS'), 'attr' => array('colspan' => 2))
		)
	));
		
	foreach($attributes as $optionId => $oInfo){
		$optionsValues = $oInfo['ProductsOptionsValues'];
		$html = '';
		switch($oInfo['option_type']){
			case 'radio':
				$list = htmlBase::newElement('list')
				->css(array(
					'list-style' => 'none',
					'padding' => 0,
					'margin' => 0
				));
				for($i=0, $n=sizeof($optionsValues); $i<$n; $i++){
					$valueId = $optionsValues[$i]['options_values_id'];
					
					$input = htmlBase::newElement('radio')
					->setId('option_' . $optionId . '_value_' . $valueId)
					->setName('id[' . $optionId . ']')
					->setValue($optionsValues[$i]['options_values_id'])
					->setLabel($optionsValues[$i]['options_values_name']);
					
					$multiList = '';
					if ($oInfo['use_image'] == '1'){
						$list->addClass('useImage');
						if ($oInfo['use_multi_image'] == '1'){
							$list->addClass('useMultiImage');
							
							$multiList = htmlBase::newElement('list')
							->setId('images_' . $optionId . '_' . $valueId)
							->css('display', 'none');
							foreach($optionsValues[$i]['ProductsAttributesViews'] as $idx => $viewInfo){
								if ($idx == 0){
									$input->attr('title', $viewInfo['view_name'])
									->attr('imageSrc', DIR_WS_IMAGES . $viewInfo['view_image']);
								}
								
								$liObj = htmlBase::newElement('li')
								->attr('imgSrc', 'product_thumb.php?w=280&img=' . DIR_WS_IMAGES . $viewInfo['view_image'])
								->attr('bigImgSrc', DIR_WS_IMAGES . $viewInfo['view_image'])
								->html($viewInfo['view_name']);
								
								$multiList->addItemObj($liObj);
							}
							$multiList = $multiList->draw();
						}else{
							$list->addClass('useSingleImage');
							
							$input->attr('title', $optionsValues[$i]['options_values_name'])
							->attr('imageSrc', DIR_WS_IMAGES . $optionsValues[$i]['options_values_image']);
						}
					}
					
					$list->addItem('', $input->draw() . $multiList);
				}
				$html .= $list->draw() . '<br />';
				break;
			default:
				$input = htmlBase::newElement('selectbox')
				->setName('id[' . $optionId . ']')
				->addOption('', 'Please Select');

				if ($oInfo['use_image'] == '1'){
					$input->addClass('useImage');
					if ($oInfo['use_multi_image'] == '1'){
						$input->addClass('useMultiImage');
					}else{
						$input->addClass('useSingleImage');
					}
				}
				
				$multiList = '';
				for($i=0, $n=sizeof($optionsValues); $i<$n; $i++){
					$valueId = $optionsValues[$i]['options_values_id'];
					$price = '';
					if ($optionsValues[$i]['options_values_price'] != '0') {
						$price = ' (' . 
							$optionsValues[$i]['price_prefix'] . 
							$currencies->display_price($optionsValues[$i]['options_values_price'], $product->getTaxRate()) . 
						') ';
					}
					$optionEl = htmlBase::newElement('option')
					->attr('value', $optionsValues[$i]['options_values_id'])
					->html($optionsValues[$i]['options_values_name'] . $price);
					
					if ($oInfo['use_image'] == '1'){
						if ($oInfo['use_multi_image'] == '1'){
							$imageList = htmlBase::newElement('list')
							->setId('images_' . $optionId . '_' . $valueId)
							->css('display', 'none');
							foreach($optionsValues[$i]['ProductsAttributesViews'] as $viewInfo){
								$liObj = htmlBase::newElement('li')
								->attr('imgSrc', DIR_WS_IMAGES . $viewInfo['view_image'])
								->html($viewInfo['view_name']);
								
								$imageList->addItemObj($liObj);
							}
							$multiList .= $imageList->draw();
						}else{
							$optionEl->attr('title', $optionsValues[$i]['options_values_name'])
							->attr('imageSrc', DIR_WS_IMAGES . $optionsValues[$i]['options_values_image']);
						}
					}
					$input->addOptionObj($optionEl);
				}

				if (isset($shoppingCartBase->contents[$_GET['products_id']]['new']['attributes'][$optionId])){
					$input->selectOptionByValue($optionId);
				}
				$html .= $input->draw() . $multiList;
				break;
		}

		$table->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => $oInfo['options_name'] . ':', 'attr' => array('valign' => 'top')),
				array('addCls' => 'main', 'text' => $html, 'attr' => array('valign' => 'top'))
			)
		));
	}
	
	//echo '<script src="extensions/attributes/catalog/ext_app/product_info/javascript/product_info.js"></script>';
	echo $table->draw();
?>