<?php
	Doctrine_Query::create()
	->delete('PayPerRentalTypes')
	->where('pay_per_rental_types_id = ?', (int)$_GET['rID'])
	->execute();
	
	EventManager::attachActionResponse(itw_app_link('appExt=payPerRentals', 'rental_types', 'default'), 'redirect');
?>