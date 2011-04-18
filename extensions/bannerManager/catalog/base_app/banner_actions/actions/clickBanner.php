<?php
/**
 * Created by IntelliJ IDEA.
 * User: madarcu
 * Date: May 9, 2010
 * Time: 1:29:09 AM
 * To change this template use File | Settings | File Templates.
 */

$bid = (int)$_GET['bid'];
$url = $_GET['url'];

$Banners = Doctrine_Core::getTable('BannerManagerBanners');
$Banner = $Banners->findOneByBannersId($bid);
$Banner->banners_clicks = $Banner->banners_clicks + 1;
$Banner->save();

EventManager::attachActionResponse($url, 'redirect');
?>