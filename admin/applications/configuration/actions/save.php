<?php
	$configurationId = (int)$_GET['cID'];

	if (is_array($_POST['configuration_value'])){
		$configurationValue = trim(implode(',', $_POST['configuration_value']));
	}else{
		$configurationValue = $_POST['configuration_value'];
	}
	$Configuration = Doctrine_Core::getTable('Configuration')->find($configurationId);
	if ($Configuration !== false){
		$Configuration->configuration_value = $configurationValue;
		$Configuration->save();
        if($Configuration->configuration_key == 'CFG_SORT_IGNORE_WORDS'){
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
        }
	}

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action'))), 'redirect');
?>