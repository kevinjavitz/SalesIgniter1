<?php
$Qadmin = Doctrine_Query::create()
	->from('Admin')
	->where('admin_override_password = ?', $_POST['password'])
	->execute();
if ($Qadmin && $Qadmin->count() > 0){
	$status = true;
	Session::set('OverrideApproved', 'true');
}
else {
	$status = false;
	Session::set('OverrideApproved', 'false');
}

EventManager::attachActionResponse(array(
		'success' => true,
		'status' => $status
	), 'json');
