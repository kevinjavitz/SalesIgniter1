<?php
$pageTitle = 'Select Closest Store';

$input = htmlBase::newElement('input')
	->setName('postcode');

$addressHolder = htmlBase::newElement('table')
	->setId('addressHolder')
	->setCellPadding(2)
	->setCellSpacing(0);

$mapsHolder = htmlBase::newElement('div')
	->setId('mapsHolder')
	->html('<div id="map_canvas" style="width: 500px; height: 300px"></div>');

$pageContents = '<script src="http://maps.googleapis.com/maps/api/js?sensor=false" type="text/javascript"></script>' .
	'<p>Enter Your Address:' .
	$input->draw() .
	htmlBase::newElement('button')->setId('findStores')->setText('Find Closest')->draw() .
	'<br>( Format: Street Address, Zipcode )</p>' .
	'<table width="100%">' .
	'<tbody>' .
	'<tr>' .
	'<td valign="top">' . $addressHolder->draw() . '</td>' .
	'<td width="500" valign="top">' . $mapsHolder->draw() . '</td>' .
	'</tr>' .
	'</tbody>' .
	'</table>';

$pageButtons = htmlBase::newElement('button')->setId('setStore')->setText('Reserve Product At Store')->hide()->draw();

$pageContent->set('pageTitle', $pageTitle);
$pageContent->set('pageContent', $pageContents);
$pageContent->set('pageButtons', $pageButtons);
