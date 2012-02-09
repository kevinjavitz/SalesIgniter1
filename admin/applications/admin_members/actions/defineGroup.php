<?php
//echo '<pre>';print_r($_POST['applications']);itwExit();
	$Permissions = Doctrine_Core::getTable('AdminApplicationsPermissions');
	$groupId = (int) $_GET['gID'];
	
	$Reset = $Permissions->findAll();
	foreach($Reset as $rInfo){
		$perms = explode(',', $rInfo->admin_groups);
		if (in_array($groupId, $perms)){
			foreach($perms as $idx => $id){
				if ($id == $groupId){
					unset($perms[$idx]);
					break;
				}
			}
			$rInfo->admin_groups = implode(',', $perms);
			$rInfo->save();
		}
	}
	
	if (isset($_POST['applications'])){
		foreach($_POST['applications'] as $appName => $Pages){
			if ($appName == 'ext') continue;
		
			foreach($Pages as $pageName){
				$Permission = $Permissions->findOneByApplicationAndPage($appName, $pageName);
				if (!$Permission){
					$Permission = new AdminApplicationsPermissions();
					$Permission->application = $appName;
					$Permission->page = $pageName;
				}
			
				$currentGroups = explode(',', $Permission->admin_groups);
				if (!in_array($groupId, $currentGroups)){
					$currentGroups[] = $groupId;
				}
				$Permission->admin_groups = implode(',', $currentGroups);
				$Permission->save();
			}
		}
	}
	
	if (isset($_POST['applications']['ext'])){
		foreach($_POST['applications']['ext'] as $extName => $Applications){
			foreach($Applications as $appName => $Pages){
				foreach($Pages as $pageName){
					$Permission = $Permissions->findOneByApplicationAndPageAndExtension($appName, $pageName, $extName);
					if (!$Permission){
						$Permission = new AdminApplicationsPermissions();
						$Permission->application = $appName;
						$Permission->page = $pageName;
						$Permission->extension = $extName;
					}
			
					$currentGroups = explode(',', $Permission->admin_groups);
					if (!in_array($groupId, $currentGroups)){
						$currentGroups[] = $groupId;
					}
					$Permission->admin_groups = implode(',', $currentGroups);
					$Permission->save();
				}
			}
		}
	}
	
	EventManager::attachActionResponse(itw_app_link(null, 'admin_members', 'groups'), 'redirect');
?>