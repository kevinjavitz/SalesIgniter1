<?php
	$OrdersCustomFields = Doctrine_Core::getTable('OrdersCustomFields');
	if (isset($_GET['fID'])){
		$Field = $OrdersCustomFields->findOneByFieldId((int)$_GET['fID']);
	}else{
		$Field = $OrdersCustomFields->create();
	}
	
	$Field->input_type = $_POST['input_type'];
	$Field->input_required = (isset($_POST['input_required']));
	$Field->sort_order = $_POST['sort_order'];
	
	$FieldDescription =& $Field->OrdersCustomFieldsDescription;
	foreach($_POST['field_name'] as $lId => $fieldName){
		$FieldDescription[$lId]->field_name = $fieldName;
		$FieldDescription[$lId]->language_id = $lId;
	}
	
	$Field->save();

	$OptionsToFields = Doctrine_Query::create()
	->from('OrdersCustomFieldsOptionsToFields o2f')
	->leftJoin('o2f.OrdersCustomFieldsOptions o')
	->leftJoin('o.OrdersCustomFieldsOptionsDescription od')
	->where('o2f.field_id = ?', $Field->field_id)
	->execute();
	if ($OptionsToFields){
		$OptionsToFields->delete();
	}
	
	if ($_POST['input_type'] == 'select' || $_POST['input_type'] == 'select_other'){
		$lID = Session::get('languages_id');
		
		$i=0;
		foreach($_POST['option_name'] as $index => $val){
			if (!empty($val)){
				$Option = new OrdersCustomFieldsOptions();
				$Option->sort_order = $_POST['option_sort'][$index];
				
				$Option->OrdersCustomFieldsOptionsDescription[$lID]->option_name = $val;
				$Option->OrdersCustomFieldsOptionsDescription[$lID]->language_id = $lID;
				
				$Option->OrdersCustomFieldsOptionsToFields[]->field_id = $Field->field_id;
				
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
 	->setHref(itw_app_link('appExt=ordersCustomFields&action=removeField&field_id=' . $Field->field_id))
 	->css($iconCss);

 	$editIcon = htmlBase::newElement('icon')->setType('wrench')->setTooltip('Click to edit field')
 	->setHref(itw_app_link('appExt=ordersCustomFields&windowAction=edit&action=getFieldWindow&fID=' . $Field->field_id))
 	->css($iconCss);

	$newFieldWrapper = new htmlElement('div');
	$newFieldWrapper->css(array(
		'float'   => 'left',
		'width'   => '150px',
		'height'  => '59px',
		'padding' => '4px',
		'margin'  => '3px'
	))->addClass('ui-widget ui-widget-content ui-corner-all draggableField')
	->html('<b><span class="fieldName" field_id="' . $Field->field_id . '">' . $Field->OrdersCustomFieldsDescription[Session::get('languages_id')]['field_name'] . '</span></b>' . $deleteIcon->draw() . $editIcon->draw() . '<br />' . sysLanguage::get('TEXT_TYPE') . '<span class="fieldType">' . $Field->input_type . '</span><br />Required: ' . ($Field->input_required == '1' ? 'Yes': 'No').'<br />Sort Order: '. $Field->sort_order);
	
	EventManager::attachActionResponse($newFieldWrapper->draw(), 'html');
?>