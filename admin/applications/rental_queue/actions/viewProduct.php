<?php
	Session::set('categories_cancel_link', itw_app_link('cID=' . (int)$_GET['cID'], null, 'details'));
	Session::set('categories_save_redirect', itw_app_link('cID=' . (int)$_GET['cID'], null, 'details'));

	EventManager::attachActionResponse(itw_app_link('pID=' . $_GET['pID'], 'products', 'new_product') . '#page-4', 'redirect');
?>