<script language="javascript" type="text/javascript">
	$(document).ready(function (){
		var indexKey = 1;
		$('select[name="option_type"]').change(function (){
			var Option = $(this).val();
			if (Option == ''){
				$('.noSelection').show();
				$('.optionBox').hide();
			}else if(Option == 'price'){
				$('.optionBox, .noSelection').hide();
				$('#priceContainer').show();
				$('input[name="option_id[' + Option + '][start]"]').val('');
				$('input[name="option_id[' + Option + '][stop]"]').val('');
				//indexKey++;
			}else if(Option == 'priceppr'){
				$('.optionBox, .noSelection').hide();
				$('#pricePPRContainer').show();
				$('input[name="option_id[' + Option + '][start]"]').val('');
				$('input[name="option_id[' + Option + '][stop]"]').val('');
				//indexKey++;
			} else{
				$('.optionBox, .noSelection').hide();
				$('select[name="option_id[' + Option + ']"]').val('').show();
			}
		});

		$('.addPriceOptionButton').click(function (){
			$('#priceContainer').append(
				'<br /><label>Start:</label>' +
				'<input name="option_id[price][start][' + indexKey + ']">' +
				'<label>End:</label>' +
				'<input name="option_id[price][stop][' + indexKey + ']"><br />'
			);
			indexKey++;
		});

		$('.addPricePPROptionButton').click(function (){
			$('#pricePPRContainer').append(
				'<br /><label>Start:</label>' +
				'<input name="option_id[priceppr][start][' + indexKey + ']">' +
				'<label>End:</label>' +
				'<input name="option_id[priceppr][stop][' + indexKey + ']"><br />'
			);
			indexKey++;
		});

		$('.addOptionButton').click(function (){
			var optionType = $('select[name="option_type"]').val();
			var addToList = false;
			if (optionType == 'attribute' || optionType == 'custom_field'){
				var heading = $('select[name="option_id[' + optionType + ']"]').find('option:selected').html();
				var optionId = $('select[name="option_id[' + optionType + ']"]').find('option:selected').val();
				if (optionId != '' && $('input[name="option[' + optionType + '][]"][value="' + optionId + '"]').size() <= 0){
					addToList = true;
				}
			}else{
				if (optionType == 'purchase_type'){
					var heading = 'Purchase Type';
				}else if (optionType == 'price'){
					var heading = 'Price';
				}else if (optionType == 'priceppr'){
					var heading = 'Price PPR';
				}
				optionId = optionType;
				addToList = true;
			}

			if (addToList === true){
				var idx = $('.searchOptions li').size();
				var filters = '';
				var filtersppr = '';

				if(optionType == 'price'){
					optionId = $('.priceOptions').size() + 1;
					$('input',$('#priceContainer')).each(function (){
						filters += '<input type="hidden" class="sortBox" name="' +$(this).attr('name') + '" value="'+$(this).val()+'"';
					});
					var filter_start = $('input[name="option_id[' + optionType + '][start]"]').val();
					var filter_stop = $('input[name="option_id[' + optionType + '][stop]"]').val();
					var liHtml = '<li class="priceOptions" id="options_' + optionType + '_' + optionId + '" data-option_type="' + optionType + '" data-option_id="' + optionId + '" data-start="' + filter_start + '" data-stop="' + filter_stop + '">' +
						'<div class="ui-widget ui-widget-content ui-corner-all">' +
						'<table cellpadding="2" cellspacing="0" border="0">' +
						'<tr>' +
						'<td valign="top">' +
						'<b>Heading</b><br/><textarea name="option_heading[' + optionType + '][' + optionId + ']" rows="1" cols="50">' +
						heading +
						'</textarea>' +
						'Price filter from: ' + filter_start + ' to: ' + filter_stop +
						'<input type="hidden" name="option[' + optionType + '][]" value="' + optionId + '">' +
						'<input type="hidden" class="sortBox" name="option_sort[' + optionType + '][start][' + optionId + ']" value="' + filter_start + '">' +
						'<input type="hidden" class="sortBox" name="option_sort[' + optionType + '][stop][' + optionId + ']" value="' + filter_stop + '">' +
						'</td>' +
						'</tr>' +
						'</table>' +
						'</div>' +
						'</li>';
					indexKey = 1;
				}else if(optionType == 'priceppr'){
					optionId = $('.pricePPROptions').size() + 1;
					$('input',$('#pricePPRContainer')).each(function (){
						filtersppr += '<input type="hidden" class="sortBox" name="' +$(this).attr('name') + '" value="'+$(this).val()+'"';
					});
					var filter_pprstart = $('input[name="option_id[' + optionType + '][start]"]').val();
					var filter_pprstop = $('input[name="option_id[' + optionType + '][stop]"]').val();
					var liHtml = '<li class="pricePPROptions" id="options_' + optionType + '_' + optionId + '" data-option_type="' + optionType + '" data-option_id="' + optionId + '" data-pprstart="' + filter_pprstart + '" data-pprstop="' + filter_pprstop + '">' +
						'<div class="ui-widget ui-widget-content ui-corner-all">' +
						'<table cellpadding="2" cellspacing="0" border="0">' +
						'<tr>' +
						'<td valign="top">' +
						'<b>Heading</b><br/><textarea name="option_heading[' + optionType + '][' + optionId + ']" rows="1" cols="50">' +
						heading +
						'</textarea>' +
						'PPR Price filter from: ' + filter_pprstart + ' to: ' + filter_pprstop +
						'<input type="hidden" name="option[' + optionType + '][]" value="' + optionId + '">' +
						'<input type="hidden" class="sortBox" name="option_sort[' + optionType + '][start][' + optionId + ']" value="' + filter_pprstart + '">' +
						'<input type="hidden" class="sortBox" name="option_sort[' + optionType + '][stop][' + optionId + ']" value="' + filter_pprstop + '">' +
						'</td>' +
						'</tr>' +
						'</table>' +
						'</div>' +
						'</li>';
					indexKey = 1;
				}else  {
					var liHtml = '<li id="options_' + optionType + '_' + optionId + '" data-option_type="' + optionType + '" data-option_id="' + optionId + '">' +
						'<div class="ui-widget ui-widget-content ui-corner-all">' +
						'<table cellpadding="2" cellspacing="0" border="0">' +
						'<tr>' +
						'<td valign="top">' +
						'<b>Heading</b><br/><textarea name="option_heading[' + optionType + '][' + optionId + ']" rows="1" cols="50">' +
						heading +
						'</textarea>' +
						'<input type="hidden" name="option[' + optionType + '][]" value="' + optionId + '">' +
						'<input type="hidden" class="sortBox" name="option_sort[' + optionType + '][' + optionId + ']" value="' + idx + '">' +
						'</td>' +
						'</tr>' +
						'</table>' +
						'</div>' +
						'</li>';
				}

				$('.searchOptions').append(liHtml);
				$('.searchOptions').sortable('refresh');
			}
		}).button();

		$('.searchOptions').sortable({
			revert: true,
			tolerance: 'intersect',
			forcePlaceholderSize: true,
			placeholder: 'ui-state-highlight',
			forceHelperSize: true,
			opacity: 0.5,
			update: function (e, ui){
				var self = ui.item;
				$('.searchOptions li').each(function (i, el){
					$('.sortBox', el).val(i + 1);
				});
			}
		});

		$('.searchTrashBin').droppable({
			accept: 'li',
			tolerance: 'touch',
			hoverClass: 'ui-state-highlight',
			drop: function (e, ui){
				$(ui.draggable).remove();
				$('.searchOptions').sortable('refresh');
			}
		});
	});
</script>
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
		->addOption('priceppr', 'Product PPR Price')
		->addOption('custom_field', 'Custom Field')
		->addOption('attribute', 'Attribute');

	$DropDownSelectionContainer = htmlBase::newElement('span');

	$PurchaseTypeIsDropDownCheckBox = htmlBase::newElement('checkbox')
		->setName('dropdown[purchase_type]')
		->val('1')
		->setChecked(isset($WidgetSettings->dropdown->purchase_type))
		->setLabelPosition('after')
		->setLabel('Purchase Type');
	$DropDownSelectionContainer->append($PurchaseTypeIsDropDownCheckBox);

	$PriceIsDropDownCheckBox = htmlBase::newElement('checkbox')
		->setName('dropdown[price]')
		->val('1')
		->setChecked(isset($WidgetSettings->dropdown->price))
		->setLabelPosition('after')
		->setLabel('Price');
	$DropDownSelectionContainer->append($PriceIsDropDownCheckBox);

	$PricePPRIsDropDownCheckBox = htmlBase::newElement('checkbox')
		->setName('dropdown[priceppr]')
		->val('1')
		->setChecked(isset($WidgetSettings->dropdown->priceppr))
		->setLabelPosition('after')
		->setLabel('Price PPR');
	$DropDownSelectionContainer->append($PricePPRIsDropDownCheckBox);

	$CustomFieldIsDropDownCheckBox = htmlBase::newElement('checkbox')
		->setName('dropdown[custom_field]')
		->val('1')
		->setChecked(isset($WidgetSettings->dropdown->custom_field))
		->setLabelPosition('after')
		->setLabel('Custom Field');
	$DropDownSelectionContainer->append($CustomFieldIsDropDownCheckBox);

	$AttributeIsDropDownCheckBox = htmlBase::newElement('checkbox')
		->setName('dropdown[attribute]')
		->val('1')
		->setChecked(isset($WidgetSettings->dropdown->attribute))
		->setLabelPosition('after')
		->setLabel('Attribute');
	$DropDownSelectionContainer->append($AttributeIsDropDownCheckBox);

	$editTable->addBodyRow(array(
                            'columns' => array(
	                            array('text' => 'Select the option types to display as a drop down: '),
	                            array('text' => $DropDownSelectionContainer)
                            )
                       ));
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

	$PriceStartOptionBox = htmlBase::newElement('input')
	->setName('option_id[price][start]')
	->setLabelPosition('before')
	->setLabel('Start:');

	$PriceStopOptionBox = htmlBase::newElement('input')
	->setLabelPosition('before')
	->setLabel('End:')
	->setName('option_id[price][stop]');

	$addPriceOptionButton = htmlBase::newElement('button')
	->addClass('addPriceOptionButton')
	->usePreset('install')
	->setText('Add Price Slab');

	$priceOptionsContainer = htmlBase::newElement('div')
		->setId('priceContainer')
		->addClass('optionBox')
		->append($PriceStartOptionBox)
		->append($PriceStopOptionBox)
		->hide();

	$PricePPRStartOptionBox = htmlBase::newElement('input')
	->setName('option_id[priceppr][start]')
	->setLabelPosition('before')
	->setLabel('Start:');

	$PricePPRStopOptionBox = htmlBase::newElement('input')
	->setLabelPosition('before')
	->setLabel('End:')
	->setName('option_id[priceppr][stop]');

	$addPricePPROptionButton = htmlBase::newElement('button')
	->addClass('addPricePPROptionButton')
	->usePreset('install')
	->setText('Add PPR Price Slab');

	$pricePPROptionsContainer = htmlBase::newElement('div')
		->setId('pricePPRContainer')
		->addClass('optionBox')
		->append($PricePPRStartOptionBox)
		->append($PricePPRStopOptionBox)
		->hide();

	$editTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'Option: '),
			array('text' => $PleaseSelectText->draw() . $AttributeOptionBox->draw() . $CustomFieldOptionBox->draw() . $priceOptionsContainer->draw() . $pricePPROptionsContainer->draw())
		)
	));

######################################

######################################

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

	$Qitems = (array)$WidgetSettings->searchOptions;

	if (isset($Qitems)){
		foreach($Qitems as $type){
			$type = (array)$type;
			foreach($type as $iInfo){
				$iInfo = (array)$iInfo;
				foreach($iInfo['search_title'] as $key => $search_title ){
					if((int)$key == (int)Session::get('languages_id')){
						$heading = $search_title;
						break;
					}
				}
				$optionId = $iInfo['option_id'];
				$optionType = $iInfo['option_type'];
				$optionSort = $iInfo['option_sort'];
				if($optionType == 'price'){
					$priceStart = $iInfo['price_start'];
					$priceStop = $iInfo['price_stop'];
					$liItems .= '<li id="options_' . $optionType . '_' . $optionId . '" data-option_type="' . $optionType . '" data-option_id="' . $optionId . '">' .
					            '<div class="ui-widget ui-widget-content ui-corner-all">' .
					            '<table cellpadding="2" cellspacing="0" border="0">' .
					            '<tr>' .
					            '<td valign="top">' .
					            '<b>Heading</b><br />' .
					            '<textarea name="option_heading[' . $optionType . '][' . $optionId . ']" rows="3" cols="50">' .
					            $heading .
					            '</textarea>' .
					            'Price filter from: ' . $priceStart . ' to: ' . $priceStop .
					            '<input type="hidden" name="option[' . $optionType . '][]" value="' . $optionId . '">' .
					            '<input type="hidden" class="sortBox" name="option_sort[' . $optionType . '][start][' . $optionId . ']" value="' . $priceStart . '">' .
					            '<input type="hidden" class="sortBox" name="option_sort[' . $optionType . '][stop][' . $optionId . ']" value="' . $priceStop . '">' .
					            '</td>' .
					            '</tr>' .
					            '</table>' .
					            '</div>' .
					            '</li>';
				}
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
?>