<?php
$BannerManagerBanners = Doctrine_Core::getTable('BannerManagerBanners');
$BannerManagerGroups = Doctrine_Core::getTable('BannerManagerGroups');
?>
$BannerManagerBanners = Doctrine_Core::getTable('BannerManagerBanners');
$BannerManagerGroups = Doctrine_Core::getTable('BannerManagerGroups');
$BannerManagerBannersToGroups = Doctrine_Core::getTable('BannerManagerBannersToGroups');
<?php
$Settings = json_decode($Widget->Configuration['widget_settings']->configuration_value);
$BannerGroup = $BannerManagerGroups->find($Settings->selected_banner_group);
foreach($BannerGroup->BannerManagerBannersToGroups as $Banner){
	?>

$Banner = $BannerManagerBanners->findOneByBannersName('<?php echo $Banner->BannerManagerBanners->banners_name;?>');
if (!$Banner){
$Banner = $BannerManagerBanners->create();
<?php
			foreach($BannerManagerBanners->getColumns() as $colName => $cInfo){
		if (
			$colName == 'banners_id' ||
			$colName == 'banners_date_added' ||
			$colName == 'banners_date_scheduled' ||
			$colName == 'banners_date_status_changed' ||
			$colName == 'banners_expires_date'
		) continue;
		?>
	$Banner-><?php echo $colName;?> = '<?php echo $Banner->BannerManagerBanners->$colName;?>';
	<?php
 			}
	?>
$Banner->save();
}

$BannerGroup = $BannerManagerGroups->findOneByBannerGroupName('<?php echo $Banner->BannerManagerGroups->banner_group_name;?>');
if (!$BannerGroup){
$BannerGroup = $BannerManagerGroups->create();
<?php
			foreach($BannerManagerGroups->getColumns() as $colName => $cInfo){
		if (
			$colName == 'banner_group_id'
		) continue;
		?>
	$BannerGroup-><?php echo $colName;?> = '<?php echo $Banner->BannerManagerGroups->$colName;?>';
	<?php
 			}
	?>
$BannerGroup->save();
}

$BannerToGroup = $BannerManagerBannersToGroups->findOneByBannersIdAndBannerGroupId($Banner->banners_id, $BannerGroup->banner_group_id);
if (!$BannerToGroup){
$BannerToGroup = $BannerManagerBannersToGroups->create();
$BannerToGroup->banners_id = $Banner->banners_id;
$BannerToGroup->banner_group_id = $BannerGroup->banner_group_id;
$BannerToGroup->save();
}

<?php
		}
?>
$WidgetProperties->selected_banner_group = $BannerGroup->banner_group_id;

