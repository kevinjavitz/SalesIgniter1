<?php
	$setName = $_POST['set_name'];
	/*if(isset($_POST['selected_fav']) && $_POST['selected_fav'] != '0'){
		$AdminFavorites = Doctrine_Core::getTable('AdminFavorites')->find((int)$_POST['selected_fav']);
	}else{
		$AdminFavorites = new AdminFavorites();
		$AdminFavorites->admin_favs_name = $setName;
	}          */
    $AdminFavorites = new AdminFavorites();
    $AdminFavorites->admin_favs_name = $setName;
	$Admin = Doctrine_Core::getTable('Admin')->findOneByAdminId((int)Session::get('login_id'));
	$AdminFavorites->favorites_links = $Admin->favorites_links;
	$AdminFavorites->favorites_names = $Admin->favorites_names;
	$AdminFavorites->save();
	$json = array(
		'success' => true
	);

	EventManager::attachActionResponse($json, 'json');
?>