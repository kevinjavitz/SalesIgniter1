<?php
class InfoBoxDrawAttributes extends InfoBoxAbstract {
	
	public function __construct(){
		global $App;
		$this->init('drawAttributes', 'attributes');
	}

	public function drawAttributes($settings = null){
		global $appExtension, $currencies, $ShoppingCart;
		$product = $settings['productClass'];

		if (!isset($product->productInfo['ProductsAttributes']) || sizeof($product->productInfo['ProductsAttributes']) <= 0) return;

		if (!isset($settings['purchase_type'])) return;

		$ProductsAttributes = attributesUtil::getAttributes($product->productInfo['products_id'], null, null, $settings['purchase_type']);
		$Attributes = attributesUtil::organizeAttributeArray($ProductsAttributes);

		if (is_null($settings) === false && isset($settings['return_array'])){
			return $Attributes;
		}
		if (sizeof($Attributes) <= 0) return '';

		$table = htmlBase::newElement('table')
			->setCellPadding(3)
			->setCellSpacing(0)
			->addClass('attributesTable');

		$table->addBodyRow(array(
				'columns' => array(
					array('addCls' => 'main', 'text' => sysLanguage::get('TEXT_PRODUCT_OPTIONS'), 'attr' => array('colspan' => 2))
				)
			));

		foreach($Attributes as $optionId => $oInfo){
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
							->setName('id[' . $settings['purchase_type'] . '][' . $optionId . ']')
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
											->attr('imageSrc', sysConfig::get('DIR_WS_IMAGES') . $viewInfo['view_image']);
									}

									$liObj = htmlBase::newElement('li')
										->attr('imgSrc', 'product_thumb.php?w=280&img=' . sysConfig::get('DIR_WS_IMAGES') . $viewInfo['view_image'])
										->attr('bigImgSrc', sysConfig::get('DIR_WS_IMAGES') . $viewInfo['view_image'])
										->html($viewInfo['view_name']);

									$multiList->addItemObj($liObj);
								}
								$multiList = $multiList->draw();
							}else{
								$list->addClass('useSingleImage');

								$input->attr('title', $optionsValues[$i]['options_values_name'])
									->attr('imageSrc', sysConfig::get('DIR_WS_IMAGES') . $optionsValues[$i]['options_values_image']);
							}
						}

						$list->addItem('', $input->draw() . $multiList);
					}
					$html .= $list->draw() . '<br />';
					break;
				default:
					$input = htmlBase::newElement('selectbox')
						->setName('id[' . $settings['purchase_type'] . '][' . $optionId . ']')
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
										->attr('imgSrc', sysConfig::get('DIR_WS_IMAGES') . $viewInfo['view_image'])
										->html($viewInfo['view_name']);

									$imageList->addItemObj($liObj);
								}
								$multiList .= $imageList->draw();
							}else{
								$optionEl->attr('title', $optionsValues[$i]['options_values_name'])
									->attr('imageSrc', sysConfig::get('DIR_WS_IMAGES') . $optionsValues[$i]['options_values_image']);
							}
						}
						$input->addOptionObj($optionEl);
					}

					if ($ShoppingCart->inCart($_GET['products_id'], $settings['purchase_type'])){
						$cartProduct = $ShoppingCart->getProduct($_GET['products_id'], $settings['purchase_type']);

						if ($cartProduct->hasInfo('attributes')){
							$Attributes = $cartProduct->getInfo('attributes');
							if (isset($Attributes[$optionId])){
								$input->selectOptionByValue($optionId);
							}
						}
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

		return $table->draw();
	}
	
	public function show(){
		global $appExtension;
		//$AttributesExt = $appExtension->getExtension('attributes');
		$product = new product($_GET['products_id']);

		$Attributes = $this->drawAttributes(array('productClass' => $product,'purchase_type' => 'reservation'/*, 'return_array' => '1'*/));

		/*$output = '';

		//var_dump($AttributesOutput);
		if(is_array($Attributes)){
			$output = '<div class="productListingColBoxContent_attributes ui-corner-bottom-big">';
			$table = htmlBase::newElement('table')
				->setCellPadding(3)
				->setCellSpacing(0);

			foreach($Attributes as $optionId => $attribute)
			{
				$attributeName = '';
				$attributeValuesText = '';
				$attributeName = $attribute['options_name'];
				if(is_array($attribute['ProductsOptionsValues'])){
					$attributeValues = array();
					$ctr = 0;
					$index = 0;
					foreach($attribute['ProductsOptionsValues'] as $attributeKey => $attributeValue)
					{
						$attributeValues[] = htmlBase::newElement('a')
							->attr('href', itw_app_link('options[' . $attributeKey . ']=' . $attributeValue['options_values_id'] . '&values[' . $attributeKey . ']=' . $optionId, 'products', 'search_result'))
							->html($attributeValue['options_values_name'])->draw();
						if($ctr >= 7){
							$attributeValuesText .= implode(', ', $attributeValues) . '<br>';
							$attributeValues = array();
							$ctr = 0;
						}
						$ctr++;
					}
					$attributeValuesText .= implode(', ', $attributeValues) . '<br>';
				}
				$table->addBodyRow(array(
						'columns' => array(
							array('addCls' => 'productListingColBoxContent_attributesName', 'text' => '<b>' . $attributeName . ': </b>'),
							array('addCls' => 'productListingColBoxContent_attributesValue', 'text' => $attributeValuesText, 'align' => 'left')
						)
					));
			}

			$output .= $table->draw();
			$output .= '</div>';
		}
        */
		$this->setBoxContent($Attributes);
		
		return $this->draw();
	}
}
?>