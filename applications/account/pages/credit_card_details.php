<?php
/*
  $Id: address_book_details.php,v 1.10 2003/06/09 22:49:56 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

	if (!isset($process)) $process = false;

	$ccTable = htmlBase::newElement('table')
	->setCellPadding(3)
	->setCellSpacing(0);
	
	$ccTable->addBodyRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('CREDIT_CARD_NUMBER')),
			array('text' => tep_draw_input_field('cc_number') . '&nbsp;' .$card_number. (tep_not_null(sysLanguage::get('CREDIT_CARD_NUMBER_TEXT')) ? '<span class="inputRequirement">' . sysLanguage::get('CREDIT_CARD_NUMBER_TEXT') . '</span>': ''))
		)
	));
	
	$ccTable->addBodyRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('CREDIT_CARD_EXPIRY')),
			array('text' => tep_draw_pull_down_menu('cc_expires_month', $expires_month,$arr_date[0]) . '&nbsp;' . tep_draw_pull_down_menu('cc_expires_year', $expires_year,$year) . '&nbsp;' . (tep_not_null(sysLanguage::get('CREDIT_CARD_EXPIRY_TEXT')) ? '<span class="inputRequirement">' . sysLanguage::get('CREDIT_CARD_EXPIRY_TEXT') . '</span>': ''))
		)
	));
	
	$ccTable->addBodyRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('CREDIT_CARD_CVV')),
			array('text' => tep_draw_input_field('cc_cvv', $cardCvvNumber, 'size="5" maxlength="4"'))
		)
	));
	$pageTitle = sysLanguage::get('MODIFY_CC_INFO_TITLE');
	
	$pageContents = '<div style="text-align:right">' . 
		sysLanguage::get('FORM_REQUIRED_INFORMATION') . 
	'</div>';
	
	$pageContents .= $ccTable->draw();
	
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
