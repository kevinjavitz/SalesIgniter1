<?php
	$userAccount->requestReactivation();
	
	EventManager::attachActionResponse(itw_app_link(null, 'account', 'default', 'SSL'), 'redirect');
?>