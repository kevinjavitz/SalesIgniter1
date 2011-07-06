<?php
	
	if (Session::exists('childrenAccount') && $App->getPageName() !='changePassword'){
		tep_redirect(itw_app_link(null, 'account', 'default', 'SSL'));
	}
	
	$appContent = $App->getAppContentFile();
?>