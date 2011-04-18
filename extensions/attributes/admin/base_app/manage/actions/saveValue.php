<?php
	$ProductsOptionsValues = Doctrine::getTable('ProductsOptionsValues');
	if (isset($_GET['vID'])){
		$Value = $ProductsOptionsValues->findOneByProductsOptionsValuesId($_GET['vID']);
	}else{
		$Value = $ProductsOptionsValues->create();
	}
	
	$Descriptions =& $Value->ProductsOptionsValuesDescription;
	foreach($_POST['value_name'] as $langId => $valueName){
		$Descriptions[$langId]->products_options_values_name = $valueName;
		$Descriptions[$langId]->language_id = $langId;
		
		if ($langId == Session::get('languages_id')){
			$outputName = $valueName;
		}
	}

	$Value->save();
	
	if (isset($_GET['vID'])){
		EventManager::attachActionResponse(array(
			'success'    => true,
			'value_id'   => $Value->products_options_values_id,
			'value_name' => $outputName
		), 'json');
	}else{
		$vID = $Value->products_options_values_id;

		$iconCss = array(
	 		'float'    => 'right',
			'position' => 'relative',
			'top'      => '-4px',
			'right'    => '-4px'
		);
	
	 	$deleteIcon = htmlBase::newElement('icon')->setType('circleClose')->setTooltip('Click to delete value')
	 	->setHref(itw_app_link('action=removeValue&value_id=' . $vID))
	 	->css($iconCss);

	 	$editIcon = htmlBase::newElement('icon')->setType('wrench')->setTooltip('Click to edit value')
	 	->setHref(itw_app_link('windowAction=edit&action=getValueWindow&value_id=' . $vID))
	 	->css($iconCss);

		$newValueWrapper = new htmlElement('div');
		$newValueWrapper->css(array(
			'float'   => 'left',
			'width'   => '150px',
			'height'  => '50px',
			'padding' => '4px',
			'margin'  => '3px'
		))->addClass('ui-widget ui-widget-content ui-corner-all draggableValue')
		->html('<b><span class="valueName" value_id="' . $vID . '">' . $outputName . '</span></b>' . $deleteIcon->draw() . $editIcon->draw());
	
		EventManager::attachActionResponse($newValueWrapper->draw(), 'html');
	}
?>