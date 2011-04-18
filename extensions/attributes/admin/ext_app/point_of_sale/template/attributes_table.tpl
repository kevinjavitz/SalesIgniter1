<?php
	global $currencies;
	
	$product = &$appExtension->getResource('productClass');
	
	$table = htmlBase::newElement('table')
	->setCellPadding(3)
	->setCellSpacing(0)
	->setId('attributesTable');
		
	$table->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main', 'text' => '<b>Products Attributes</b>', 'attr' => array('colspan' => 2))
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
					
					$list->addItem('', $input->draw());
				}
				$html .= $list->draw() . '<br />';
				break;
			default:
				$input = htmlBase::newElement('selectbox')
				->setName('id[' . $optionId . ']')
				->addOption('', 'Please Select');
				
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
					
					$input->addOptionObj($optionEl);
				}
				$html .= $input->draw();
				break;
		}

		$table->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => $oInfo['options_name'] . ':', 'attr' => array('valign' => 'top')),
				array('addCls' => 'main', 'text' => $html, 'attr' => array('valign' => 'top'))
			)
		));
	}
	
	echo $table->draw();
?>