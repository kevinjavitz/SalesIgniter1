<?php
	$country = $_GET['cID'];
	if (isset($_GET['zName'])){
		$state = $_GET['zName'];
	}
	$html = '';
	$Qcountry = Doctrine_Query::create()
		->select('countries_id')
		->from('Countries')
		->where('countries_iso_code_2 = ?', $country)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	$html = '';
	$Qcheck = Doctrine_Query::create()
		->select('zone_id, zone_code, zone_name')
		->from('Zones')
		->where('zone_country_id = ?', $Qcountry[0]['countries_id'])
		->orderBy('zone_name')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	$htmlField = htmlBase::newElement('selectbox')->setName('zone_code');
	//$htmlField->addOption('', 'Please Select');
	$mystate = '';
	foreach($Qcheck as $zInfo){
		$htmlField->addOption($zInfo['zone_code'], $zInfo['zone_name']);
		if (isset($state) && $zInfo['zone_name'] == $state){
			$mystate = $zInfo['zone_code'];
		}
	}
	$htmlField->selectOptionByValue($mystate);

	EventManager::attachActionResponse($htmlField->draw(), 'html');
?>