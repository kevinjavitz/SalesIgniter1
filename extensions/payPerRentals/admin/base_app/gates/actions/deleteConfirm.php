<?php
	Doctrine_Query::create()
	->delete('PayPerRentalGates')
	->where('gates_id = ?', (int)$_GET['gID'])
	->execute();
	
	EventManager::attachActionResponse(itw_app_link('appExt=payPerRentals', 'gates', 'default'), 'redirect');
?>