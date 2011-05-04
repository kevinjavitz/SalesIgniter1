<?php
	$country = $_GET['cID'];
	$html = '';
	$Qcheck = Doctrine_Query::create()
	->select('zone_id, zone_code, zone_name')
	->from('Zones')
	->where('zone_country_id = ?', (int)$country)
	->orderBy('zone_name')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if ($Qcheck) {
		$htmlField = htmlBase::newElement('selectbox')->setName('state');
		$htmlField->addOption('', 'Please Select');
		foreach($Qcheck as $zInfo){
			$htmlField->addOption($zInfo['zone_name'], $zInfo['zone_name']);
		}
        $htmlField->selectOptionByValue($_GET['zName']);
	} else {
		$htmlField = htmlBase::newElement('input')->setName('state');
	}

	EventManager::attachActionResponse($htmlField->draw(), 'html');
?>