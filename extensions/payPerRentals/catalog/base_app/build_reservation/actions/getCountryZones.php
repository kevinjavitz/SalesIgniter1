<?php
	$country = $_GET['cID'];
	if(isset($_GET['zName'])){
		$state = $_GET['zName'];
	}
	$html = '';
	$Qcheck = Doctrine_Query::create()
	->select('zone_id, zone_code, zone_name')
	->from('Zones')
	->where('zone_country_id = ?', (int)$country)
	->orderBy('zone_name')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if ($Qcheck) {
		$htmlField = htmlBase::newElement('selectbox')->setName('state');
		$htmlField->addOption('', sysLanguage::get('TEXT_PLEASE_SELECT'));
		foreach($Qcheck as $zInfo){
			$htmlField->addOption($zInfo['zone_name'], $zInfo['zone_name']);
		}
		if(isset($state)){
			$htmlField->selectOptionByValue($state);
		}
	} else {
		$htmlField = htmlBase::newElement('input')->setName('state');
		if(isset($state)){
			$htmlField->setValue($state);
		}
	}
	
	EventManager::attachActionResponse($htmlField->draw(), 'html');
?>