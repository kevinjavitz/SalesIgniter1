<?php
	$ListingTable = htmlBase::newElement('table')
	->setCellPadding(3)
	->setCellSpacing(0)
	->addClass('ui-widget ui-widget-content')
	->css(array(
		'width' => '100%'
	));
	
	$ListingTable->addHeaderRow(array(
		'addCls' => 'ui-widget-header',
		'columns' => array(
			array('align' => 'left', 'css' => array('color' => '#FFFFFF'), 'text' => sysLanguage::get('TABLE_HEADING_FILENAME')),
			array('css' => array('color' => '#FFFFFF'), 'text' => sysLanguage::get('TABLE_HEADING_ACTION'))
		)
	));
	
	foreach($Qdownloads as $dInfo){
		$downloadButton = htmlBase::newElement('button')
		->setText(sysLanguage::get('TEXT_BUTTON_DOWNLOAD'))
		->setHref(itw_app_link('appExt=downloadProducts&pID=' . $dInfo['products_id'] . '&dID=' . $dInfo['download_id'], 'downloads', 'get'))
		->css(array(
			'font-size' => '.8em'
		))
		->draw();

		$ext = substr($dInfo['file_name'], strpos($dInfo['file_name'], '.'), strlen($dInfo['file_name']));
		$filename = 'stream_file_' . $dInfo['download_id'] . $ext;
					
		$ListingTable->addBodyRow(array(
			'columns' => array(
				array('text' => (!empty($sInfo['display_name']) ? $sInfo['display_name'] : $filename)),
				array('align' => 'center', 'text' => $downloadButton)
			)
		));
	}
	
	$pageTitle = sprintf(sysLanguage::get('HEADING_TITLE_PRODUCT_DOWNLOADS'), $QproductsName[0]['products_name']);
	$pageContents = $ListingTable->draw();
	
	$pageButtons = htmlBase::newElement('button')
	->usePreset('back')
	->setHref(itw_app_link('products_id=' . (int) $_GET['pID'], 'product', 'info', 'SSL'))
	->draw();
	
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
