<?php
    $error = false;
	foreach($_POST['product'] as $id => $pInfo){
		if (isset($pInfo['reservation']['dates']) && empty($pInfo['reservation']['dates'])){
				$Editor->addErrorMessage('Please check dates');
				$error = true;
		}else
		if (isset($pInfo['reservation']['events']) && $pInfo['reservation']['events'] == '0'){
			$Editor->addErrorMessage('Please check events');
			$error = true;
		}
		if (isset($pInfo['reservation']['gate']) && $pInfo['reservation']['gate'] == '0'){
			$Editor->addErrorMessage('Please check gate');
			$error = true;
		}
		//$Product = $this->getContents($id);
	}
 	if($error){
		 if (isset($_GET['oID'])){
			EventManager::attachActionResponse(itw_app_link('appExt=orderCreator&error=true&oID=' . $_GET['oID'], 'default', 'new'), 'redirect');
		}else{
			EventManager::attachActionResponse(itw_app_link('appExt=orderCreator&error=true', 'default', 'new'), 'redirect');
		}
	}
?>