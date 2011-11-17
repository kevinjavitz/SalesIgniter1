<?php
//echo 'POST::<pre>';print_r($_POST);echo '</pre>';
//echo 'FILES::<pre>';print_r($_FILES);echo '</pre>';
//echo '{ success:true }';
//itwExit();

	$products_date_available = tep_db_prepare_input($_POST['products_date_available']);
	$products_date_available = (date('Y-m-d') < $products_date_available) ? $products_date_available : 'null';

	$Products = Doctrine_Core::getTable('Products');
	if (isset($_GET['pID'])){
		$Product = $Products->findOneByProductsId((int)$_GET['pID']);
	}elseif (isset($_POST['product_id'])){
		$Product = $Products->findOneByProductsId((int)$_POST['product_id']);
	}else{
		$Product = $Products->create();

		$Product->ProductsToCategories[0]['categories_id'] = $current_category_id;
	}

	$Product->products_on_order = (isset($_POST['products_on_order']) ? (int)$_POST['products_on_order'] : '0');
	$Product->products_date_ordered = (!empty($_POST['products_date_ordered']) ? $_POST['products_date_ordered'] : 'null');
	$Product->products_model = str_replace(array("'", '"', ' '), '_', $_POST['products_model']);
	$Product->products_price = (float)$_POST['products_price'];
	$Product->products_price_used = (float)$_POST['products_price_used'];
	$Product->products_date_available = $products_date_available;
	$Product->products_weight = ((float)$_POST['products_weight'] <= 0) ? '1' : (float)$_POST['products_weight'];
	$Product->products_status = $_POST['products_status'];
	$Product->products_featured = $_POST['products_featured'];
	$Product->products_tax_class_id = $_POST['products_tax_class_id'];
	$Product->manufacturers_id = (isset($_POST['manufacturers_id']))?(int)$_POST['manufacturers_id']:0;
	$Product->products_inventory_controller = $_POST['products_inventory_controller'];
	if (isset($_POST['products_type'])){
		if (is_array($_POST['products_type'])){
			$Product->products_type = implode(',', $_POST['products_type']);
		}else{
			$Product->products_type = $_POST['products_type'];
		}
	}else{
		$Product->products_type = 'new';
	}
	$Product->products_in_box = (int)(isset($_POST['products_in_box']) ? $_POST['products_in_box'] : '0');

	if (isset($_POST['products_image'])){
		$Product->products_image = $_POST['products_image'];
	}

	$languages = tep_get_languages();
	$ProductsDescription = $Product->ProductsDescription;
	for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
		$lang_id = $languages[$i]['id'];

		$ProductsDescription[$lang_id]->language_id = $lang_id;
		$ProductsDescription[$lang_id]->products_name = $_POST['products_name'][$lang_id];
		$ProductsDescription[$lang_id]->products_sname = trim(strtolower($_POST['products_name'][$lang_id]));
		$ProductsDescription[$lang_id]->products_description = $_POST['products_description'][$lang_id];
		$ProductsDescription[$lang_id]->products_url = $_POST['products_url'][$lang_id];
		$ProductsDescription[$lang_id]->products_seo_url = $_POST['products_seo_url'][$lang_id];
	}

	if(isset($_POST['rental_membership_enabled'])){
		$Product->membership_enabled = implode(';',$_POST['rental_membership_enabled']);
	} else {
		$Product->membership_enabled = '';
	}

	/*
	 * anything additional to handle into $ProductsDescription ?
	 */
	EventManager::notify('ProductsDescriptionsBeforeSave', &$ProductsDescription);

	//------------------------- BOX set begin block -----------------------------//
	/*$ProductsToBox = $Product->ProductsToBox;
	if (isset($_POST['products_in_box']) && $_POST['products_in_box'] == '1'){
		$ProductsToBox->box_id = $_POST['box_ex'];
		$ProductsToBox->disc = $_POST['disc_label'];
	}else{
		$ProductsToBox->delete();
	}*/
	//------------------------- BOX set end block -----------------------------//

	$ProductsToCategories = $Product->ProductsToCategories;
	$ProductsToCategories->delete();
	if (isset($_POST['categories'])){
		foreach($_POST['categories'] as $categoryId){
			$ProductsToCategories[]->categories_id = $categoryId;
		}
	}

	$ProductsAdditionalImages = $Product->ProductsAdditionalImages;
	$ProductsAdditionalImages->delete();
	if (isset($_POST['additional_images']) && !empty($_POST['additional_images'])){
		$saved = array();
		$imgArr = explode(';', $_POST['additional_images']);
		foreach($imgArr as $fileName){
			if (!in_array($fileName, $saved)){
				$ProductsAdditionalImages[]->file_name = $fileName;
				$saved[] = $fileName;
			}
		}
	}

	//echo '<pre>';print_r($_POST);print_r($Product->toArray(true));exit;
	$Product->save();

	$inv = 0;
	$postedQty = $_POST['inventory_quantity']['normal'];
	$ProductsInventoryTable = Doctrine_Core::getTable('ProductsInventory');
	foreach($inventoryTypes as $typeShort => $typeName){
		$ProductsInventory = $ProductsInventoryTable->findOneByTypeAndControllerAndProductsId(
			$typeShort,
			'normal',
			$Product->products_id
		);
		if (!$ProductsInventory){
			$ProductsInventory = new ProductsInventory();
			$ProductsInventory->products_id = $Product->products_id;
			$ProductsInventory->type = $typeShort;
			$ProductsInventory->controller = 'normal';
		}

		if ($appExtension->isInstalled('inventoryCenters')){
			if (isset($_POST['use_center']['normal'][$typeShort])){
				$ProductsInventory->use_center = '1';
			}else{
				$ProductsInventory->use_center = '0';
			}
		}
		if (isset($_POST['track_method']['normal'][$typeShort])){
			$ProductsInventory->track_method = $_POST['track_method']['normal'][$typeShort];
		}
		$ProductsInventory->save();

		$invId = $ProductsInventory->inventory_id;

		$ProductsInventoryQuantity = null;
		if (isset($postedQty[$typeShort])){
			$QProductsInventoryQuantity = Doctrine_Query::create()
			->select('quantity_id')
			->from('ProductsInventoryQuantity')
			->where('inventory_id = ?', $invId);
			if ($appExtension->isInstalled('attributes')){
				$QProductsInventoryQuantity->andWhere('attributes IS NULL');
			}
			if ($appExtension->isInstalled('inventoryCenters')){
				$QProductsInventoryQuantity->andWhere('inventory_center_id = ?', '0');
				if ($appExtension->isInstalled('multiStore')){
					$QProductsInventoryQuantity->andWhere('inventory_store_id = ?', '0');
				}
			}
			$Result = $QProductsInventoryQuantity->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			if (!$Result){
				$ProductsInventoryQuantity = new ProductsInventoryQuantity();
				$ProductsInventoryQuantity->inventory_id = $invId;
			}else{
				$ProductsInventoryQuantity = Doctrine_Core::getTable('ProductsInventoryQuantity')->find($Result[0]['quantity_id']);
			}
			$ProductsInventoryQuantity->available = $postedQty[$typeShort]['A'];
			$ProductsInventoryQuantity->save();
		}

		EventManager::notify(
			'SaveProductInventoryQuantity',
			&$ProductsInventory,
			'normal',
			$typeShort,
			$postedQty
		);
	}

	if (isset($_GET['rType']) && $_GET['rType'] == 'ajax'){
		EventManager::attachActionResponse(array(
			'success' => true,
			'pID'     => $Product->products_id
		), 'json');
	}else{
		if (isset($_POST['categories_save_redirect'])){
			$link = $_POST['categories_save_redirect'];
		}else{
			$link = itw_app_link(tep_get_all_get_params(array('action', 'pID')) . 'pID=' . $Product->products_id, null, 'default');
		}
		EventManager::attachActionResponse($link, 'redirect');
	}
?>
