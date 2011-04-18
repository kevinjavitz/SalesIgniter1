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
	
	foreach($Qstreams as $sInfo){
		$streamButton = htmlBase::newElement('button')
		->setText(sysLanguage::get('TEXT_BUTTON_VIEW_STREAM'))
		->setHref(itw_app_link('appExt=streamProducts&pID=' . $sInfo['products_id'] . '&sID=' . $sInfo['stream_id'], 'streams', 'view'))
		->css(array(
			'font-size' => '.8em'
		))
		->draw();

		$ext = substr($sInfo['file_name'], strpos($sInfo['file_name'], '.'), strlen($sInfo['file_name']));
		$filename = 'stream_file_' . $sInfo['stream_id'] . $ext;
					
		$ListingTable->addBodyRow(array(
			'columns' => array(
				array('text' => (!empty($sInfo['display_name']) ? $sInfo['display_name'] : $filename)),
				array('align' => 'center', 'text' => $streamButton)
			)
		));
	}
	
	$pageTitle = sprintf(sysLanguage::get('HEADING_TITLE_PRODUCT_STREAMS'), $QproductsName[0]['products_name']);
	$pageContents = $ListingTable->draw();
	
	$pageButtons = htmlBase::newElement('button')
	->usePreset('back')
	->setHref(itw_app_link('products_id=' . (int) $_GET['pID'], 'product', 'info', 'SSL'))
	->draw();
	
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
