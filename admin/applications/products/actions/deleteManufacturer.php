<?php
	$Manufacturer = Doctrine_Core::getTable('Manufacturers')->find($_GET['mID']);
	if ($Manufacturer){
		if (isset($_POST['delete_image']) && $_POST['delete_image'] == '1'){
			$image_location = sysConfig::getDirFsCatalog() . 'images/' . $Manufacturer->manufacturers_image;

			if (file_exists($image_location)) @unlink($image_location);
		}

		$Products = $Manufacturer->Products;
		if (isset($_POST['delete_products']) && $_POST['delete_products'] == '1'){
			$Products->delete();
		}else{
			$Products->manufacturers_id = '';
		}
		
		$Manufacturer->delete();
	}
	
	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'mID')), 'products', 'manufacturers'), 'redirect');
?>