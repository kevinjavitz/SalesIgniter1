<?php
	$windowAction = $_GET['windowAction'];
	if ($windowAction == 'new'){
		$header = '<b>' . 'Create New Field' . '</b>';
	}else{
		$Field = Doctrine_Core::getTable('OrdersCustomFields')->findOneByFieldId((int)$_GET['fID']);
		$FieldDescription = $Field->OrdersCustomFieldsDescription;
		$FieldOptions = $Field->OrdersCustomFieldsOptionsToFields;
		$header = '<b>' . $FieldDescription[Session::get('languages_id')]['field_name'] . '</b>';
	}

	$languages = tep_get_languages();
	$fieldNames = htmlBase::newElement('table')->setCellPadding('3')->setCellSpacing('0');
	for ($i=0, $n=sizeof($languages); $i<$n; $i++){
		$langId = $languages[$i]['id'];
				
		$langImage = htmlBase::newElement('image')
		->setSource(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'])
		->setTitle($languages[$i]['name']);
				
		$fieldNameInput = htmlBase::newElement('input')->setName('field_name[' . $langId . ']');
		
		if (isset($Field) && $Field !== false){
			$fieldNameInput->setValue($FieldDescription[$langId]['field_name']);
		}
		
		$fieldNames->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => ''),
				array('addCls' => 'main', 'text' => $fieldNameInput)
			)
		));
	}

	$sortOrder = htmlBase::newElement('input')->setName('sort_order');
	$sortOrder->setValue($Field->sort_order);
	$requiredCheckbox = htmlBase::newElement('checkbox')
	->setName('input_required');

	$selectInputOptions = htmlBase::newElement('table')->setCellPadding('3')->setCellSpacing('0')
	->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main', 'text' => '<b><u>' . sysLanguage::get('TABLE_HEADING_OPTION_TEXT') . '</u></b>'),
			array('addCls' => 'main', 'text' => '<b><u>' . sysLanguage::get('TABLE_HEADING_SORT_ORDER') . '</u></b>')
		)
	));

	$lId = Session::get('languages_id');
	for($i=0; $i<15; $i++){
		$nameInput = htmlBase::newElement('input')->setName('option_name[' . $i . ']');
		$sortInput = htmlBase::newElement('input')->setName('option_sort[' . $i . ']')->setSize('4');

		if (isset($FieldOptions[$i])){
			$Option = $FieldOptions[$i]['OrdersCustomFieldsOptions'];
			
			$nameInput->setValue($Option['OrdersCustomFieldsOptionsDescription'][$lId]['option_name']);
			$sortInput->setValue($Option['sort_order']);
		}
		
		$selectInputOptions->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => $nameInput),
				array('addCls' => 'main', 'text' => $sortInput)
			)
		));
	}
			
	$optionsWrapper = new htmlElement('div');
	$optionsWrapper->attr('id', 'selectOptions');
	$optionsWrapper->append($selectInputOptions);

	$inputTypeMenu = htmlBase::newElement('selectbox')
	->setName('input_type')
	->change('showOptionEntry(this)');
			
	$inputTypeMenu->addOption('text', 'Text')
	->addOption('textarea', 'Textarea')
	->addOption('select', 'Select Box Without Other')
	->addOption('select_other', 'Select Box With Other');
			
	if (!isset($Field) || ($Field !== false && ($Field['input_type'] != 'select' && $Field['input_type'] != 'select_other'))){
		$optionsWrapper->css('display', 'none');
	}
	
	$finalTable = htmlBase::newElement('table')->setCellPadding('3')->setCellSpacing('0');
	
	if (isset($Field) && $Field !== false){
		$inputTypeMenu->selectOptionByValue($Field['input_type']);
		$requiredCheckbox->setChecked(($Field['input_required'] == 1));

		$finalTable->attr('field_id', $Field['field_id']);
	}
			
	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('ENTRY_FIELD_NAME') . '</b>')
	)));

	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => $fieldNames)
	)));
			
	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('ENTRY_FIELD_REQUIRED') . '</b>')
	)));

	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => $requiredCheckbox)
	)));
			
	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('ENTRY_INPUT_TYPE') . '</b>')
	)));

	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => $inputTypeMenu)
	)));

	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('ENTRY_SORT_ORDER') . '</b>')
	)));

	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => $sortOrder)
	)));


	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => $optionsWrapper)
	)));

	EventManager::attachActionResponse($finalTable->draw(), 'html');
?>