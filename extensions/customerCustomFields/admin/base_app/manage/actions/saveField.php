<?php
	$CustomerCustomFields = Doctrine_Core::getTable('CustomerCustomFields');
	if (isset($_GET['fID'])){
		$Field = $CustomerCustomFields->findOneByFieldId((int)$_GET['fID']);
	}else{
		$Field = $CustomerCustomFields->create();
	}

	$Field->input_type = $_POST['input_type'];
	$Field->max = $_POST['max'];
	$Field->pattern = $_POST['pattern'];
	$Field->placeholder = $_POST['placeholder'];
	$Field->custom_message = $_POST['custom_message'];
	$Field->min = $_POST['min'];

	$Field->required = (isset($_POST['required']) ? '1' : '0');
	$Field->novalidate = (isset($_POST['novalidate']) ? '1' : '0');
	$Field->autofocus = (isset($_POST['autofocus']) ? '1' : '0');
	EventManager::notify('CustomerCustomFieldsSaveOptions', &$Field);

	$FieldDescription =& $Field->CustomerCustomFieldsDescription;
	foreach($_POST['field_name'] as $lId => $fieldName){
		$FieldDescription[$lId]->field_name = $fieldName;
		$FieldDescription[$lId]->language_id = $lId;
	}

	$Field->save();

	$OptionsToFields = Doctrine_Query::create()
	->from('CustomerCustomFieldsOptionsToFields o2f')
	->leftJoin('o2f.CustomerCustomFieldsOptions o')
	->leftJoin('o.CustomerCustomFieldsOptionsDescription od')
	->where('o2f.field_id = ?', $Field->field_id)
	->execute();
	if ($OptionsToFields){
		$OptionsToFields->delete();
	}

	if ($_POST['input_type'] == 'select' || $_POST['input_type'] == 'selectOther' || $_POST['input_type'] == 'radioGroup' || $_POST['input_type'] == 'checkboxGroup'){
		$lID = Session::get('languages_id');

		$i=0;
		foreach($_POST['option_name'] as $index => $val){
			if (!empty($val)){
				$Option = new CustomerCustomFieldsOptions();
				$Option->sort_order = $_POST['option_sort'][$index];

				$Option->CustomerCustomFieldsOptionsDescription[$lID]->option_name = $val;
				$Option->CustomerCustomFieldsOptionsDescription[$lID]->language_id = $lID;

				$Option->CustomerCustomFieldsOptionsToFields[]->field_id = $Field->field_id;

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
 	->setHref(itw_app_link('appExt=customerCustomFields&action=removeField&field_id=' . $Field->field_id))
 	->css($iconCss);

 	$editIcon = htmlBase::newElement('icon')->setType('wrench')->setTooltip('Click to edit field')
 	->setHref(itw_app_link('appExt=customerCustomFields&windowAction=edit&action=getFieldWindow&fID=' . $Field->field_id))
 	->css($iconCss);

	$newFieldWrapper = new htmlElement('div');
	$newFieldWrapper->css(array(
		'float'   => 'left',
		'width'   => '150px',
		'height'  => '50px',
		'padding' => '4px',
		'margin'  => '3px'
	))->addClass('ui-widget ui-widget-content ui-corner-all draggableField')
	->html('<b><span class="fieldName" field_id="' . $Field->field_id . '">' . $Field->CustomerCustomFieldsDescription[Session::get('languages_id')]['field_name'] . '</span></b>' . $deleteIcon->draw() . $editIcon->draw() . '<br />' . sysLanguage::get('TEXT_TYPE') . '<span class="fieldType">' . $Field->input_type . '</span><br />');

	EventManager::attachActionResponse($newFieldWrapper->draw(), 'html');
?>