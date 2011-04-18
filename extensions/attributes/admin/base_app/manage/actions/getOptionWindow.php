<?php
	$windowAction = $_GET['windowAction'];
	if ($windowAction == 'edit'){
		$Qvalue = Doctrine_Query::create()
		->select('o.products_options_id, o.option_type, o.use_image, o.use_multi_image, o.update_product_image, od.products_options_name')
		->from('ProductsOptions o')
		->leftJoin('o.ProductsOptionsDescription od')
		->where('o.products_options_id = ?', $_GET['option_id'])
		->orderBy('od.products_options_name')
		->fetchOne()->toArray();
	}

	$languages = tep_get_languages();
	$optionNames = htmlBase::newElement('table')->setCellPadding('3')->setCellSpacing('0');
	for ($i=0, $n=sizeof($languages); $i<$n; $i++){
		$langID = $languages[$i]['id'];
				
		$langImage = htmlBase::newElement('image')
		->setSource(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'])
		->setTitle($languages[$i]['name']);
				
		$optionNameInput = htmlBase::newElement('input')->setName('option_name[' . $langID . ']');
		if (isset($Qvalue)){
			$optionNameInput->setValue($Qvalue['ProductsOptionsDescription'][$langID]['products_options_name']);
		}
		$optionNames->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => ''),
				array('addCls' => 'main', 'text' => $optionNameInput)
			)
		));
	}

	$optionTypeRadios = htmlBase::newElement('radio')
	->addGroup(array(
		'name' => 'option_type',
		'checked' => $Qvalue['option_type'],
		'separator' => '<br />',
		'data' => array(
			array(
				'label' => 'Select Box',
				'value' => 'selectbox',
				'labelPosition' => 'after'
			),
			array(
				'label' => 'Radio',
				'value' => 'radio',
				'labelPosition' => 'after'
			),
			array(
				'label' => 'Checkbox',
				'value' => 'checkbox',
				'labelPosition' => 'after',
				'disabled' => true
			),
			array(
				'label' => 'Text',
				'value' => 'input',
				'labelPosition' => 'after',
				'disabled' => true
			),
			array(
				'label' => 'Textarea',
				'value' => 'textarea',
				'labelPosition' => 'after',
				'disabled' => true
			)
		)
	));

	$optionSettings = array();
	$optionSettings[0] = htmlBase::newElement('checkbox')
	->setId('use_image')
	->setName('use_image')
	->setValue('1')
	->setLabel('Use images for values')
	->setLabelPosition('after');
	
	$optionSettings[1] = htmlBase::newElement('checkbox')
	->setId('use_multi_image')
	->setName('use_multi_image')
	->setValue('1')
	->setLabel('Each value has multiple views')
	->setLabelPosition('after');
	
	$optionSettings[2] = htmlBase::newElement('checkbox')
	->setId('update_product_image')
	->setName('update_product_image')
	->setValue('1')
	->setLabel('Update product image')
	->setLabelPosition('after');
	
	$optionSetting = '';
	foreach($optionSettings as $settingsObj){
		if (isset($Qvalue)){
			if ($Qvalue[$settingsObj->attr('name')] == $settingsObj->val()){
				$settingsObj->setChecked(true);
			}
		}
		$optionSetting .= $settingsObj->draw() . '<br />';
	}

	$finalTable = htmlBase::newElement('table')->setCellPadding('3')->setCellSpacing('0');
	
	if (isset($_GET['option_id'])){
		$finalTable->attr('option_id', (int)$_GET['option_id']);
	}
			
	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('ENTRY_OPTION_NAME') . '</b>')
	)));

	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => $optionNames)
	)));

	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('ENTRY_OPTION_TYPE') . '</b>')
	)));

	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => $optionTypeRadios->draw())
	)));

	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('ENTRY_OPTION_SETTINGS') . '</b>')
	)));

	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => $optionSetting)
	)));

	EventManager::attachActionResponse($finalTable->draw(), 'html');
?>