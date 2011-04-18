<?php
	Doctrine_Query::create()
	->delete('RentalStatus')
	->where('rental_status_id = ?', (int)$_GET['rID'])
	->execute();
	
	EventManager::attachActionResponse(itw_app_link('appExt=payPerRentals', 'rental_status', 'default'), 'redirect');
?>