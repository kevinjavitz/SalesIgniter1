<?php

	if(isset($_GET['url']) && !empty($_GET['url'])){
		$Admin = Doctrine_Core::getTable('Admin')->findOneByAdminId((int)Session::get('login_id'));
		$favorites_links = explode(';', $Admin->favorites_links);
		$favorites_names = explode(';', $Admin->favorites_names);
		$removeVal = array_search($_GET['url'], $favorites_links);
		$removeValName = array_search($_GET['url'], $favorites_names);
		unset($favorites_links[$removeVal]);
		unset($favorites_names[$removeValName]);
		$Admin->favorites_links = implode(';', $favorites_links);
		$Admin->favorites_names = implode(';', $favorites_names);
		$Admin->save();
	}
	EventManager::attachActionResponse(itw_app_link(null,'index','default'), 'redirect');
?>