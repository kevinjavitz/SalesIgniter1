<?php
	if ($_POST['flag'] == 'true'){
		Session::set('cot_gv', true);
	}else{
		Session::remove('cot_gv');
	}
	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>