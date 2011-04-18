<?php
	while (list($key, $value) = each($_POST['configuration'])) {
		if (is_array($value)){
			$value = implode(',', $value);
		}

		$Qupdate = Doctrine_Query::create()
		->update('Configuration')
		->set('configuration_value', '?', $value)
		->where('configuration_key = ?', $key)
		->execute();
	}
	
	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action'))), 'redirect');
?>