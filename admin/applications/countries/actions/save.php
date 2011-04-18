<?php
	$Countries = Doctrine_Core::getTable('Countries');
	if (isset($_GET['cID'])){
		$Country = $Countries->find((int)$_GET['cID']);
	}else{
		$Country = $Countries->create();
	}
	
	$Country->countries_name = $_POST['countries_name'];
	$Country->countries_iso_code_2 = $_POST['countries_iso_code_2'];
	$Country->countries_iso_code_3 = $_POST['countries_iso_code_3'];
	$Country->address_format_id = $_POST['address_format_id'];

	$Country->save();

	$Zones =& $Country->Zones;
	if (isset($_POST['zone_id'])){
		foreach($Zones as $zInfo){
			if (!in_array($zInfo['zone_id'], $_POST['zone_id'])){
				$zInfo->delete();
			}
		}
	}else{
		$Zones->delete();
	}

	if (isset($_POST['new_zone_code'])){
		foreach($_POST['new_zone_code'] as $idx => $code){
			$Zone = new Zones();
			$Zone->zone_code = $code;
			$Zone->zone_name = $_POST['new_zone_name'][$idx];
				
			$Zones->add($Zone);
		}
	}
	
	$Country->save();
	
	EventManager::attachActionResponse(array(
		'success' => true,
		'cID'     => $Country->countries_id
	), 'json');
?>