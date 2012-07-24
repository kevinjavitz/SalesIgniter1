<?php
	$windowAction = $_GET['windowAction'];
	if ($windowAction == 'new'){
		$header = '<b>' . 'Create New Field' . '</b>';
	}else{
		$Field = Doctrine_Core::getTable('CustomerCustomFields')->findOneByFieldId((int)$_GET['fID']);
		$FieldDescription = $Field->CustomerCustomFieldsDescription;
		$FieldOptions = $Field->CustomerCustomFieldsOptionsToFields;
		$header = '<b>' . $FieldDescription[Session::get('languages_id')]['field_name'] . '</b>';
	}


	$fieldNames = htmlBase::newElement('table')->setCellPadding('3')->setCellSpacing('0');
	foreach(sysLanguage::getLanguages() as $lInfo){
		$langId = $lInfo['id'];
				
		$langImage = htmlBase::newElement('div')
		->html($lInfo['showName']());
				
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
			$Option = $FieldOptions[$i]['CustomerCustomFieldsOptions'];
			
			$nameInput->setValue($Option['CustomerCustomFieldsOptionsDescription'][$lId]['option_name']);
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
			
	$inputTypeMenu
	->addOption('text', 'Input')
	->addOption('textarea', 'Textarea')
	->addOption('select', 'Select Box')
	->addOption('radioGroup', 'Group of Radio Buttons')
	->addOption('checkboxGroup', 'Group of Checkbox Buttons')
	->addOption('selectOther', 'Select Box With Other')
	->addOption('number', 'Number')
	->addOption('country', 'Country')
	->addOption('state', 'State')
	->addOption('email', 'Email')
	;

	$requiredCheckbox = htmlBase::newElement('checkbox')
	->setId('required_' . $windowAction)
	->setName('required')
	->setLabel('<b>' . sysLanguage::get('ENTRY_REQUIRED') . '</b>')
	->setLabelPosition('after')
	->setValue('1')
	->setChecked(false);

	/*$autofocusCheckbox = htmlBase::newElement('checkbox')
		->setId('autofocus_' . $windowAction)
		->setName('autofocus')
		->setLabel('<b>' . sysLanguage::get('ENTRY_AUTOFOCUS') . '</b>')
		->setLabelPosition('after')
		->setValue('1')
		->setChecked(false);

	$novalidateCheckbox = htmlBase::newElement('checkbox')
		->setId('novalidate_' . $windowAction)
		->setName('novalidate')
		->setLabel('<b>' . sysLanguage::get('ENTRY_NOVALUDATE') . '</b>')
		->setLabelPosition('after')
		->setValue('1')
		->setChecked(false); */

	$maxCharsInput = htmlBase::newElement('input')->setSize(4)->setName('max');
	$minCharsInput = htmlBase::newElement('input')->setSize(4)->setName('min');
	$patternInput = htmlBase::newElement('input')->setName('pattern');
	$placeholderInput = htmlBase::newElement('input')->setName('placeholder');
	$customMessageInput = htmlBase::newElement('input')->setName('custom_message');


	if (!isset($Field) || ($Field !== false && $Field['input_type'] != 'select' && $Field['input_type'] != 'selectOther' && $Field['input_type'] != 'radioGroup' && $Field['input_type'] != 'checkboxGroup')){
		$optionsWrapper->css('display', 'none');
	}
	
	$finalTable = htmlBase::newElement('table')->setCellPadding('3')->setCellSpacing('0');
	
	if (isset($Field) && $Field !== false){
		$inputTypeMenu->selectOptionByValue($Field['input_type']);


		$maxCharsInput->setValue($Field['max']);
		$minCharsInput->setValue($Field['min']);

		$patternInput->setValue($Field['pattern']);
		$placeholderInput->setValue($Field['placeholder']);
		$customMessageInput->setValue($Field['custom_message']);

		
		$finalTable->attr('field_id', $Field['field_id']);

		$requiredCheckbox->setId('required_' . $Field['field_id'] . $windowAction)
		->setChecked(($Field['required'] == '1'));
		/*$novalidateCheckbox->setId('novalidate_' . $Field['field_id'] . $windowAction)
		->setChecked(($Field['novalidate'] == '1'));
		$autofocusCheckbox->setId('autofocus_' . $Field['field_id'] . $windowAction)
				->setChecked(($Field['autofocus'] == '1')); */
	}
			
	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('ENTRY_FIELD_NAME') . '</b>')
	)));

	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => $fieldNames)
	)));

			
	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('ENTRY_INPUT_TYPE') . '</b>')
	)));

	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => $inputTypeMenu)
	)));

	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => $optionsWrapper)
	)));

	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('ENTRY_MAX_CHARS') . '</b>')
	)));
	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => $maxCharsInput)
	)));

	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('ENTRY_MIN_CHARS') . '</b>')
	)));
	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => $minCharsInput)
	)));

	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('ENTRY_PATTERN') . '</b>')
	)));
	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => $patternInput)
	)));

	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('ENTRY_PLACEHOLDER') . '</b>')
	)));
	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => $placeholderInput)
	)));

	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('ENTRY_CUSTOM_MESSAGE') . '</b>')
	)));
	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => $customMessageInput)
	)));


$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => $requiredCheckbox)
	)));
	/*$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => $novalidateCheckbox)
	)));
	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => $autofocusCheckbox)
	)));*/
	EventManager::notify('CustomerCustomFieldsNewOptions', $Field, &$finalTable, $windowAction);

	EventManager::attachActionResponse($finalTable->draw(), 'html');
?>