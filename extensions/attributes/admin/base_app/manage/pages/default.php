<?php
	$languageId = Session::get('languages_id');
	
	$valuesListing = htmlBase::newElement('div')->attr('id', 'valuesListing')->css(array(
		'display'  => 'block',
		'width'    => '100%',
		'vertical-align' => 'top'
	));

	$optionsListing = htmlBase::newElement('div')->attr('id', 'optionsListing')->css(array(
		'display'  => 'block',
		'width'    => '100%'
	));

	$groupsListing = htmlBase::newElement('div')->attr('id', 'groupsListing')->css(array(
		'display'  => 'block',
		'width'    => '100%'
	));

	$iconCss = array(
		'float'    => 'right',
		'position' => 'relative',
		'top'      => '-4px',
		'right'    => '-4px'
	);

	$Qvalues = Doctrine_Query::create()
	->select('v.products_options_values_id, vd.products_options_values_name')
	->from('ProductsOptionsValues v')
	->leftJoin('v.ProductsOptionsValuesDescription vd')
	->where('vd.language_id = ?', $languageId)
	->orderBy('vd.products_options_values_name');

	$Result = $Qvalues->execute()->toArray();
	if ($Result){
		foreach($Result as $vInfo){
			$deleteIcon = htmlBase::newElement('icon')->setType('circleClose')->setTooltip('Click to delete value')
			->setHref(itw_app_link('appExt=attributes&action=removeValue&value_id=' . $vInfo['products_options_values_id']))
			->css($iconCss);

			$editIcon = htmlBase::newElement('icon')->setType('wrench')->setTooltip('Click to edit value')
			->setHref(itw_app_link('appExt=attributes&windowAction=edit&action=getValueWindow&value_id=' . $vInfo['products_options_values_id']))
			->css($iconCss);

			$newValueWrapper = htmlBase::newElement('div')->css(array(
				'float'   => 'left',
				'width'   => '150px',
				'height'  => '2em',
				'padding' => '.3em',
				'margin'  => '.2em'
			))->addClass('ui-widget ui-widget-content ui-corner-all optionValue draggableValue')
			->html('<b><span class="valueName" value_id="' . $vInfo['products_options_values_id'] . '">' . $vInfo['ProductsOptionsValuesDescription'][$languageId]['products_options_values_name'] . '</span></b>' . $deleteIcon->draw() . $editIcon->draw());

			$valuesListing->append($newValueWrapper);
		}
	}

	$Qoptions = Doctrine_Query::create()
	->select('o.products_options_id, od.products_options_name')
	->from('ProductsOptions o')
	->leftJoin('o.ProductsOptionsDescription od')
	->where('od.language_id = ?', $languageId);

	$trashBin = new htmlElement('div');
	$trashBin->addClass('trashBin')->html(sysLanguage::get('TEXT_TRASH_BIN') . '<div class="ui-icon ui-icon-trash" style="float:left;"></div>');

	$Result = $Qoptions->execute()->toArray();
	if ($Result){
		foreach($Result as $oInfo){
			$trashBin->attr('option_id', $oInfo['products_options_id']);
			$sortableList = htmlBase::newElement('sortable_list');

			$QoptionsValues = Doctrine_Query::create()
			->select('ov.products_options_values_id, ovd.products_options_values_name, v2o.sort_order')
			->from('ProductsOptionsValues ov')
			->leftJoin('ov.ProductsOptionsValuesDescription ovd')
			->leftJoin('ov.ProductsOptionsValuesToProductsOptions v2o')
			->where('ovd.language_id = ?', $languageId)
			->andWhere('v2o.products_options_id = ?', $oInfo['products_options_id'])
			->orderBy('v2o.sort_order')
			->execute()->toArray();
			if ($QoptionsValues){
				foreach($QoptionsValues as $vInfo){
					$valuesDescription = $vInfo['ProductsOptionsValuesDescription'][$languageId];
				
					$liObj = new htmlElement('li');
					$liObj->css(array(
						'font-size' => '.8em',
						'line-height' => '1.1em'
					))
					->attr('id', 'value_' . $vInfo['products_options_values_id'])
					->attr('sort_order', $vInfo['ProductsOptionsValuesToProductsOptions'][0]['sort_order'])
					->html($valuesDescription['products_options_values_name']);
					$sortableList->addItemObj($liObj);
				}
			}
			
			$deleteIcon = htmlBase::newElement('icon')->setType('circleClose')->setTooltip('Click to delete option')
			->setHref(itw_app_link('appExt=attributes&action=removeOption&option_id=' . $oInfo['products_options_id']))
			->css($iconCss);

			$editIcon = htmlBase::newElement('icon')->setType('wrench')->setTooltip('Click to edit option')
			->setHref(itw_app_link('appExt=attributes&windowAction=edit&action=getOptionWindow&option_id=' . $oInfo['products_options_id']))
			->css($iconCss);

			$newOptionWrapper = htmlBase::newElement('div')->attr('option_id', $oInfo['products_options_id'])->css(array(
				'float'   => 'left',
				'width'   => '150px',
				'padding' => '.3em',
				'margin'  => '.2em'
			))->addClass('ui-widget ui-widget-content ui-corner-all productOption droppableOption draggableOption')
			->html('<b><span class="optionName" option_id="' . $oInfo['products_options_id'] . '">' . $oInfo['ProductsOptionsDescription'][$languageId]['products_options_name'] . '</span></b>' . $deleteIcon->draw() . $editIcon->draw() . '<hr>' . $trashBin->draw() . '<hr />' . $sortableList->draw());

			$optionsListing->append($newOptionWrapper);
		}
	}
	
	$QoptionsGroups = Doctrine_Query::create()
	->select('og.products_options_groups_name, og.products_options_groups_id')
	->from('ProductsOptionsGroups og')
	->orderBy('og.products_options_groups_name');

	$trashBin = new htmlElement('div');
	$trashBin->addClass('trashBin')->html(sysLanguage::get('TEXT_TRASH_BIN') . '<div class="ui-icon ui-icon-trash" style="float:left;"></div>');

	$Result = $QoptionsGroups->execute()->toArray();
	if ($Result){
		foreach($Result as $gInfo){
			$trashBin->attr('group_id', $gInfo['products_options_groups_id']);
			$sortableList = htmlBase::newElement('sortable_list');

			$Qoptions = Doctrine_Query::create()
			->select('o.products_options_id, od.products_options_name, o2g.sort_order')
			->from('ProductsOptions o')
			->leftJoin('o.ProductsOptionsDescription od')
			->leftJoin('o.ProductsOptionsToProductsOptionsGroups o2g')
			->where('o2g.products_options_groups_id = ?', $gInfo['products_options_groups_id'])
			->andWhere('od.language_id = ?', $languageId)
			->orderBy('o2g.sort_order')
			->execute()->toArray();
			if ($Qoptions){
				foreach($Qoptions as $goInfo){
					$liObj = new htmlElement('li');
					$liObj->css(array(
						'font-size' => '.8em',
						'line-height' => '1.1em'
					))
					->attr('id', 'option_' . $goInfo['products_options_id'])
					->attr('sort_order', $goInfo['ProductsOptionsToProductsOptionsGroups'][0]['sort_order'])
					->html($goInfo['ProductsOptionsDescription'][$languageId]['products_options_name']);
					$sortableList->addItemObj($liObj);
				}
			}
			
			$deleteIcon = htmlBase::newElement('icon')->setType('circleClose')->setTooltip('Click to delete group')
			->setHref(itw_app_link('appExt=attributes&action=removeGroup&group_id=' . $gInfo['products_options_groups_id']))
			->css($iconCss);

			$editIcon = htmlBase::newElement('icon')->setType('wrench')->setTooltip('Click to edit group')
			->setHref(itw_app_link('appExt=attributes&windowAction=edit&action=getGroupWindow&group_id=' . $gInfo['products_options_groups_id']))
			->css($iconCss);

			$newGroupWrapper = htmlBase::newElement('div')->attr('group_id', $gInfo['products_options_groups_id'])->css(array(
				'float'   => 'left',
				'width'   => '150px',
				'height'  => '150px',
				'padding' => '.3em',
				'margin'  => '.2em'
			))->addClass('ui-widget ui-widget-content ui-corner-all droppableGroup')
			->html('<b>' . $gInfo['products_options_groups_name'] . '</b>' . $deleteIcon->draw() . $editIcon->draw() . '<hr>' . $trashBin->draw() . '<hr />' . $sortableList->draw());

			$groupsListing->append($newGroupWrapper);
		}
	}
?>
 
 <div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE_ATTRIBUTES');?></div>
 <br />
 <?php
  $newOptionButton = htmlBase::newElement('div')->append(htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_NEW_OPTION'))->setId('newOption'));
  $newValueButton = htmlBase::newElement('div')->append(htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_NEW_VALUE'))->setId('newValue'));
  $newGroupButton = htmlBase::newElement('div')->append(htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_NEW_GROUP'))->setId('newGroup'));
  
  echo $newValueButton->draw().'Examples: small, medium, large';
  echo $valuesListing->draw().'<br style="clear:both"/>';
  
  echo $newOptionButton->draw().'Example: size<br/>After adding an option, drag the value down to the option';
  echo $optionsListing->draw().'<br style="clear:both"/>';
  
  echo $newGroupButton->draw().'Example: shirt<br/>Drag one or more options to a Group.<br/> You MUST put your option to a group to add attributes to a product, even if there is only one option in a group';
  echo $groupsListing->draw().'<br style="clear:both"/>';
 ?>
 
 <div id="newOptionDialog" title="<?php echo sysLanguage::get('WINDOW_TITLE_NEW_OPTION');?>"></div>
 <div id="editOptionDialog" title="<?php echo sysLanguage::get('WINDOW_TITLE_EDIT_OPTION');?>"></div>
 <div id="newValueDialog" title="<?php echo sysLanguage::get('WINDOW_TITLE_NEW_VALUE');?>"></div>
 <div id="editValueDialog" title="<?php echo sysLanguage::get('WINDOW_TITLE_EDIT_VALUE');?>"></div>
 <div id="newGroupDialog" title="<?php echo sysLanguage::get('WINDOW_TITLE_NEW_GROUP');?>"></div>
 <div id="editGroupDialog" title="<?php echo sysLanguage::get('WINDOW_TITLE_EDIT_GROUP');?>"></div>
