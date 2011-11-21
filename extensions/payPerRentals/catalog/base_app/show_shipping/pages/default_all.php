<?php

$tableListing = htmlBase::newElement('table')
	->setCellPadding(3)
	->setCellSpacing(0)
	->attr('width', '100%');

$shippingHeader = array(
	array('addCls' => 'ui-widget-header', 'text' => sysLanguage::get('TABLE_HEADING_SHIPPING_TITLE'), 'align' => 'center'),
	array('addCls' => 'ui-widget-header', 'text' => sysLanguage::get('TABLE_HEADING_SHIPPING_DESCRIPTION'), 'align' => 'center'),
);



$tableListing->addBodyRow(array(
		'addCls' => 'ui-widget-header',
		'columns' => $shippingHeader
	));




$ModuleShipping = OrderShippingModules::getModule('zonereservation');
if($ModuleShipping){
	$quotes = $ModuleShipping->quote();

	foreach($quotes['methods'] as $quote){
		$shInfo = ReservationUtilities::getShippingDetails($quote['id']);
		$shippingBodyRow = array(
			array(
				'text' => $shInfo['title'],
				'attr' => array('align' => 'center', 'valign' => 'top')
			),

			array(
				'text' => $shInfo['details'],
				'attr' => array('align' => 'right', 'valign' => 'top')
			)
		);



		$tableListing->addBodyRow(array(
				'columns' => $shippingBodyRow
		));

	}


	$pageTitle = stripslashes('Shipping Information');
	$pageContents = stripslashes($tableListing->draw());

	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$Template->setPopupMode(true);
}
?>