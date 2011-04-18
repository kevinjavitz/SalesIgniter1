<?php
	Doctrine_Query::create()
	->delete('ProductDesignerPredesigns')
	->where('predesign_id = ?', $_POST['predesign_id'])
	->execute();
	
	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action')), null, 'default'), 'redirect');
?>