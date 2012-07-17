<?php
	$countryName = $_GET['country'];
	$aType = $_GET['addressType'];

	$QCountry = Doctrine_Query::create()
	->select('c.countries_id, z.zone_name, z.zone_code')
	->from('Countries c')
	->leftJoin('c.Zones z')
	->where('countries_name = ?', $countryName)
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if (sizeof($QCountry[0]['Zones']) > 0){
		$html = htmlBase::newElement('selectbox');
		if(isset($_GET['state'])){
			$html->selectOptionByValue($_GET['state']);
		}
		foreach($QCountry[0]['Zones'] as $zInfo){
            $iso = array();
            $iso['iso_code'] = $zInfo['zone_code'];
            $html->addOption($zInfo['zone_name'], $zInfo['zone_name'], false, $iso);
		}
	}else{
		$html = htmlBase::newElement('input');
	}
	
	$html->setName('address[' . $aType . '][entry_state]')
    ->addClass('state_'.$aType)
	->css(array('width' => '150px'));
	
	EventManager::attachActionResponse($html->draw(), 'html');
?>