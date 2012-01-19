<?php
	if (isset($_POST['url']) && !empty($_POST['url']) && isset($_POST['link_name']) && !empty($_POST['link_name'])){
		$Admin = Doctrine_Core::getTable('Admin')->findOneByAdminId((int)Session::get('login_id'));
		if (sysConfig::get('ENABLE_SSL') == 'true') {

			$url = str_replace( sysConfig::get('HTTPS_SERVER') . sysConfig::get('DIR_WS_ADMIN'),'', $_POST['url']);
			$url = preg_replace('/([?&])'.'osCAdminID'.'=[^&]+(&|$)/','$1',$url);
			$Admin->favorites_links .= ';' .$url;
		}else{
			$url = str_replace( sysConfig::get('HTTP_SERVER') . sysConfig::get('DIR_WS_ADMIN'),'', $_POST['url']);
			$url = preg_replace('/([?&])'.'osCAdminID'.'=[^&]+(&|$)/','$1',$url);
			$Admin->favorites_links .= ';' . $url;
		}
		$Admin->favorites_names .= ';' . $_POST['link_name'];
		$Admin->save();		
	}

	$json = array(
			'success' => true
	);

	EventManager::attachActionResponse($json, 'json');
?>