<?php
	$extension = basename($_GET['ext']);
	$extensionDir = DIR_FS_CATALOG . 'extensions/' . $extension . '/';

	$config = simplexml_load_file($extensionDir . 'data/base/configuration.xml', 'SimpleXMLElement', LIBXML_NOCDATA);
	$ConfigurationGroup = Doctrine_Core::getTable('ConfigurationGroup');

	$Group = Doctrine_Query::create()
	->select('configuration_group_id')
	->from('ConfigurationGroup')
	->where('configuration_group_title = ?', (string) $config->title)
	->fetchOne();
	if ($Group !== false){
		$groupID = $Group['configuration_group_id'];
		foreach((array) $config->Configuration as $configKey => $configSettings){
			$Qcheck = Doctrine_Query::create()
			->select('configuration_id')
			->from('Configuration')
			->where('configuration_key = ?', $configKey)
			->execute();
			if ($Qcheck->count() <= 0){
				$newConfig = new Configuration();
				$newConfig->configuration_key = (string) $configKey;
				$newConfig->configuration_title = (string) $configSettings->title;
				$newConfig->configuration_value = (string) $configSettings->value;
				$newConfig->configuration_description = (string) $configSettings->description;
				$newConfig->configuration_group_id = (int) $groupID;
				$newConfig->sort_order = (int) $configSettings->sort_order;

				if (isset($configSettings->use_function)){
					$newConfig->use_function = (string) $configSettings->use_function;
				}

				if (isset($configSettings->set_function)){
					$newConfig->set_function = (string) $configSettings->set_function;
				}
				$newConfig->save();
			}
			$Qcheck->free();
		}
	}
	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action'))), 'redirect');
?>