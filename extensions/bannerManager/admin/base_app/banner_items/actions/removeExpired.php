<?php
/*
$BannersBefore =  Doctrine_Query::create()
				->select('b.*')
				->from('BannerManagerBanners b')
				->leftJoin('b.BannerManagerBannersToGroups g')
				->where('g.banner_group_id = ?', $group)
				->orderBy('b.banners_id')
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

$UpdateBanners = Doctrine_Query::create()
						->update('BannerManagerBanners b')
						->leftJoin('b.BannerManagerBannersToGroups g')
						->set('b.banners_status','b.banners_status')
						->where('g.banner_group_id = ?', $group)
						->execute();

$BannersAfter =  Doctrine_Query::create()
				->select('b.*')
				->from('BannerManagerBanners b')
				->leftJoin('b.BannerManagerBannersToGroups g')
				->where('g.banner_group_id = ?', $group)
				->orderBy('b.banners_id')
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

$removedIDs = Array();

foreach($BannersBefore as $bannerb){
	$bid = $bannerb['banners_id'];
	$rem = true;
	$theId = -1;
	foreach($BannersAfter as $bannera){
		if($bannera['banners_id'] == $bid){
			$rem = false;
			$theId = $bannera['banners_id'];
			break;
		}
	}
	if($rem)
		$removedIDs[] = $theId;
}

Return array with ID as json array.
*/


?>