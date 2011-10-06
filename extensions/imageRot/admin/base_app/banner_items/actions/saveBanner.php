<?php
	require(sysConfig::getDirFsAdmin() . 'includes/classes/upload.php');

	$banner_date_scheduled = tep_db_prepare_input($_POST['banners_date_scheduled']);
	$banner_date_expires = tep_db_prepare_input($_POST['banners_expires_date']);

	$Banners = Doctrine_Core::getTable('BannerManagerBanners');
	if (isset($_GET['bID'])){
		$Banner = $Banners->findOneByBannersId((int)$_GET['bID']);
	}elseif (isset($_POST['banners_id'])){
		$Banner = $Banners->findOneByBannersId((int)$_POST['banners_id']);
	}else{
		$Banner = $Banners->create();
	}

	$Banner->banners_status = $_POST['banners_status'];
	$Banner->banners_date_scheduled = $banner_date_scheduled;
	$Banner->banners_expires_date = $banner_date_expires;

	if ($banners_body = new upload('banners_body', sysConfig::getDirFsCatalog().'extensions/imageRot/images/')) {
		if(tep_not_null($banners_body->filename))
			$Banner->banners_body = $banners_body->filename;
	}

	if ($banners_body_thumbs = new upload('banners_body_thumbs', sysConfig::getDirFsCatalog().'extensions/imageRot/images/')) {
		if(tep_not_null($banners_body_thumbs->filename))
			$Banner->banners_body_thumbs = $banners_body_thumbs->filename;
	}

		$Banner->banners_name = $_POST['banners_name'];
		$Banner->banners_url = $_POST['banners_url'];
		$Banner->banners_cms_page = $_POST['banners_cms_page'];
        $Banner->banners_html = $_POST['banners_html'];
		$Banner->banners_description = $_POST['banners_description'];
		$Banner->banners_small_description = $_POST['banners_small_description'];
		$Banner->banners_expires_clicks = $_POST['banners_expires_clicks'];
		$Banner->banners_expires_views = $_POST['banners_expires_views'];

	
	$BannersToGroups = $Banner->BannerManagerBannersToGroups;
	$BannersToGroups->delete();
	if (isset($_POST['groups'])){
		foreach($_POST['groups'] as $groupId){
			$BannersToGroups[]->banner_group_id = $groupId;
		}
	}
	$Banner->save();

	
	$link = itw_app_link(tep_get_all_get_params(array('action', 'bID')) . 'bID=' . $Banner->banners_id, null, 'default');
	EventManager::attachActionResponse($link, 'redirect');
?>