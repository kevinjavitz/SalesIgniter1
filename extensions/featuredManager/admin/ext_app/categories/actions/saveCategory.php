<?php
	$CategoriesToGroups =& $Category->FeaturedManagerCategoriesToGroups;
	$CategoriesToGroups->delete();
	if (isset($_POST['groups'])){
		foreach($_POST['groups'] as $groupId){
			$CategoriesToGroups[]->featured_group_id = $groupId;
		}
	}
	$Category->save();
?>