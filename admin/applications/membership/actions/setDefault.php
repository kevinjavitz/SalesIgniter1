<?php
	Doctrine_Query::create()->update('Membership')->set('default_plan', '?', '0')->execute();
			
	Doctrine_Query::create()
	->update('Membership')
	->set('default_plan', '?', '1')
	->where('plan_id = ?', (int)$_GET['pID'])
	->execute();
	
	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action'))), 'redirect');
?>