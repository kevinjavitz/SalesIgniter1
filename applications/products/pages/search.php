<?php
	$SearchCriteria = htmlBase::newElement('fieldset')
	->setLegend(sysLanguage::get('HEADING_SEARCH_CRITERIA'))
	->append(htmlBase::newElement('input')->setName('keywords')->css(array('width' => '100%')))
	->append(htmlBase::newElement('div')->css(array('text-align' => 'right'))->append(htmlBase::newElement('checkbox')->setName('search_in_description')->setValue('1')->setLabelPosition('before')->setLabel(sysLanguage::get('TEXT_SEARCH_IN_DESCRIPTION'))));
	
	$ButtonDiv = htmlBase::newElement('div')
	->css(array('margin-top' => '.5em'))
	//->append(htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_SEARCH_HELP_LINK'))->setHref('javascript:popupWindow(\'' . itw_app_link('appExt=infoPages&dialog=true', 'show_page', 'search_help') . '\')'))
	->append(htmlBase::newElement('button')->usePreset('search')->setType('submit')->css(array('float' => 'right')));
	
	$AdvancedTable = htmlBase::newElement('table')
	->setCellPadding(2)->setCellSpacing(0);
	
	$AdvancedTable->addBodyRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('ENTRY_CATEGORIES')),
			array('text' => tep_draw_pull_down_menu('categories_id', tep_get_categories(array(array('id' => '', 'text' => sysLanguage::get('TEXT_ALL_CATEGORIES'))))))
		)
	));
	if(sysConfig::get('SHOW_INCLUDE_SUBCATEGORIES') == 'true'){
	$AdvancedTable->addBodyRow(array(
		'columns' => array(
			array('text' => ''),
			array('text' => tep_draw_checkbox_field('inc_subcat', '1', true) . ' ' . sysLanguage::get('ENTRY_INCLUDE_SUBCATEGORIES'))
		)
	));
	}

	
	EventManager::notify('AdvancedSearchAddSearchFields', &$AdvancedTable);
	if(sysConfig::get('SHOW_PRICE_FROM_TO') == 'true'){
	$AdvancedTable->addBodyRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('ENTRY_PRICE_FROM')),
			array('text' => htmlBase::newElement('input')->setName('pfrom'))
		)
	));
	
	$AdvancedTable->addBodyRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('ENTRY_PRICE_TO')),
			array('text' => htmlBase::newElement('input')->setName('pto'))
		)
	));
	}
	if(sysConfig::get('SHOW_DATE_FROM_TO') == 'true'){
	$AdvancedTable->addBodyRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('ENTRY_DATE_FROM')),
			array('text' => htmlBase::newElement('input')->setName('dfrom'))
		)
	));
	
	$AdvancedTable->addBodyRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('ENTRY_DATE_TO')),
			array('text' => htmlBase::newElement('input')->setName('dto'))
		)
	));
	}
	
	$pageContents = '<form name="advanced_search" action="' . itw_app_link(null, 'products', 'search_result') . '" method="get" onsubmit="return check_form(this);">' . 
		tep_hide_session_id() . 
		$SearchCriteria->draw() . 
		$ButtonDiv->draw() . 
		$AdvancedTable->draw() . 
	'</form>';
	
	$pageContent->set('pageTitle', sysLanguage::get('HEADING_TITLE_SEARCH'));
	$pageContent->set('pageContent', $pageContents);
