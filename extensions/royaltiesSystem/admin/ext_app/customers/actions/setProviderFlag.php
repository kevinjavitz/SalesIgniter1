<?php
	Doctrine_Query::create()
	->update('Customers')
	->set('is_content_provider', '?', (int)$_GET['flag'])
	->where('customers_id = ?', (int)$_GET['cID'])
	->execute();
	
	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('app', 'appPage', 'action', 'flag')), 'customers', 'default'), 'redirect');
?>