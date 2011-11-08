<?php
	$Manufacturer = Doctrine_Core::getTable('Manufacturers')->find($_GET['mID']);
	if ($Manufacturer){
		if (isset($_POST['delete_image']) && $_POST['delete_image'] == '1' && strlen($Manufacturer->manufacturers_image) > 0){
			$image_location = sysConfig::getDirFsCatalog() . 'images/' . $Manufacturer->manufacturers_image;

			if (file_exists($image_location) && !is_dir($image_location)) @unlink($image_location);
		}
		if(count($Manufacturer->Products) > 0){
			$Products = $Manufacturer->Products;
			if (isset($_POST['delete_products']) && $_POST['delete_products'] == '1'){
				$Products->delete();
			}else{
				$Products->manufacturers_id = '';
			}
		}
		
		$Manufacturer->delete();
	}
	
	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'mID')), 'products', 'manufacturers'), 'redirect');
?>