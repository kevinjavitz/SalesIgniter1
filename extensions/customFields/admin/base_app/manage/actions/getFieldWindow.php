<?php
	$windowAction = $_GET['windowAction'];
	if ($windowAction == 'new'){
		$header = '<b>' . 'Create New Field' . '</b>';
	}else{
		$Field = Doctrine_Core::getTable('ProductsCustomFields')->findOneByFieldId((int)$_GET['fID']);
		$FieldDescription = $Field->ProductsCustomFieldsDescription;
		$FieldOptions = $Field->ProductsCustomFieldsOptionsToFields;
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
			$Option = $FieldOptions[$i]['ProductsCustomFieldsOptions'];
			
			$nameInput->setValue($Option['ProductsCustomFieldsOptionsDescription'][$lId]['option_name']);
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
	->addOption('select', 'Select Box')
	->addOption('upload', 'Image Upload')
	->addOption('search', 'Click To Search');
			
	$showSiteCheckbox = htmlBase::newElement('checkbox')
	->setId('showOnSite_' . $windowAction)
	->setName('show_on_site')
	->setLabel('<b>' . sysLanguage::get('ENTRY_SHOW_ON_SITE') . '</b>')
	->setLabelPosition('after')
	->setValue('1')
	->setChecked(false);
	
	$showListingCheckbox = htmlBase::newElement('checkbox')
	->setId('showOnSite_' . $windowAction)
	->setName('show_on_listing')
	->setLabel('<b>' . sysLanguage::get('ENTRY_SHOW_ON_PRODUCT_LISTING') . '</b>')
	->setLabelPosition('after')
	->setValue('1')
	->setChecked(false);

	$showNameListingCheckbox = htmlBase::newElement('checkbox')
	->setId('showOnSite_' . $windowAction)
	->setName('show_name_on_listing')
	->setLabel('<b>' . sysLanguage::get('ENTRY_SHOW_NAME_ON_PRODUCT_LISTING') . '</b>')
	->setLabelPosition('after')
	->setValue('1')
	->setChecked(false);
	
	$showLabelCheckbox = htmlBase::newElement('checkbox')
	->setId('showOnLabels_' . $windowAction)
	->setName('show_on_labels')
	->setLabel('<b>' . sysLanguage::get('ENTRY_SHOW_ON_LABELS') . '</b>')
	->setLabelPosition('after')
	->setValue('1')
	->setChecked(false);

	$includeSearchCheckbox = htmlBase::newElement('checkbox')
	->setId('showOnSite_' . $windowAction)
	->setName('include_in_search')
	->setLabel('<b>' . sysLanguage::get('ENTRY_INCLUDE_IN_SEARCH') . '</b>')
	->setLabelPosition('after')
	->setValue('1')
	->setChecked(false);
	
	$searchKeyInput = htmlBase::newElement('input')->setName('search_key');
	$maxCharsInput = htmlBase::newElement('input')->setSize(4)->setName('labels_max_chars');

	if (!isset($Field) || ($Field !== false && $Field['input_type'] != 'select')){
		$optionsWrapper->css('display', 'none');
	}
	
	$finalTable = htmlBase::newElement('table')->setCellPadding('3')->setCellSpacing('0');
	
	if (isset($Field) && $Field !== false){
		$inputTypeMenu->selectOptionByValue($Field['input_type']);

		$showSiteCheckbox->setId('showOnSite_' . $Field['field_id'] . $windowAction)
		->setChecked(($Field['show_on_site'] == '1'));

		$showListingCheckbox->setId('showOnListing_' . $Field['field_id'] . $windowAction)
		->setChecked(($Field['show_on_listing'] == '1'));

		$showNameListingCheckbox->setId('showNameOnListing_' . $Field['field_id'] . $windowAction)
		->setChecked(($Field['show_name_on_listing'] == '1'));

		$showLabelCheckbox->setId('showOnLabels_' . $Field['field_id'] . $windowAction)
		->setChecked(($Field['show_on_labels'] == '1'));
		
		$searchKeyInput->setValue($Field['search_key']);
		$maxCharsInput->setValue($Field['labels_max_chars']);
		
		$finalTable->attr('field_id', $Field['field_id']);

		$includeSearchCheckbox->setId('showOnSite_' . $Field['field_id'] . $windowAction)
		->setChecked(($Field['include_in_search'] == '1'));
	}
			
	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('ENTRY_FIELD_NAME') . '</b>')
	)));

	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => $fieldNames)
	)));
			
	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('ENTRY_SEARCH_KEY') . '</b>')
	)));

	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => $searchKeyInput)
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
		array('addCls' => 'main', 'text' => $showSiteCheckbox)
	)));
	
	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => $showListingCheckbox)
	)));

	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => $showNameListingCheckbox)
	)));
	
	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => $showLabelCheckbox)
	)));
	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('ENTRY_MAX_LABEL_CHARS') . '</b>')
	)));
	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => $maxCharsInput)
	)));
	$finalTable->addBodyRow(array('columns' => array(
		array('addCls' => 'main', 'text' => $includeSearchCheckbox)
	)));
	EventManager::notify('CustomFieldsNewOptions', $Field, &$finalTable, $windowAction);

	EventManager::attachActionResponse($finalTable->draw(), 'html');
?>