<?php
if ($_GET['force'] == 1){
	Doctrine_Query::create()
		->update('Languages')
		->set('forced_default', '?', '0')
		->execute();
}

$Language = Doctrine_Core::getTable('Languages')->find((int) $_GET['lID']);
$Language->forced_default = $_GET['force'];
$Language->save();

EventManager::attachActionResponse(itw_app_link(null, 'languages', 'default'), 'redirect');
?>