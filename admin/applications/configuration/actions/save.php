<?php
	$configurationId = (int)$_GET['cID'];
	
	if (isset($_FILES['configuration_value'])){
		$curUpload = new upload('configuration_value');
		$curUpload->set_extensions(array('jpg', 'gif', 'png'));
		$curUpload->set_destination(DIR_FS_CATALOG_IMAGES);
		$configurationValue = '';
		if ($curUpload->parse() && $curUpload->save()){
			$configurationValue = $curUpload->filename;
		}elseif (isset($_POST['configuration_value_local']) && $_POST['configuration_value_local'] != 'none'){
			$configurationValue = $_POST['configuration_value_local'];
		}
	}elseif (is_array($_POST['configuration_value'])){
		$configurationValue = trim(implode(',', $_POST['configuration_value']));
	}else{
		$configurationValue = $_POST['configuration_value'];
	}
	
	$Configuration = Doctrine_Core::getTable('Configuration')->find($configurationId);
	if ($Configuration !== false){

        switch($Configuration->configuration_key){
	        case 'CFG_SORT_IGNORE_WORDS':
		        $Configuration->configuration_value = $configurationValue;
		        $Configuration->save();
		        $findString = array($configurationValue);
		        if(strstr($configurationValue,',') !== false){
			        $findString = explode(',',$configurationValue);
		        }

		        $allProducts = Doctrine_Query::create()
			        ->from('ProductsDescription')
			        ->fetchArray();
		        foreach($allProducts as $currentProduct) {
			        $currentProductSave = Doctrine_Core::getTable('ProductsDescription')->findOneByProductsDescriptionId($currentProduct['products_description_id']);
			        $currentProductSave->products_sname = trim(strtolower(str_ireplace($findString,'', $currentProduct['products_sname'])));

			        $currentProductSave->save();
		        }
		        break;
	        case 'PRODUCT_LISTING_PRODUCTS_LIMIT_ARRAY':
		        $valArray = explode(',',$configurationValue);
			    foreach($valArray as $val){
				    if($val > 25){
					    $messageStack->addSession('pageStack', 'Maximum value for product listing is 25. please lower the values higher than 25 and save again.' . $configurationId);
					    EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action'))), 'redirect');
				    }
			    }
		        break;
	        default:
		        $Configuration->configuration_value = $configurationValue;
		        $Configuration->save();
        }
	}else{
		$messageStack->addSession('pageStack', 'Configuration not found by id=' . $configurationId);
	}

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action'))), 'redirect');
?>