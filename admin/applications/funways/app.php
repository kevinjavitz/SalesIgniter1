<?php
// calculate funways category path
if (isset($_GET['cPath'])) {
	$cPath = $_GET['cPath'];
	/* } elseif (isset($_GET['products_id']) && !isset($_GET['manufacturers_id'])) {
			$fcPath = tep_get_product_path($_GET['products_id']);*/
} else {
	$cPath = 0;
}

if (tep_not_null($cPath)) {
	$cPath_array = tep_parse_funways_category_path($cPath);
	$cPath = implode('_', $cPath_array);
	$current_category_id = $cPath_array[(sizeof($cPath_array)-1)];
} else {
	$current_category_id = 0;
}


require(sysConfig::getDirFsAdmin() . 'includes/classes/upload.php');
require(sysConfig::getDirFsAdmin() . 'includes/classes/table_block.php');
require(sysConfig::getDirFsAdmin() . 'includes/classes/box.php');

require(sysConfig::getDirFsCatalog() . 'includes/classes/currencies.php');
$currencies = new currencies();
$action = (isset($_GET['action']) ? $_GET['action'] : '');
$redirect = false;
if (isset($_REQUEST['save_order']))
{
	$cats_order = $_REQUEST['sort_order_cat'];
	if (sizeof($cats_order))
		foreach ($cats_order as $key => $value) {
			$query = 'update ' . TABLE_FUNWAYS_CATEGORIES . '
				  set sort_order = ' . tep_db_input($value) . '
				  where categories_id = ' . tep_db_input($key);
			tep_db_query($query);
		}


	$prods_order = $_REQUEST['sort_order_prod'];
	if (sizeof($prods_order))
		foreach ($prods_order as $key => $value) {
			$query = 'update ' . TABLE_FUNWAYS_PRODUCTS . '
		set sort_order = ' . tep_db_input($value) . '
		where products_id = ' . tep_db_input($key);
			tep_db_query($query);
		}
}
//echo $action;
//itwExit();

if (!empty($action)) {
	switch ($action) {
		case 'setflag':
			if ( ($_GET['flag'] == '0') || ($_GET['flag'] == '1') ) {
				if (isset($_GET['pID'])) {
					tep_set_product_status($_GET['pID'], $_GET['flag']);
				}
			}

			$redirect = itw_app_link('cPath=' . $_GET['cPath'] . '&pID=' . $_GET['pID'],'funways','default');
			break;
		case 'setfflag':
			if ( ($_GET['fflag'] == '0') || ($_GET['fflag'] == '1') ) {
				if (isset($_GET['pID'])) {
					tep_set_product_featured($_GET['pID'], $_GET['fflag']);
				}
			}

			$redirect = itw_app_link('cPath=' . $_GET['cPath'] . '&pID=' . $_GET['pID'],'funways','default');
			break;

		case 'insert_category':
		case 'update_category':
			if (isset($_POST['categories_id'])) $categories_id = tep_db_prepare_input($_POST['categories_id']);
			$sort_order = tep_db_prepare_input($_POST['sort_order']);
			$categories_name = tep_db_prepare_input($_POST['categories_name']);
			$categories_link_to = tep_db_prepare_input($_POST['categories_link_to']);
			$categories_description = tep_db_prepare_input($_POST['categories_description']);

			$sql_data_array = array('sort_order' => $sort_order,
			                        'categories_name' => $categories_name,
			                        'link_to' => $categories_link_to,
			                        'categories_description' => $categories_description);

			if ($action == 'insert_category') {
				$insert_sql_data = array('parent_id' => $current_category_id,
				                         'date_added' => 'now()');

				$sql_data_array = array_merge($sql_data_array, $insert_sql_data);

				tep_db_perform(TABLE_FUNWAYS_CATEGORIES, $sql_data_array);

				$categories_id = tep_db_insert_id();
			} elseif ($action == 'update_category') {
				$update_sql_data = array('last_modified' => 'now()');

				$sql_data_array = array_merge($sql_data_array, $update_sql_data);

				tep_db_perform(TABLE_FUNWAYS_CATEGORIES, $sql_data_array, 'update', "categories_id = '" . (int)$categories_id . "'");
			}

			if ($categories_image = new upload('categories_image', DIR_FS_CATALOG_IMAGES)) {
				tep_db_query("update " . TABLE_FUNWAYS_CATEGORIES . " set categories_image = '" . tep_db_input($categories_image->filename) . "' where categories_id = '" . (int)$categories_id . "'");
			}

			$redirect = itw_app_link('cPath=' . $cPath . 'cID=' . $categories_id,'funways','default');
			break;
		case 'insert_product':
		case 'update_product':
			if (isset($_POST['products_id'])) $products_id = tep_db_prepare_input($_POST['products_id']);
			if (isset($_POST['categories_id'])) $categories_id = tep_db_prepare_input($_POST['categories_id']);
			$products_sort_order = tep_db_prepare_input($_POST['products_sort_order']);
			$products_name = tep_db_prepare_input($_POST['products_name']);
			$products_link_to = tep_db_prepare_input($_POST['products_link_to']);
			$products_description = tep_db_prepare_input($_POST['products_description']);

			$sql_data_array = array('sort_order' => $products_sort_order,
			                        'products_name' => $products_name,
			                        'link_to' => $products_link_to,
			                        'products_description' => $products_description);

			if ($action == 'insert_product') {
				$insert_sql_data = array('products_date_added' => 'now()');

				$sql_data_array = array_merge($sql_data_array, $insert_sql_data);

				tep_db_perform(TABLE_FUNWAYS_PRODUCTS, $sql_data_array);

				$products_id = tep_db_insert_id();

				$insert_sql_data = array('categories_id' => $categories_id,
				                         'products_id' => $products_id);
				tep_db_perform(TABLE_FUNWAYS_PTC, $insert_sql_data);
			} elseif ($action == 'update_product') {
				$update_sql_data = array('products_last_modified' => 'now()');

				$sql_data_array = array_merge($sql_data_array, $update_sql_data);

				tep_db_perform(TABLE_FUNWAYS_PRODUCTS, $sql_data_array, 'update', "products_id = '" . (int)$products_id . "'");
			}

			if ($products_image = new upload('products_image', DIR_FS_CATALOG_IMAGES)) {
				tep_db_query("update " . TABLE_FUNWAYS_PRODUCTS . " set products_image = '" . tep_db_input($products_image->filename) . "' where products_id = '" . (int)$products_id . "'");
			}

			$redirect = itw_app_link('cPath=' . $cPath . '&pID=' . $products_id . '&cID=' . $categories_id,'funways','default');

			break;
		case 'delete_category_confirm':
			if (isset($_POST['categories_id'])) {
				$categories_id = tep_db_prepare_input($_POST['categories_id']);
				$categories = tep_get_funways_category_tree($categories_id, '', '0', '', true);
				$products = array();
				$products_delete = array();

				for ($i=0, $n=sizeof($categories); $i<$n; $i++) {
					$product_ids_query = tep_db_query("select products_id from " . TABLE_FUNWAYS_PTC . " where categories_id = '" . (int)$categories[$i]['id'] . "'");

					while ($product_ids = tep_db_fetch_array($product_ids_query)) {
						$products[$product_ids['products_id']]['categories'][] = $categories[$i]['id'];
					}
				}

				reset($products);
				while (list($key, $value) = each($products)) {
					$category_ids = '';

					for ($i=0, $n=sizeof($value['categories']); $i<$n; $i++) {
						$category_ids .= "'" . (int)$value['categories'][$i] . "', ";
					}
					$category_ids = substr($category_ids, 0, -2);

					$check_query = tep_db_query("select count(*) as total from " . TABLE_FUNWAYS_PTC . " where products_id = '" . (int)$key . "' and categories_id not in (" . $category_ids . ")");
					$check = tep_db_fetch_array($check_query);
					if ($check['total'] < '1') {
						$products_delete[$key] = $key;
					}
				}

				// removing categories can be a lengthy process
				tep_set_time_limit(0);
				for ($i=0, $n=sizeof($categories); $i<$n; $i++) {
					tep_remove_funways_category($categories[$i]['id']);
				}

				reset($products_delete);
				while (list($key) = each($products_delete)) {
					tep_remove_funways_product($key);
				}


			}

			$redirect = itw_app_link('cPath=' . $cPath,'funways','default');
			break;
		case 'delete_product_confirm':
			echo('xxx3');
			if (isset($_POST['products_id']) && isset($_POST['product_categories']) && is_array($_POST['product_categories'])) {
				$product_id = tep_db_prepare_input($_POST['products_id']);
				$product_categories = $_POST['product_categories'];

				for ($i=0, $n=sizeof($product_categories); $i<$n; $i++) {
					tep_db_query("delete from " . TABLE_FUNWAYS_PTC . " where products_id = '" . (int)$product_id . "' and categories_id = '" . (int)$product_categories[$i] . "'");
				}

				$product_categories_query = tep_db_query("select count(*) as total from " . TABLE_FUNWAYS_PTC . " where products_id = '" . (int)$product_id . "'");
				$product_categories = tep_db_fetch_array($product_categories_query);
				echo('xxx2');
				if ($product_categories['total'] <= 0) {
					tep_remove_funways_product($product_id);
				}

			}

			$redirect = itw_app_link('cPath=' . $cPath,'funways','default');
				itwExit();
			break;
		case 'move_category_confirm':
			if (isset($_POST['categories_id']) && ($_POST['categories_id'] != $_POST['move_to_category_id'])) {
				$categories_id = tep_db_prepare_input($_POST['categories_id']);
				$new_parent_id = tep_db_prepare_input($_POST['move_to_category_id']);

				$path = explode('_', tep_get_generated_category_path_ids($new_parent_id));

				if (in_array($categories_id, $path)) {
					$messageStack->add_session(sysLanguage::get('ERROR_CANNOT_MOVE_CATEGORY_TO_PARENT'), 'error');
					tep_redirect(itw_app_link('cPath=' . $cPath . '&cID=' . $categories_id,'funways','default'));
				} else {
					tep_db_query("update " . TABLE_FUNWAYS_CATEGORIES . " set parent_id = '" . (int)$new_parent_id . "', last_modified = now() where categories_id = '" . (int)$categories_id . "'");

					$redirect = itw_app_link('cPath=' . $new_parent_id . '&cID=' . $categories_id,'funways','default');
				}
			}

			break;
	}
}
if(tep_not_null($redirect)){
	EventManager::attachActionResponse($redirect, 'redirect');
} else {
	$appContent = $App->getAppContentFile();
}

?>