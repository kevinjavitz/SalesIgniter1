<?php
	if(isset($_GET['cID']) && !empty($_GET['cID'])){
		$country = $_GET['cID'];
	}else{
		$country = sysConfig::get('ONEPAGE_DEFAULT_COUNTRY');
	}

	$addressEntry = $addressBook->getAddress($_GET['edit']);
    if(isset($addressEntry['entry_state'])){
	    $state = htmlentities($addressEntry['entry_state'], ENT_IGNORE, 'utf-8');
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

		$htmlField->selectOptionByValue((isset($state)?$state:1));

	} else {
		$addressEntry = $addressBook->getAddress($_GET['edit']);
		$htmlField = htmlBase::newElement('input')
					->setName($_GET['state_type'])
					->attr('id',$_GET['state_type'])
					->setValue($addressEntry['entry_state']);
	}

	
	EventManager::attachActionResponse($htmlField->draw(), 'html');
?>