<?php
	$polygon = serialize($_POST['poly_point']);
	$zoneName = $_POST['google_zones_name'];

	$GoogleZones = Doctrine_Core::getTable('GoogleZones');
	if (isset($_GET['zID'])){
		$GoogleZone = $GoogleZones->find((int)$_GET['zID']);
	}else{
		$GoogleZone = new GoogleZones();
	}
	
	$GoogleZone->google_zones_name = $zoneName;
	$GoogleZone->gmaps_polygon = $polygon;
	$GoogleZone->save();
	
	EventManager::attachActionResponse(itw_app_link('zID=' . $GoogleZone->google_zones_id, 'zones', 'default'), 'redirect');
?>