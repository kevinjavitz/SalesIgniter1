<?php
	if (isset($_GET['gID'])){
		$oldAction = 'edit_group';
	}else{
		$oldAction = 'new_group';
	}
	$admin_groups_name = ucwords(strtolower($_POST['admin_groups_name']));
	$error = false;

	if (empty($_POST['admin_groups_name']) || strlen($_POST['admin_groups_name']) <= 5){
		$messageStack->addSession('pageStack', sysLanguage::get('TEXT_INFO_GROUPS_NAME_FALSE'), 'error');
		$error = true;
	}

	if ($error === false){
		$searchGroups = Doctrine_Query::create()
		->select('admin_groups_name')
		->from('AdminGroups')
		->where('admin_groups_name like ?', '%' . str_replace(' ', '%', $admin_groups_name) . '%');
		if (isset($_GET['gID'])){
			$searchGroups->andWhere('admin_groups_id != ?', (int)$_GET['gID']);
		}
		$searchGroups = $searchGroups->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if (count($searchGroups) > 0){
			$messageStack->addSession('pageStack', sysLanguage::get('TEXT_INFO_GROUPS_NAME_USED'), 'error');
			$error = true;
		}
	}

	if ($error === false){
		$AdminGroups = Doctrine_Core::getTable('AdminGroups');
		if (isset($_GET['gID'])){
			$group = $AdminGroups->findByAdminGroupsId((int)$_GET['gID']);
		}else{
			$group = $AdminGroups->create();
		}
		$group->admin_groups_name = $admin_groups_name;
		$group->customer_login_allowed = (isset($_POST['customer_login']) ? '1' : '0');
		$group->save();

		$link = itw_app_link(tep_get_all_get_params(array('action', 'gID')) . 'gID=' . $group->admin_groups_id);
	}else{
		$link = itw_app_link(tep_get_all_get_params(array('action')) . 'action=' . $oldAction);
	}
	EventManager::attachActionResponse($link, 'redirect');
?>