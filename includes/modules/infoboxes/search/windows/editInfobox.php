<?php
$editTable = htmlBase::newElement('table')
	->setCellPadding(2)
	->setCellSpacing(0);

$editTable->addBodyRow(array(
	'columns' => array(
		array('colspan' => 2, 'text' => '<b>Add Guided Search Option</b>')
	)
));

$PleaseSelectText = htmlBase::newElement('span')
	->addClass('noSelection')
	->html('Please Select An Option Type');

$OptionTypeBox = htmlBase::newElement('selectbox')
	->setName('option_type')
	->addOption('', 'Please Select')
	->addOption('purchase_type', 'Purchase Type')
	->addOption('price', 'Product Price')
	->addOption('custom_field', 'Custom Field')
	->addOption('attribute', 'Attribute');

$editTable->addBodyRow(array(
	'columns' => array(
		array('text' => 'Option Type: '),
		array('text' => $OptionTypeBox)
	)
));

$Qattributes = Doctrine_Query::create()
	->from('ProductsOptions o')
	->leftJoin('o.ProductsOptionsDescription od')
	->where('od.language_id = ?', Session::get('languages_id'))
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
$AttributeOptionBox = htmlBase::newElement('selectbox')
	->addClass('optionBox')
	->setName('option_id[attribute]')
	->hide();
if ($Qattributes){
	$AttributeOptionBox->addOption('', 'Please Select');
	foreach($Qattributes as $aInfo){
		$AttributeOptionBox->addOption($aInfo['products_options_id'], $aInfo['ProductsOptionsDescription'][0]['products_options_name']);
	}
}

$QcustomFields = Doctrine_Query::create()
	->from('ProductsCustomFields f')
	->leftJoin('f.ProductsCustomFieldsDescription fd')
	->where('fd.language_id = ?', Session::get('languages_id'))
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

$CustomFieldOptionBox = htmlBase::newElement('selectbox')
	->addClass('optionBox')
	->setName('option_id[custom_field]')
	->hide();
if ($QcustomFields){
	$CustomFieldOptionBox->addOption('', 'Please Select');
	foreach($QcustomFields as $fInfo){
		$CustomFieldOptionBox->addOption($fInfo['field_id'], $fInfo['ProductsCustomFieldsDescription'][0]['field_name']);
	}
}
$editTable->addBodyRow(array(
	'columns' => array(
		array('text' => 'Option: '),
		array('text' => $PleaseSelectText->draw() . $AttributeOptionBox->draw() . $CustomFieldOptionBox->draw())
	)
));

$addOptionButton = htmlBase::newElement('button')
	->addClass('addOptionButton')
	->usePreset('install')
	->setText('Add Search Option');

$editTable->addBodyRow(array(
	'columns' => array(
		array('colspan' => 2, 'align' => 'right', 'text' => $addOptionButton)
	)
));

$editTable->addBodyRow(array(
	'columns' => array(
		array('colspan' => 2, 'text' => '<b>Current Search Options</b>')
	)
));

$trashBin = htmlBase::newElement('div')
	->addClass('searchTrashBin')
	->html('Drop Here To Remove<div class="ui-icon ui-icon-trash" style="float:left;"></div>');

$editTable->addBodyRow(array(
	'columns' => array(
		array('colspan' => 2, 'text' => '<hr>' . $trashBin->draw() . '<hr>')
	)
));

require(sysConfig::getDirFsCatalog() . 'includes/modules/infoboxes/search/infobox.php');
$classObj = new InfoBoxSearch();

$liItems = '';
$Qitems = Doctrine_Query::create()
	->from('TemplatesInfoboxSearchGuided i')
	->leftJoin('i.TemplatesInfoboxSearchGuidedDescription id')
	->where('i.template_name = ?', $template)
	->orderBy('i.option_sort')
	->execute()->toArray(true);
if ($Qitems){
	foreach($Qitems as $iInfo){
		$optionId = $iInfo['option_id'];
		$optionType = $iInfo['option_type'];
		$optionSort = $iInfo['option_sort'];
		$heading = $iInfo['TemplatesInfoboxSearchGuidedDescription'][Session::get('languages_id')]['search_title'];

		$liItems .= '<li id="options_' . $optionType . '_' . $optionId . '" data-option_type="' . $optionType . '" data-option_id="' . $optionId . '">' .
			'<div class="ui-widget ui-widget-content ui-corner-all">' .
			'<table cellpadding="2" cellspacing="0" border="0">' .
			'<tr>' .
			'<td valign="top">' .
			'<b>Heading</b><br />' .
			'<textarea name="option_heading[' . $optionType . '][' . $optionId . ']" rows="3" cols="50">' .
			$heading .
			'</textarea>' .
			'<input type="hidden" name="option[' . $optionType . '][]" value="' . $optionId . '">' .
			'<input type="hidden" class="sortBox" name="option_sort[' . $optionType . '][' . $optionId . ']" value="' . $optionSort . '">' .
			'</td>' .
			'</tr>' .
			'</table>' .
			'</div>' .
			'</li>';
	}
}
$editTable->addBodyRow(array(
	'columns' => array(
		array('colspan' => 2, 'text' => '<ul class="searchOptions">' . $liItems . '</ul>')
	)
));

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('colspan' => 2, 'text' => $editTable->draw())
	)
));
