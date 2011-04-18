<?php
	$fieldListing = htmlBase::newElement('div')->attr('id', 'fieldListing')->css(array(
		'display'  => 'block',
		'width'    => '100%',
		'height'   => '250px',
		'overflow' => 'auto'
	));

	$Qfields = Doctrine_Query::create()
	->select('f.field_id, f.input_type, f.input_required, fd.field_name, f.sort_order')
	->from('OrdersCustomFields f')
	->leftJoin('f.OrdersCustomFieldsDescription fd')
	->where('fd.language_id = ?', Session::get('languages_id'))
	->execute();
	if ($Qfields->count() > 0){
		$iconCss = array(
			'float'    => 'right',
			'position' => 'relative',
			'top'      => '-4px',
			'right'    => '-4px'
		);

		foreach($Qfields->toArray(true) as $fInfo){
			$fieldId = $fInfo['field_id'];
			$fieldName = $fInfo['OrdersCustomFieldsDescription'][Session::get('languages_id')]['field_name'];
			$inputType = $fInfo['input_type'];
			$inputRequired = $fInfo['input_required'];
			$sortOrder = $fInfo['sort_order'];
			
			$deleteIcon = htmlBase::newElement('icon')->setType('circleClose')->setTooltip('Click to delete field')
			->setHref(itw_app_link('appExt=ordersCustomFields&action=removeField&field_id=' . $fieldId))
			->css($iconCss);

			$editIcon = htmlBase::newElement('icon')->setType('wrench')->setTooltip('Click to edit field')
			->setHref(itw_app_link('appExt=ordersCustomFields&windowAction=edit&action=getFieldWindow&fID=' . $fieldId))
			->css($iconCss);

			$newFieldWrapper = htmlBase::newElement('div')->css(array(
				'float'   => 'left',
				'width'   => '150px',
				'height'  => '59px',
				'padding' => '4px',
				'margin'  => '3px'
			))->addClass('ui-widget ui-widget-content ui-corner-all draggableField')
			->html('<b><span class="fieldName" field_id="' . $fieldId . '">' . $fieldName . '</span></b>' . $deleteIcon->draw() . $editIcon->draw() . '<br />' . sysLanguage::get('TEXT_TYPE') . '<span class="fieldType">' . $inputType . '</span><br />Required: ' . ($inputRequired == '1' ? 'Yes': 'No'). '<br />Sort Order: '.$sortOrder );

			$fieldListing->append($newFieldWrapper);
		}
	}
?>
 
 <div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
 <br />
 <div><?php echo htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_NEW_FIELD'))->setId('newField')->draw();?></div>
 <?php echo $fieldListing->draw();?>