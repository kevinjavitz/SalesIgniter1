<?php
/*
	Info Pages Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/
	$parameters = array(
		'page_type'  => 'popup',
		'sort_order' => '',
		'status'     => '',
		'page_key'   => ''
	);
	$pInfo = new objectInfo($parameters);

	$languages = tep_get_languages();
	for($i=0, $n=sizeof($languages); $i<$n; $i++){
		$lID = $languages[$i]['id'];
		if (!isset($pInfo->pages_title)) $pInfo->pages_title = array();
		if (!isset($pInfo->pages_html_text)) $pInfo->pages_html_text = array();
		if (!isset($pInfo->intorext)) $pInfo->intorext = array();
		if (!isset($pInfo->externallink)) $pInfo->externallink = array();
		if (!isset($pInfo->link_target)) $pInfo->link_target = array();

		$pInfo->pages_title[$lID] = '';
		$pInfo->pages_html_text[$lID] = '';
		$pInfo->intorext[$lID] = '';
		$pInfo->externallink[$lID] = '';
		$pInfo->link_target[$lID] = '';
	}

	if (isset($_GET['pID'])){
		$Qpage = Doctrine_Query::create()
		->from('Pages p')
		->leftJoin('p.PagesDescription pd')
		->where('p.pages_id = ?', (int)$_GET['pID'])
		->execute()->toArray(true);

		$pInfo->page_type = $Qpage[0]['page_type'];
		$pInfo->page_key = $Qpage[0]['page_key'];
		$pInfo->sort_order = $Qpage[0]['sort_order'];
		$pInfo->status = $Qpage[0]['status'];

		foreach($Qpage[0]['PagesDescription'] as $lID => $pageDesc){
			$pInfo->pages_title[$lID] = $pageDesc['pages_title'];
			$pInfo->pages_html_text[$lID] = $pageDesc['pages_html_text'];
			$pInfo->intorext[$lID] = $pageDesc['intorext'];
			$pInfo->externallink[$lID] = $pageDesc['externallink'];
			$pInfo->link_target[$lID] = $pageDesc['link_target'];
		}
	}elseif (tep_not_null($_POST)){
		$pInfo->objectInfo($_POST);
	}

	$pageTypeMenu = htmlBase::newElement('selectbox')->setName('page_type');
	foreach($extraInfoPages as $k => $v){
		$pageTypeMenu->addOption($k, $v);
	}
	$pageTypeMenu->selectOptionByValue($pInfo->page_type);
	
	$pageKeyInput = htmlBase::newElement('input')
	->setName('page_key')
	->setValue($pInfo->page_key);

	$tabsObj = htmlBase::newElement('tabs')->setId('languageTabs');
	for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
		$lID = $languages[$i]['id'];

		/* Build all inputs that are needed --BEGIN-- */
		$titleInput = htmlBase::newElement('input')
		->attr('language_id', $lID)
		->css(array(
			'width' => '75%'
		))
		->addClass('titleInput')
		->setName('pages_title[' . $lID . ']')
		->setValue($pInfo->pages_title[$lID]);

		$internalButton = htmlBase::newElement('radio')
		->setName('intorext[' . $lID . ']')
		->setValue('0')
		->setLabel(sysLanguage::get('TEXT_TARGET_INTERNAL'))
		->setChecked(($pInfo->intorext[$lID] == '' || $pInfo->intorext[$lID] == '0'))
		->attr('onclick', 'disableIt(\'externallink_' . $lID . '\');');

		$externalButton = htmlBase::newElement('radio')
		->setName('intorext[' . $lID . ']')
		->setValue('1')
		->setLabel(sysLanguage::get('TEXT_TARGET_EXTERNAL'))
		->setChecked(($pInfo->intorext[$lID] == '1'))
		->attr('onclick', 'enableIt(\'externallink_' . $lID . '\');');

		$extLinkInput = htmlBase::newElement('input')
		->setId('externallink_'.$lID)
		->css(array(
			'width' => '75%'
		))
		->setName('externallink[' . $lID . ']')
		->setValue($pInfo->externallink[$lID]);

		if ($pInfo->intorext[$lID] == '' || $pInfo->intorext[$lID] == '0'){
			$extLinkInput->attr('disabled', 'disabled');
		}

		$linkTargetMenu = htmlBase::newElement('selectbox')
		->setName('link_target[' . $lID . ']')
		->selectOptionByValue($pInfo->link_target[$lID]);
		$linkTargetMenu->addOption('1', sysLanguage::get('TEXT_TARGET_NEWWINDOW'));
		$linkTargetMenu->addOption('2', 'Javascript Dialog (Internal Links Only)');

		$ckField = htmlBase::newElement('ck_editor')
		->setName('pages_html_text[' . $lID . ']')
		->attr('language_id', $lID)
		->attr('cols', 90)
		->attr('rows', 20)
		->html($pInfo->pages_html_text[$lID]);
		/* Build all inputs that are needed --END-- */

		/* Build the table --BEGIN-- */
		$editTable = htmlBase::newElement('table')
		->setCellPadding(3)
		->setCellSpacing(0)
		->css(array(
			'width' => '100%'
		));

		$editTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'css' => array('width' => '150px'), 'text' => sysLanguage::get('TEXT_PAGES_TITLE')),
				array('addCls' => 'main','text' => $titleInput)
			)
		));

		$editTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'css' => array('width' => '150px'), 'text' => sysLanguage::get('TEXT_PAGES_INTEXT')),
				array('addCls' => 'main','text' => $internalButton->draw() . '&nbsp;&nbsp;' . $externalButton->draw())
			)
		));

		$editTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'css' => array('width' => '150px'), 'text' => sysLanguage::get('TEXT_PAGES_EXTERNAL_LINK')),
				array('addCls' => 'main','text' => $extLinkInput)
			)
		));

		$editTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'css' => array('width' => '150px'), 'text' => sysLanguage::get('TEXT_TARGET')),
				array('addCls' => 'main','text' => $linkTargetMenu)
			)
		));
		/* Build the table --END-- */

		/* Build the tabbed interface --BEGIN-- */
		$imgObj = htmlBase::newElement('image')
		->setSource(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image']);

		$tabsObj->addTabHeader('tab_' . $lID, array(
			'text' => $imgObj->draw() . '&nbsp;' . $languages[$i]['name']
		))->addTabPage('tab_' . $lID, array(
			'text' => $editTable->draw() . '<br />' . $ckField->draw() . '<br />' . sysLanguage::get('TEXT_PAGES_PAGE_NOTE')
		));
		/* Build the tabbed interface --END-- */
	}
	
	$topTable = htmlBase::newElement('table')
	->setCellPadding(3)
	->setCellSpacing(0);
	
	$topTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main', 'text' => sysLanguage::get('TEXT_PAGES_TYPE')),
			array('addCls' => 'main', 'text' => $pageTypeMenu)
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
			array('addCls' => 'main', 'text' => sysLanguage::get('TEXT_PAGES_SORT_ORDER')),
			array('addCls' => 'main', 'text' => tep_draw_input_field('sort_order', $pInfo->sort_order))
		)
	));
	
	$saveButton = htmlBase::newElement('button')->usePreset('save')->setType('submit');
	$cancelButton = htmlBase::newElement('button')->usePreset('cancel')
	->setHref(itw_app_link(tep_get_all_get_params(array('action')), null, 'default'));

	$buttonContainer = new htmlElement('div');
	$buttonContainer->append($saveButton)->append($cancelButton)->css(array(
		'float' => 'right',
		'width' => 'auto'
	))->addClass('ui-widget');
	
	$multiStore = $appExtension->getExtension('multiStore');
	if ($multiStore !== false && $multiStore->isEnabled() === true){
		if (isset($multiStore->pagePlugin)){
			$multiStore->pagePlugin->loadTabs($tabsObj);
		}
	}
	
	$pageForm = htmlBase::newElement('form')
	->attr('name', 'new_page')
	->attr('action', itw_app_link(tep_get_all_get_params(array('action')) . 'action=save'))
	->attr('method', 'post')
	->html($topTable->draw() . '<br /><div style="position:relative;">' . $tabsObj->draw() . '</div><br />' . $buttonContainer->draw());
	
	$headingTitle = htmlBase::newElement('div')
	->addClass('pageHeading')
	->html(sysLanguage::get('HEADING_TITLE'));
	
	echo $headingTitle->draw() . '<br />' . $pageForm->draw();
?>