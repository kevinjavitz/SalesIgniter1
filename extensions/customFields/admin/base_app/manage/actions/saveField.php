<?php
	$ProductsCustomFields = Doctrine_Core::getTable('ProductsCustomFields');
	if (isset($_GET['fID'])){
		$Field = $ProductsCustomFields->findOneByFieldId((int)$_GET['fID']);
	}else{
		$Field = $ProductsCustomFields->create();
	}

	$Field->input_type = $_POST['input_type'];
	$Field->search_key = $_POST['search_key'];
	$Field->show_on_site = (isset($_POST['show_on_site']) ? '1' : '0');
	$Field->show_on_tab = (isset($_POST['show_on_tab']) ? '1' : '0');
	$Field->show_on_listing = (isset($_POST['show_on_listing']) ? '1' : '0');
	$Field->show_name_on_listing = (isset($_POST['show_name_on_listing']) ? '1' : '0');
	$Field->show_on_labels = (isset($_POST['show_on_labels']) ? '1' : '0');
	$Field->show_as_checkbox = (isset($_POST['show_as_checkbox']) ? '1' : '0');
	$Field->labels_max_chars = $_POST['labels_max_chars'];
	$Field->include_in_search = (isset($_POST['include_in_search']) ? '1' : '0');
	EventManager::notify('CustomFieldsSaveOptions', &$Field);

	$FieldDescription =& $Field->ProductsCustomFieldsDescription;
	foreach($_POST['field_name'] as $lId => $fieldName){
		$FieldDescription[$lId]->field_name = $fieldName;
		$FieldDescription[$lId]->language_id = $lId;
	}

	$Field->save();

	$OptionsToFields = Doctrine_Query::create()
	->from('ProductsCustomFieldsOptionsToFields o2f')
	->leftJoin('o2f.ProductsCustomFieldsOptions o')
	->leftJoin('o.ProductsCustomFieldsOptionsDescription od')
	->where('o2f.field_id = ?', $Field->field_id)
	->execute();
	if ($OptionsToFields){
		$OptionsToFields->delete();
	}

	if ($_POST['input_type'] == 'select'){
		$lID = Session::get('languages_id');

		$i=0;
		foreach($_POST['option_name'] as $index => $val){
			if (!empty($val)){
				$Option = new ProductsCustomFieldsOptions();
				$Option->sort_order = $_POST['option_sort'][$index];

				$Option->ProductsCustomFieldsOptionsDescription[$lID]->option_name = $val;
				$Option->ProductsCustomFieldsOptionsDescription[$lID]->language_id = $lID;

				$Option->ProductsCustomFieldsOptionsToFields[]->field_id = $Field->field_id;

				$Option->save();
				$i++;
			}
		}
	}


	$iconCss = array(
 		'float'    => 'right',
		'position' => 'relative',
		'top'      => '-4px',
		'right'    => '-4px'
	);

 	$deleteIcon = htmlBase::newElement('icon')->setType('circleClose')->setTooltip('Click to delete field')
 	->setHref(itw_app_link('appExt=customFields&action=removeField&field_id=' . $Field->field_id))
 	->css($iconCss);

 	$editIcon = htmlBase::newElement('icon')->setType('wrench')->setTooltip('Click to edit field')
 	->setHref(itw_app_link('appExt=customFields&windowAction=edit&action=getFieldWindow&fID=' . $Field->field_id))
 	->css($iconCss);

	$newFieldWrapper = new htmlElement('div');
	$newFieldWrapper->css(array(
		'float'   => 'left',
		'width'   => '150px',
		'height'  => '50px',
		'padding' => '4px',
		'margin'  => '3px'
	))->addClass('ui-widget ui-widget-content ui-corner-all draggableField')
	->html('<b><span class="fieldName" field_id="' . $Field->field_id . '">' . $Field->ProductsCustomFieldsDescription[Session::get('languages_id')]['field_name'] . '</span></b>' . $deleteIcon->draw() . $editIcon->draw() . '<br />' . TEXT_TYPE . '<span class="fieldType">' . $Field->input_type . '</span><br />' . sysLanguage::get('TEXT_SHOWN_ON_SITE') . ($Field->show_on_site == '1' ? 'Yes' : 'No'));

	EventManager::attachActionResponse($newFieldWrapper->draw(), 'html');
?>