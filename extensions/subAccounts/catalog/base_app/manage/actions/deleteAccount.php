<?php

	Doctrine_Core::getTable('Customers')->find($_GET['cID'])->delete();
	$messageStack->addSession('pageStack','Subaccount deleted','error');
	EventManager::attachActionResponse(itw_app_link('appExt=subAccounts', 'manage', 'default'), 'redirect');
?>