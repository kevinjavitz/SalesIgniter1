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
	
	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>