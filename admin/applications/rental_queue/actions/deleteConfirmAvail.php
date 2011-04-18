<?php
	Doctrine_Query::create()
	->delete('RentalAvailability')
	->where('rental_availability_id = ?', (int)$_GET['arID'])
	->execute();

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action'))), 'redirect');
?>