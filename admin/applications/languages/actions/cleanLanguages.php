<?php
	sysLanguage::cleanAbandonedLanguages();

    EventManager::attachActionResponse(itw_app_link(null, 'languages', 'default'), 'redirect');
?>