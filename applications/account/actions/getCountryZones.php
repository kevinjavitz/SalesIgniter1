<?php
	if(isset($_GET['cID']) && !empty($_GET['cID'])){
		$country = $_GET['cID'];
	}else{
		$country = sysConfig::get('ONEPAGE_DEFAULT_COUNTRY');
	}

	$html = '';
	$Qcheck = Doctrine_Query::create()
	->select('zone_id, zone_code, zone_name')
	->from('Zones')
	->where('zone_country_id = ?', (int)$country)
	->orderBy('zone_name')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if ($Qcheck) {
		$htmlField = htmlBase::newElement('selectbox')
					->setName($_GET['state_type'])
					->attr('id',$_GET['state_type']);
		foreach($Qcheck as $zInfo){
			$htmlField->addOption($zInfo['zone_name'], $zInfo['zone_name']);
		}
		if (isset($_GET['edit']) && $_GET['edit'] > -1) {
			$addressEntry = $addressBook->getAddress($_GET['edit']);
			$htmlField->selectOptionByValue(tep_get_zone_name($addressEntry['entry_country_id'], $addressEntry['entry_zone_id'], $addressEntry['entry_state']));
		}
	} else {
		$htmlField = htmlBase::newElement('input')
					->setName($_GET['state_type'])
					->attr('id',$_GET['state_type']);
	}

	
	EventManager::attachActionResponse($htmlField->draw(), 'html');
?>