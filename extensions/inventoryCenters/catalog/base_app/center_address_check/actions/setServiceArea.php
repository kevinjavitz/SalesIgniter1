<?php
	$json = array(
		'success' => true,
		'inService' => false,
		'msgStack' => addslashes($messageStack->parseTemplate('pageStack', 'You must select a center.', 'warning'))
	);
	
	if (isset($_POST['cID']) && $_POST['cID'] > 0){
		Session::set('addressCheck', array(
			'customerSelected' => $_POST['cID']
		));

		if (isset($navigation->snapshot) && sizeof($navigation->snapshot) > 0){
			$snap = $navigation->snapshot;
			$redirectUrl = tep_href_link($snap['page'], $snap['get'], $snap['mode']);
		}else{
			$redirectUrl = itw_app_link(null, 'index', 'default');
		}
		$messageStack->addSession('pageStack', addslashes('Your selected center has been set'), 'success');
		if (isset($_GET['rType']) && $_GET['rType'] == 'ajax'){
			$json = array(
				'success' => true,
				'inService' => true,
				'redirectUrl' => $redirectUrl
			);
		}
	}

	if ($json['inService'] === false){
		Session::set('addressCheck', false);
	}

	if (isset($_GET['rType']) && $_GET['rType'] == 'ajax'){
		EventManager::attachActionResponse($json, 'json');
	}else{
		$messageStack->addSession('pageStack', 'You must select a center.', 'error');
		EventManager::attachActionResponse(itw_app_link('appExt=inventoryCenters', 'center_address_check', 'default'), 'redirect');
	}
?>