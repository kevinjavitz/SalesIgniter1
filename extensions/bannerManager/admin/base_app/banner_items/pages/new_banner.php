<?php
	$Banners = Doctrine_Core::getTable('BannerManagerBanners');
	if (isset($_GET['bID']) && empty($_POST)){
		$Banner = $Banners->findOneByBannersId((int)$_GET['bID']);
	}else{
		$Banner = $Banners->getRecord();
	}


	$languages = tep_get_languages();

	if (!isset($Banner['banners_status'])) $Banner['banners_status'] = '1';
	switch ($Banner['banners_status']) {
		case '0': $in_status = false; $out_status = true; break;
		case '1':
		default: $in_status = true; $out_status = false;
	}

	$saveButton = htmlBase::newElement('button')->setType('submit')->usePreset('save');
	$cancelButton = htmlBase::newElement('button')->usePreset('cancel');

	
	$cancelButton->setHref(itw_app_link(tep_get_all_get_params(array('app', 'appName', 'action','bID')) .(isset($_GET['bID']) ? 'bID=' . $_GET['bID'] : ''), null, 'default'));


?>
 <form name="new_post" action="<?php echo itw_app_link(tep_get_all_get_params(array('app', 'appName', 'action')) .'action=saveBanner' . ((int)$Banner['banners_id'] > 0 ? '&bID=' . $Banner['banners_id'] : ''));?>" method="post" enctype="multipart/form-data">
 <div style="position:relative;text-align:right;"><?php
 	echo  $saveButton->draw() . $cancelButton->draw();
 	echo '<div class="pageHeading" style="position:absolute;left:0;top:.5em;">' . (isset($_GET['bID']) ? 'Edit Banner' : 'New Banner') . '</div>';
 ?></div>
 <div style="position:relative;">
 <div id="tab_container">
    <ul>
     <li class="ui-tabs-nav-item"><a href="#page-2"><span><?php echo sysLanguage::get('TAB_DESCRIPTION');?></span></a></li>
     <li class="ui-tabs-nav-item"><a href="#page-categories"><span><?php echo 'Groups';?></span></a></li>
<?php
	$contents = EventManager::notifyWithReturn('NewBannerTabHeader');
   if (!empty($contents)){
		foreach($contents as $content){
			echo $content;
		}
   }
?>
    </ul>
    <div id="page-2"><?php include(sysConfig::getDirFsCatalog(). 'extensions/bannerManager/admin/base_app/banner_items/pages_tabs/tab_description.php');?></div>
    <div id="page-categories"><?php include(sysConfig::getDirFsCatalog() . 'extensions/bannerManager/admin/base_app/banner_items/pages_tabs/tab_groups.php');?></div>
<?php
	$contents = EventManager::notifyWithReturn('NewBannerTabBody', &$Banner);
	if (!empty($contents)){
		foreach($contents as $content){
			echo $content;
		}
   }
  ?>
   </div>
  </div>
   <div style="position:relative;text-align:right;margin-top:.5em;margin-left:250px;"><?php
   echo $saveButton->draw() . $cancelButton->draw();
   ?>
   </div></form>
