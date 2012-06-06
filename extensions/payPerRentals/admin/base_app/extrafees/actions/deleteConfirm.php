<?php
$multiStore = $appExtension->getExtension('multiStore');
if ($multiStore !== false && $multiStore->isEnabled() === true){
		Doctrine_Query::create()
		->delete('StoresExtraFees')
		->where('timefees_id = ?', (int)$_GET['tfID'])
		->execute();
}else{
	Doctrine_Query::create()
	->delete('PayPerRentalExtraFees')
	->where('timefees_id = ?', (int)$_GET['tfID'])
	->execute();
}
	
	EventManager::attachActionResponse(itw_app_link('appExt=payPerRentals', 'extrafees', 'default'), 'redirect');
?>