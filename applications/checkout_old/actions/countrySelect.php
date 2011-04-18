<?php
	$name = $_POST['fieldName'];
	if ($name == 'billing_state'){
		$key = 'billing';
	}else{
		$key = 'delivery';
	}
	$html = '';

	$addressBook =& $userAccount->plugins['addressBook'];
	$zones = $addressBook->getCountryZones($_POST['cID']);

	if (isset($_POST['curValue']) && !empty($_POST['curValue'])){
		$Qcheck = Doctrine_Query::create()
		->select('zone_name')
		->from('Zones')
		->where('zone_code = ?', $_POST['curValue'])
		->orWhere('zone_name = ?', $_POST['curValue'])
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Qcheck){
			$onePageCheckout->onePage[$key]['state'] = $Qcheck[0]['zone_name'];
			$onePageCheckout->onePage[$key]['zone_name'] = $Qcheck[0]['zone_name'];
		}
	}

	if (isset($onePageCheckout->onePage[$key])){
		if (isset($onePageCheckout->onePage[$key]['zone_name'])){
			$zoneName = $onePageCheckout->onePage[$key]['zone_name'];
		}else{
			$zoneName = $onePageCheckout->onePage[$key]['state'];
		}
	}else{
		$zoneName = '';
	}

	if ($zones !== false){
		$html = tep_draw_pull_down_menu($name, $zones, $zoneName, 'class="required" style="width:80%;float:left;"');
	}else{
		$html = tep_draw_input_field($name, $zoneName, 'class="required" style="width:80%;float:left;"');
	}
	
	EventManager::attachActionResponse($html, 'html');
?>