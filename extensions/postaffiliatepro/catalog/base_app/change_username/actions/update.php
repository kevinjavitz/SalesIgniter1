<?php
$QHasUsername = Doctrine_Query::create()
->from('UsernamesToIds')
->where('username = ?', $_POST['username'])
->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

if(count($QHasUsername) <= 0){
	$QusernamesToids = Doctrine_Core::getTable('UsernamesToIds')->findOneByCustomersEmailAddress($userAccount->getEmailAddress());
	if($QusernamesToids){
		$QusernamesToids->username = $_POST['username'];
		$QusernamesToids->save();
	}else{
		$messageStack->addSession('pageStack','You are not an affiliate');
	}
}else{
	$messageStack->addSession('pageStack','Username already exists');
}
EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action')), null, 'default'), 'redirect');
?>