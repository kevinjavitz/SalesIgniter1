<?php
/* TODO
check for user coockie of the banner if is set than don't count the same for clicks. If is not set than set cookie for user with the banner id.
*/

$bid = $_GET['bid'];

$Banners = Doctrine_Core::getTable('BannerManagerBanners');
$Banner = $Banners->findOneByBannersId($bid);
$Banner->banners_views = $Banner->banners_views + 1;
$Banner->save();

?>