<?php
	$checkedCats = array();
	if ($Banner['banners_id'] > 0){
		$QcurGroups = Doctrine_Query::create()
		->select('banner_group_id')
		->from('BannerManagerBannersToGroups')
		->where('banners_id = ?', $Banner['banners_id'])
		->execute();
		if ($QcurGroups->count() > 0){
			foreach($QcurGroups->toArray() as $group){
				$checkedCats[] = $group['banner_group_id'];
			}
			unset($group);
		}
		$QcurGroups->free();
		unset($QcurGroups);
	}
	echo tep_get_group_tree_list($checkedCats);
	unset($checkedCats);
?>