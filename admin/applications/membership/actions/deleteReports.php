<?php
	$ids = explode(',', $_GET['report']);
	for($i=0; $i<sizeof($ids); $i++){
		Doctrine_Query::create()
		->delete('MembershipBillingReport')
		->where('billing_report_id = ?', $ids[$i])
		->execute();
	}
	
	EventManager::attachActionResponse(itw_app_link(null, 'membership', 'billing_report'), 'redirect');
?>