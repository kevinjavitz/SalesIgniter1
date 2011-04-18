<?php
	$GeoZones = Doctrine_Core::getTable('GeoZones');
	if (isset($_GET['zID'])){
		$GeoZone = $GeoZones->find((int)$_GET['zID']);
	}else{
		$GeoZone = $GeoZones->create();
	}
	
	$GeoZone->geo_zone_name = $_POST['geo_zone_name'];
	$GeoZone->geo_zone_description = $_POST['geo_zone_description'];
	
	$Zones =& $GeoZone->ZonesToGeoZones;
	$Zones->delete();
	if (isset($_POST['zone_country_id'])){
		foreach($_POST['zone_country_id'] as $idx => $countryId){
			$zoneId = $_POST['zone_id'][$idx];
			
			$Zones[$idx]->zone_country_id = (int) $countryId;
			$Zones[$idx]->zone_id = (int) $zoneId;
		}
	}
	$GeoZone->save();
	
	EventManager::attachActionResponse(array(
		'success' => true,
		'zID'     => $GeoZone->geo_zone_id
	), 'json');
?>