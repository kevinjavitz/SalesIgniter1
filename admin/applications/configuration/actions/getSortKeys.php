<?php

	$name = $_GET['listing_id'];
	$className = 'productListing_' . $name;
 	if (isset($_GET['selected'])){
		$selected = $_GET['selected'];
	}else{
		$selected = '';
	}
	$selected = str_replace('~','=', $selected);

	$module = false;
	if(!empty($name)){
		if (file_exists(sysConfig::getDirFsCatalog() . 'includes/classes/product_listing/' . $name . '.php')){
			$module = sysConfig::getDirFsCatalog() . 'includes/classes/product_listing/' . $name . '.php';
		}

		if ($module === false){
			$dirObj = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'extensions/');
			while($dirObj->valid()){
				if ($dirObj->isDot() || $dirObj->isFile()){
					$dirObj->next();
					continue;
				}

				if (is_dir($dirObj->getPathname() . '/catalog/classes/product_listing/')){
					if (file_exists($dirObj->getPathname() . '/catalog/classes/product_listing/' . $name . '.php')){
						$module = $dirObj->getPathname() . '/catalog/classes/product_listing/' . $name . '.php';
					}
				}

				$dirObj->next();
			}
		}

		if (file_exists(sysConfig::getDirFsCatalog() . 'templates/fallback/classes/product_listing/' . $name . '.php')){
			$module = sysConfig::getDirFsCatalog() . 'templates/fallback/classes/product_listing/' . $name . '.php';
		}

		if (!class_exists($className)){
			require($module);
		}

		$sortListingClass = new $className;


		$html = '';
		foreach($sortListingClass->sortColumns() as $sortCol){
			if($sortCol['value'] == $selected){
				$html .= '<option selected="selected" value="'.$sortCol['value'].'">'.$sortCol['name'].'</option>';
			}else{
				$html .= '<option value="'.$sortCol['value'].'">'.$sortCol['name'].'</option>';
			}
		}
	}

	$json = array(
			'success' => true,
			'listId'  => $_GET['listId'],
			'html'    => $html
	);

	EventManager::attachActionResponse($json, 'json');
?>