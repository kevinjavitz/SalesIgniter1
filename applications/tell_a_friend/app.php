<?php
	$appContent = $App->getAppContentFile();
	$breadcrumb->add(sysLanguage::get('NAVBAR_TITLE'), itw_app_link('products_id=' . $_GET['products_id'], 'tell_a_friend', 'default'));
?>