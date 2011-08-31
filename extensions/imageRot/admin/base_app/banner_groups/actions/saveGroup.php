<?php
	$Groups = Doctrine_Core::getTable('BannerManagerGroups');
	if (isset($_GET['gID'])){
		$Group = $Groups->findOneByBannerGroupId((int)$_GET['gID']);
	}else{
		$Group = $Groups->create();
	}

		$Group->banner_group_name = $_POST['banner_group_name'];
		$Group->banner_group_show_arrows = $_POST['banner_group_show_arrows'];
		$Group->banner_group_is_rotator = $_POST['banner_group_is_rotator'];
		$Group->banner_group_is_expiring = $_POST['banner_group_is_expiring'];
		$Group->banner_group_show_numbers = $_POST['banner_group_show_numbers'];
		$Group->banner_group_show_thumbnails = $_POST['banner_group_show_thumbnails'];
		$Group->banner_group_show_description = $_POST['banner_group_show_description'];
		$Group->banner_group_auto_rotate = $_POST['banner_group_auto_rotate'];

		$Group->banner_group_show_custom = $_POST['banner_group_show_custom'];
		$Group->banner_group_show_thumbs_desc = $_POST['banner_group_show_thumbs_desc'];
		$Group->banner_group_use_autoresize = $_POST['banner_group_use_autoresize'];
		$Group->banner_group_use_thumbs = $_POST['banner_group_use_thumbs'];

		$Group->banner_group_auto_hide_numbers = $_POST['banner_group_auto_hide_numbers'];
		$Group->banner_group_auto_hide_custom = $_POST['banner_group_auto_hide_custom'];
		$Group->banner_group_auto_hide_arrows = $_POST['banner_group_auto_hide_arrows'];

		$Group->banner_group_auto_hide_thumbs = $_POST['banner_group_auto_hide_thumbs'];
		$Group->banner_group_auto_hide_thumbs_desc = $_POST['banner_group_auto_hide_thumbs_desc'];
		$Group->banner_group_auto_hide_title = $_POST['banner_group_auto_hide_title'];

		$Group->banner_group_hover_pause = $_POST['banner_group_hover_pause'];

		if (!empty($_POST['banner_group_time'])){
			$Group->banner_group_time = $_POST['banner_group_time'];
		}else{
			$Group->banner_group_time = '5';
		}

		if (!empty($_POST['banner_group_effect'])){
			$Group->banner_group_effect = $_POST['banner_group_effect'];
		}else{
			$Group->banner_group_effect = 'fade';
		}

		if (!empty($_POST['banner_group_effect_time'])){
			$Group->banner_group_effect_time = $_POST['banner_group_effect_time'];
		}else{
			$Group->banner_group_effect_time = '1000';
		}

			$Group->banner_group_width = $_POST['banner_group_width'];
			$Group->banner_group_height = $_POST['banner_group_height'];

			$Group->banner_group_thumbs_width = $_POST['banner_group_thumbs_width'];
			$Group->banner_group_thumbs_height = $_POST['banner_group_thumbs_height'];

			$Group->banner_group_spw = $_POST['banner_group_spw'];
			$Group->banner_group_sph = $_POST['banner_group_sph'];

			$Group->banner_group_strips = $_POST['banner_group_strips'];

		if (!empty($_POST['banner_group_description_opacity'])){
			$Group->banner_group_description_opacity = $_POST['banner_group_description_opacity'];
		}else{
			$Group->banner_group_description_opacity = '0.8';
		}



	//if (!empty($_POST['categories_htc_title_tag'][$lID])){

	$Group->save();


	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'gID')) . 'gID=' . $Group->banner_group_id, null, 'default'), 'redirect');
?>