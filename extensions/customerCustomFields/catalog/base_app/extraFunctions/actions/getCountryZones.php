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
		$htmlField = htmlBase::newElement('selectbox')
					->setName($_GET['state_name'])
					->addClass('stateExtraCustomer')
					->attr('id',$_GET['state_id']);
		foreach($Qcheck as $zInfo){
			$htmlField->addOption($zInfo['zone_name'], $zInfo['zone_name']);
		}
		$htmlField->selectOptionByValue($_GET['state_val']);
	} else {
		$htmlField = htmlBase::newElement('input')
					->setName($_GET['state_name'])
					->attr('id',$_GET['state_id']);
		$htmlField->setValue($_GET['state_val']);
	}
	
	EventManager::attachActionResponse($htmlField->draw(), 'html');
?>