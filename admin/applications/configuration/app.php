<?php
	$appContent = $App->getAppContentFile();
	$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.tabs.js');

	$gID = (isset($_GET['gID'])) ? $_GET['gID'] : ($App->getAppPage() == 'product_listing' || $App->getAppPage() == 'product_sort_listing' ? 8 : 1);
	
	if (isset($_GET['cID'])){
		$App->setInfoBoxId($_GET['cID']);
	}

	$Qgroup = Doctrine_Query::create()
	->select('configuration_group_title')
	->from('ConfigurationGroup')
	->where('configuration_group_id = ?', (int)$gID)
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
?>