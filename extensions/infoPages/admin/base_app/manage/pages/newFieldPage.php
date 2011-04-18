<?php
/*
	Info Pages Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/
	$parameters = array(
		'page_type'            => 'field',
		'sort_order'           => '',
		'status'               => '',
		'page_key'             => '',
		'top_content_id'       => '',
		'bottom_content_id'    => '',
		'listing_type'         => 'field',
		'listing_field_id'     => '',
		'listing_attribute_id' => ''
	);
	$pInfo = new objectInfo($parameters);

	if (isset($_GET['pID'])){
		$Qpage = Doctrine_Query::create()
		->from('Pages p')
		->leftJoin('p.PagesFields pf')
		->where('p.pages_id = ?', (int)$_GET['pID'])
		->execute()->toArray(true);

		$pInfo->page_type = $Qpage[0]['page_type'];
		$pInfo->page_key = $Qpage[0]['page_key'];
		$pInfo->sort_order = $Qpage[0]['sort_order'];
		$pInfo->status = $Qpage[0]['status'];
		$pInfo->top_content_id = $Qpage[0]['PagesFields']['top_content_id'];
		$pInfo->bottom_content_id = $Qpage[0]['PagesFields']['bottom_content_id'];
		$pInfo->listing_type = $Qpage[0]['PagesFields']['listing_type'];
		$pInfo->listing_field_id = $Qpage[0]['PagesFields']['listing_field_id'];
		$pInfo->listing_attribute_id = $Qpage[0]['PagesFields']['listing_attribute_id'];
	}elseif (tep_not_null($_POST)){
		$pInfo->objectInfo($_POST);
	}

	$pageKeyInput = htmlBase::newElement('input')
	->setName('page_key')
	->setValue($pInfo->page_key);

	$topContentSelect = htmlBase::newElement('selectbox')
	->setName('top_content_id')
	->selectOptionByValue($pInfo->top_content_id);
	$topContentSelect->addOption('', 'Don\'t Show');

	$bottomContentSelect = htmlBase::newElement('selectbox')
	->setName('bottom_content_id')
	->selectOptionByValue($pInfo->bottom_content_id);
	$bottomContentSelect->addOption('', 'Don\'t Show');

	$typeSelect = htmlBase::newElement('selectbox')
	->setName('listing_type')
	->selectOptionByValue($pInfo->listing_type);
	$typeSelect->addOption('attribute', 'Attributes');
	$typeSelect->addOption('field', 'Custom Fields');

	$Qcontent = Doctrine_Query::create()
	->select('p.pages_id, pd.pages_title')
	->from('Pages p')
	->leftJoin('p.PagesDescription pd')
	->where('pd.language_id = ?', Session::get('languages_id'))
	->andWhere('page_type = ?', 'block')
	->orderBy('pd.pages_title')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if ($Qcontent){
		foreach($Qcontent as $cInfo){
			$topContentSelect->addOption($cInfo['pages_id'], $cInfo['PagesDescription'][0]['pages_title']);
			$bottomContentSelect->addOption($cInfo['pages_id'], $cInfo['PagesDescription'][0]['pages_title']);
		}
	}

	$fieldSelect = htmlBase::newElement('selectbox')
	->setName('listing_field_id')
	->selectOptionByValue($pInfo->listing_field_id);

	$Qfields = Doctrine_Query::create()
	->select('f.field_id, fd.field_name')
	->from('ProductsCustomFields f')
	->leftJoin('f.ProductsCustomFieldsDescription fd')
	->where('fd.language_id = ?', Session::get('languages_id'))
	->andWhere('f.input_type != ?', 'textarea')
	->orderBy('fd.field_name')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if ($Qfields){
		foreach($Qfields as $fInfo){
			$fieldSelect->addOption($fInfo['field_id'], $fInfo['ProductsCustomFieldsDescription'][0]['field_name']);
		}
	}

	$attributeSelect = htmlBase::newElement('selectbox')
	->setName('listing_attribute_id')
	->selectOptionByValue($pInfo->listing_attribute_id);

	$Qattributes = Doctrine_Query::create()
	->select('o.products_options_id, od.products_options_name')
	->from('ProductsOptions o')
	->leftJoin('o.ProductsOptionsDescription od')
	->where('od.language_id = ?', Session::get('languages_id'))
	->orderBy('od.products_options_name')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if ($Qattributes){
		foreach($Qattributes as $aInfo){
			$attributeSelect->addOption($aInfo['products_options_id'], $aInfo['ProductsOptionsDescription'][0]['products_options_name']);
		}
	}

	$topTable = htmlBase::newElement('table')
	->setCellPadding(3)
	->setCellSpacing(0);

	$topTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main', 'text' => sysLanguage::get('TEXT_PAGES_TYPE')),
			array('addCls' => 'main', 'text' => ucwords($pInfo->page_type))
		)
	));

	$topTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main', 'text' => sysLanguage::get('TEXT_PAGES_KEY')),
			array('addCls' => 'main', 'text' => $pageKeyInput->draw() . '&nbsp;&nbsp;' . sysLanguage::get('TEXT_INFO_PAGES_KEY'))
		)
	));

	$topTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main', 'text' => 'Top Content Block: '),
			array('addCls' => 'main', 'text' => $topContentSelect)
		)
	));

	$topTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main', 'text' => 'Bottom Content Block: '),
			array('addCls' => 'main', 'text' => $bottomContentSelect)
		)
	));

	$topTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main', 'text' => 'Listing Type: '),
			array('addCls' => 'main', 'text' => $typeSelect)
		)
	));

	$topTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main', 'text' => 'Field: '),
			array('addCls' => 'main', 'text' => $fieldSelect)
		)
	));

	$topTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main', 'text' => 'Attribute: '),
			array('addCls' => 'main', 'text' => $attributeSelect)
		)
	));

	$topTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main', 'text' => sysLanguage::get('TEXT_PAGES_SORT_ORDER')),
			array('addCls' => 'main', 'text' => tep_draw_input_field('sort_order', $pInfo->sort_order))
		)
	));


	/*
	 * this event expects an array having two elements: label and content | i.e. (array(label=>'', content=>''))
	 * -BEGIN-
	 */
	$lID = 0; //really this app does not support multi-lang, but in order to make all in the same way
	$contents_middle = array();
	EventManager::notify('InfoFieldPagesFormMiddle', $lID, &$contents_middle);

	if (is_array($contents_middle)) {
		foreach($contents_middle as $element){
			if (is_array($element)) {

				if (!isset($element['label'])) $element['label'] = 'no_defined';
				if (!isset($element['content'])) $element['content'] = 'no_defined';

				$topTable->addBodyRow(array(
					'columns' => array(
						array('addCls' => 'main', 'css' => array('width' => '150px'), 'text' => $element['label']),
						array('addCls' => 'main','text' => $element['content'])
					)
				));
			}
			else {
				$editTable->addBodyRow(array(
					'columns' => array(
						array('addCls' => 'main', 'colspan' => 2, 'text' => $element['content'])
					)
				));
			}
		}
	}
	/* -END- */



	$saveButton = htmlBase::newElement('button')->usePreset('save')->setType('submit');
	$cancelButton = htmlBase::newElement('button')->usePreset('cancel')
	->setHref(itw_app_link(tep_get_all_get_params(array('action')), null, 'default'));

	$buttonContainer = new htmlElement('div');
	$buttonContainer->append($saveButton)->append($cancelButton)->css(array(
		'float' => 'right',
		'width' => 'auto'
	))->addClass('ui-widget');

	$pageForm = htmlBase::newElement('form')
	->attr('name', 'new_page')
	->attr('action', itw_app_link(tep_get_all_get_params(array('action')) . 'action=saveFieldPage'))
	->attr('method', 'post')
	->html($topTable->draw() . '<br />' . $buttonContainer->draw());

	$headingTitle = htmlBase::newElement('div')
	->addClass('pageHeading')
	->html(sysLanguage::get('HEADING_TITLE'));

	echo $headingTitle->draw() . '<br />' . $pageForm->draw();
?>
