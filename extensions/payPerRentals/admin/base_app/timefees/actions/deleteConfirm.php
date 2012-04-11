<?php
	Doctrine_Query::create()
	->delete('PayPerRentalTimeFees')
	->where('timefees_id = ?', (int)$_GET['tfID'])
	->execute();
	
	EventManager::attachActionResponse(itw_app_link('appExt=payPerRentals', 'timefees', 'default'), 'redirect');
?>