<?php
	if ($_GET['flag'] == 'N' || $_GET['flag'] == 'Y'){
		if (isset($_GET['cID'])){
			Doctrine_Query::create()
			->update('Coupons')
			->set('coupon_active', '?', $_GET['flag'])
			->where('coupon_id = ?', (int) $_GET['cID'])
			->execute();
		}
	}

	EventManager::attachActionResponse(itw_app_link(null, 'coupons', 'default'), 'redirect');
?>