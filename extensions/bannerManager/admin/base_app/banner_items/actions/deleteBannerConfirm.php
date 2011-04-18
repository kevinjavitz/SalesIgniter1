<?php
	if (isset($_GET['banners_id'])){
		$Banners = Doctrine_Core::getTable('BannerManagerBanners')->findOneByBannersId($_GET['banners_id']);
		if ($Banners){
			$Banners->delete();
			$messageStack->addSession('pageStack', 'Banner has been removed', 'success');
		}
	}



	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'banners_id'))), 'redirect');
?>