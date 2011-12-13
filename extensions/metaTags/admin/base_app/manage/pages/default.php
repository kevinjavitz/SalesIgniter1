<?php

/**
 * @brief Handle Meta Tags
 *
 * @details
 * Add Meta tags into html header
 *
 * @author Erick Romero
 * @version 1
 *
 * I.T. Web Experts, Rental Store v2
 * http://www.itwebexperts.com
 * Copyright (c) 2009 I.T. Web Experts
 * This script and it's source is not redistributable
 */

	$metaTags = $appExtension->getExtension('metaTags');

	//get Metatags defined

	$metaTagsDefault	= $metaTags->fetchPageMetaTags('D');
	$metaTagsSpecials	= $metaTags->fetchPageMetaTags('S');
	$metaTagsBestSeller	= $metaTags->fetchPageMetaTags('B');

	//get languages

	$languages = sysLanguage::getLanguages();

	//title at top

	echo sprintf(
		'<p class="pageHeading">%s</p>',
		sysLanguage::get('METATAGS_HEADING_TITLE')
	);


	//language tabs

	echo '<div id="lang_tabs">';


	//each language tabs (anchor - header)

	echo '<ul>';
	$lang_tab_headers = '';
	foreach($languages as $lInfo){
		$langImage 	= tep_image(sysConfig::getDirFsCatalog() . 'languages/' . $lInfo['directory'] . '/images/' . $lInfo['image'], $lInfo['name']);
		$showName 	= $lInfo['showName']();
		$lang_tab_header = sprintf (
			'<li class="ui-tabs-nav-item">
				<a href="#langTab_%s">
					<span>%s</span>
				</a>
			</li>',
			$lInfo['id'],
			$showName //update core
		);

		if($lInfo['id'] === sysLanguage::getId()){
			$lang_tab_headers =  $lang_tab_header . $lang_tab_headers;
		} else {
			$lang_tab_headers .=  $lang_tab_header;
		}
	}
	echo $lang_tab_headers;
	echo '</ul>';


	//each language tabs (div content)

	$layout = '';
	$lang_tab_contents = '';
	foreach($languages as $lInfo){

		$lID = $lInfo['id'];

		$layout .= sprintf('<div id="langTab_%s">', $lID);

		//metatas form's elements for defaults
		$values = isset($metaTagsDefault[$lID]) ? $metaTagsDefault[$lID] : false;
		$form_elems  = $metaTags->createFormElements($lID, $values, 'metatags');

		$layout .= sprintf(
			'<p class="pageHeading">%s</p>
			<table cellpadding="4" cellspacing="4">
					<tr> <td> %s </td> <td> %s </td> </tr>
					<tr> <td> %s </td> <td> %s </td> </tr>
					<tr> <td> %s </td> <td> %s </td> </tr>
			</table>%s
			<hr/>',

			sysLanguage::get('METATAGS_HEADING_DEFAULTS'),

			sysLanguage::get('HEADER_META_TITLE'),
			$form_elems['t']->draw(),

			sysLanguage::get('HEADER_META_DESC'),
			$form_elems['d']->draw(),

			sysLanguage::get('HEADER_META_KEYWORD'),
			$form_elems['k']->draw(),

			$form_elems['i']
		);


		//metatas form's elements for defaults
		$values = isset($metaTagsSpecials[$lID]) ? $metaTagsSpecials[$lID] : false;
		$form_elems  = $metaTags->createFormElements($lID, $values, 'metatags_special');

		$layout .= sprintf(
			'<p class="pageHeading">%s</p>
			<table cellpadding="4" cellspacing="4">
					<tr> <td> %s </td> <td> %s </td> </tr>
					<tr> <td> %s </td> <td> %s </td> </tr>
					<tr> <td> %s </td> <td> %s </td> </tr>
			</table>%s
			<hr/>',

			sysLanguage::get('METATAGS_HEADING_SPECIALS'),

			sysLanguage::get('HEADER_META_TITLE'),
			$form_elems['t']->draw(),

			sysLanguage::get('HEADER_META_DESC'),
			$form_elems['d']->draw(),

			sysLanguage::get('HEADER_META_KEYWORD'),
			$form_elems['k']->draw(),

			$form_elems['i']
		);


		//metatas form's elements for defaults
		$values = isset($metaTagsBestSeller[$lID]) ? $metaTagsBestSeller[$lID] : false;
		$form_elems  = $metaTags->createFormElements($lID, $values, 'metatags_best');

		$layout .= sprintf(
			'<p class="pageHeading">%s</p>
			<table cellpadding="4" cellspacing="4">
					<tr> <td> %s </td> <td> %s </td> </tr>
					<tr> <td> %s </td> <td> %s </td> </tr>
					<tr> <td> %s </td> <td> %s </td> </tr>
			</table>%s
			<hr/>',

			sysLanguage::get('METATAGS_HEADING_BESTSERLLERS'),

			sysLanguage::get('HEADER_META_TITLE'),
			$form_elems['t']->draw(),

			sysLanguage::get('HEADER_META_DESC'),
			$form_elems['d']->draw(),

			sysLanguage::get('HEADER_META_KEYWORD'),
			$form_elems['k']->draw(),

			$form_elems['i']
		);

		EventManager::notify('MetaTagsAdminEditAddTabContents', &$layout);
		$layout .= '</div>';

		if($lInfo['id'] === sysLanguage::getId()){
			$lang_tab_contents =  $layout . $lang_tab_contents;
		} else {
			$lang_tab_contents .=  $layout;
		}
		$layout = '';

    }
	$layout = $lang_tab_contents;


	//form

	$saveButton = htmlBase::newElement('button')->usePreset('save')->setType('submit');
	$cancelButton = htmlBase::newElement('button')->usePreset('cancel')
	->setHref(itw_app_link(tep_get_all_get_params(array('action')), null, 'default'));

	$buttonContainer = new htmlElement('div');
	$buttonContainer->append($saveButton)->append($cancelButton)->css(array(
		'float' => 'right',
		'width' => 'auto'
	))->addClass('ui-widget');

	$pageForm = htmlBase::newElement('form')
	->attr('name', 'metatags')
	->attr('action', itw_app_link(tep_get_all_get_params(array('action')) . 'action=save'))
	->attr('method', 'post')
	->html($layout . $buttonContainer->draw());


	echo $pageForm->draw();
    echo '</div>';
?>
