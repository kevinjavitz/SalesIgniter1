<?php
if (isset($_GET['zID'])){
	$heading = sysLanguage::get('TEXT_INFO_HEADING_EDIT');
}else{
	$heading = sysLanguage::get('TEXT_INFO_HEADING_NEW');
}

$infoBox = htmlBase::newElement('infobox');
$infoBox->setHeader('<b>' . $heading . '</b>');
$infoBox->setButtonBarLocation('top');

$saveButton = htmlBase::newElement('button')->addClass('saveButton')->usePreset('save');
$cancelButton = htmlBase::newElement('button')->addClass('cancelButton')->usePreset('cancel');

$infoBox->addButton($saveButton)->addButton($cancelButton);

$GoogleZones = Doctrine_Core::getTable('GoogleZones');
if (isset($_GET['zID'])){
	$GoogleZones = $GoogleZones->findOneByGoogleZonesId((int)$_GET['zID']);
}else{
	$GoogleZones = $GoogleZones->getRecord();
}

$name = '';
$scriptCommands = '';
$address = tep_get_zone_name(sysConfig::get('STORE_COUNTRY'), sysConfig::get('STORE_ZONE')) . ", " .
	tep_get_country_name(sysConfig::get('STORE_COUNTRY'));

if (isset($_GET['zID'])){
	$name = $GoogleZones->google_zones_name;

	if (!empty($GoogleZones->gmaps_polygon)){
		$address = '';
		$polygon = unserialize($GoogleZones->gmaps_polygon);
		$scriptCommands = '';
		for($i=0, $n=sizeof($polygon); $i<$n; $i++){
			if(!empty($polygon[$i]['lat']) || !empty($polygon[$i]['lng'])){
				$scriptCommands .= 'leftClick(poly, new GLatLng(' . $polygon[$i]['lat'] . ', ' . $polygon[$i]['lng'] . ', true));';
			}
		}
	}
}

$Settings = htmlBase::newElement('table')
	->setCellPadding(2)
	->setCellSpacing(0);

$Settings->addBodyRow(array(
	'columns' => array(
		array('text' => sysLanguage::get('TEXT_GOOGLE_ZONES_NAME')),
		array('text' => htmlBase::newElement('input')->setName('google_zones_name')->val($name))
	)
));

$Settings->addBodyRow(array(
	'columns' => array(
		array('text' => sysLanguage::get('TEXT_GOOGLE_ZONES_ADDRESS')),
		array('text' => htmlBase::newElement('textarea')->setName('google_zones_address')->val($address))
	)
));

$Settings->addBodyRow(array(
	'columns' => array(
		array('text' => sysLanguage::get('TEXT_GOOGLE_ZONES_MAP')),
		array('text' => '<div id="mapHolder"><div id="googleMap" style="width:650px;height:450px;"></div></div>')
	)
));

$script = '<script>var windowOnLoad = function (){ ' . $scriptCommands . ' recenterMap(); };</script>';

$infoBox->addContentRow($script . $Settings->draw());

EventManager::attachActionResponse($infoBox->draw(), 'html');
