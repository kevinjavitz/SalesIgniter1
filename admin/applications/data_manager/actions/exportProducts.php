<?php
	set_time_limit(0);
	require(sysConfig::getDirFsAdmin(). 'includes/classes/data_populate/export.php');
	$dataExport = new dataExport();

 	$uploaded = false;
	if (isset($_FILES['usrfl'])){
		$upload = new upload('usrfl');
		$upload->set_extensions(array('txt', 'xls', 'csv', 'tsv'));
		$upload->set_destination($dataExport->tempDir);
		if ($upload->parse() && $upload->save()) {
			$uploaded = true;
			$showLogInfo = true;
		}
	}
	//$dataExport = new dataExport();
	
	$dataExport->setHeaders(array(
		'v_products_model',
		'v_products_image',
		'v_products_type',
		'v_products_in_box',
		'v_products_featured',
		'v_products_price',
		'v_products_price_used',
		'v_products_price_stream',
		'v_products_price_download',
		'v_products_weight',
		'v_date_avail',
		'v_memberships_not_enabled',
		'v_products_categories'
	));

	foreach(sysLanguage::getLanguages() as $lInfo){
		$lID = $lInfo['id'];
		$dataExport->setHeaders(array(
			'v_products_name_' . $lID,
			'v_products_description_' . $lID,
			'v_products_url_' . $lID,
			'v_products_head_title_tag_' . $lID,
			'v_products_head_desc_tag_' . $lID,
			'v_products_head_keywords_tag_' . $lID
		));
	}

	$dataExport->setHeaders(array(
		'v_tax_class_title',
		'v_status'
	));

			
	EventManager::notify('DataExportFullQueryFileLayoutHeader', &$dataExport);

	$QfileLayout = Doctrine_Query::create()
	->select(
		'p.products_id, ' . 
		'p.products_model as v_products_model, ' . 
		'p.products_image as v_products_image, ' . 
		'p.products_price as v_products_price, ' . 
		'p.products_price_used as v_products_price_used, ' . 
		'p.products_price_stream as v_products_price_stream, ' . 
		'p.products_price_download as v_products_price_download, ' . 
		'p.products_weight as v_products_weight, ' . 
		'p.products_date_available as v_date_avail, ' . 
		'p.products_tax_class_id as v_tax_class_id, ' .
		'p.products_type as v_products_type, ' . 
		'p.products_in_box as v_products_in_box, ' . 
		'p.products_featured as v_products_featured, ' . 
		'p.products_status as v_status, ' .
		'p.membership_enabled as v_memberships_not_enabled, ' .
		'(SELECT group_concat(p2c.categories_id) FROM ProductsToCategories p2c WHERE p2c.products_id = p.products_id) as v_products_categories'
	)->from('Products p')
	->where('p.products_model is not null')
	->andWhere('p.products_model != ?', '');
			
	EventManager::notify('DataExportFullQueryBeforeExecute', &$QfileLayout);

	$preResult = $QfileLayout->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

	$Result = array();
	$colConverts = array();
	foreach($preResult as $i => $product){
		$Result[$i] = array();
		foreach($product as $k => $v){
			if (!isset($colConverts[$k])){
				$colConverts[$k] = substr($k, strpos($k, '_')+1);
			}
			$Result[$i][$colConverts[$k]] = $v;
		}
	}
	
	$dataRows = array();

	$p = -1;
	foreach($Result as $pInfo){
		$p++;
		if (isset($_POST['start_num']) && (!empty($_POST['start_num']) || $_POST['start_num'] == 0)){
			if ($p < $_POST['start_num']) continue;
		}
		if (isset($_POST['num_items']) && !empty($_POST['num_items'])){
			if ($p >= ((int)$_POST['start_num'] + $_POST['num_items'])) break;
		}

		foreach(sysLanguage::getLanguages() as $lInfo){
			$lID = $lInfo['id'];

			$Qdescription = Doctrine_Query::create()
			->from('ProductsDescription pd')
			->where('products_id = ?', $pInfo['products_id'])
			->andWhere('language_id = ?', $lID)
			->execute()->toArray();
			if (isset($Qdescription[$lID])){
				$pInfo = array_merge($pInfo, array(
					'v_products_name_' . $lID              => $Qdescription[$lID]['products_name'],
					'v_products_description_' . $lID       => $Qdescription[$lID]['products_description'],
					'v_products_url_' . $lID               => $Qdescription[$lID]['products_url'],
					'v_products_head_title_tag_' . $lID    => $Qdescription[$lID]['products_head_title_tag'],
					'v_products_head_desc_tag_' . $lID     => $Qdescription[$lID]['products_head_desc_tag'],
					'v_products_head_keywords_tag_' . $lID => $Qdescription[$lID]['products_head_keywords_tag']
				));
			}
		}
		$categories = explode(',', $pInfo['v_products_categories']);
		$catPaths = array();
		foreach($categories as $categoryId){
			$currentParent = $categoryId;
			$catPath = array();
			while($currentParent > 0){
				$Qcategory = Doctrine_Query::create()
				->select('c.categories_id, c.parent_id, cd.categories_name')
				->from('Categories c')
				->leftJoin('c.CategoriesDescription cd')
				->where('c.categories_id = ?', $currentParent)
				->andWhere('cd.language_id = ?', Session::get('languages_id'))
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

				$catPath[] = trim($Qcategory[0]['CategoriesDescription'][0]['categories_name']);
				$currentParent = $Qcategory[0]['parent_id'];
			}
			$catPaths[] = implode('>', array_reverse($catPath));
		}
		$pInfo['v_products_categories'] = implode(';', $catPaths);

		$nmembershipsString = '';
		if($pInfo['v_memberships_not_enabled'] != ''){
			$notEnabledMemberships = explode(';',$pInfo['v_memberships_not_enabled']);
			$Qmembership = Doctrine_Query::create()
			->from('Membership m')
			->leftJoin('m.MembershipPlanDescription md')
			->where('md.language_id = ?', Session::get('languages_id'))
			->orderBy('sort_order')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			foreach($Qmembership as $mInfo){
				if(in_array($mInfo['plan_id'], $notEnabledMemberships)){
					$nmembershipsString.= $mInfo['MembershipPlanDescription'][0]['name'].';';
				}
			}
			$nmembershipsString = substr($nmembershipsString,0,strlen($nmembershipsString)-1);
		}

		$pInfo['v_memberships_not_enabled'] = $nmembershipsString;

		$pInfo['v_tax_class_title'] = tep_get_tax_class_title($pInfo['v_tax_class_id']);
		$pInfo['v_products_price'] = $pInfo['v_products_price'];

		// Now set the status to a word the user specd in the config vars
		if ( $pInfo['v_status'] == '1' ){
			$pInfo['v_status'] = $active;
		} else {
			$pInfo['v_status'] = $inactive;
		}

		EventManager::notify('DataExportBeforeFileLineCommit', &$pInfo);
		
		$dataRows[] = $pInfo;
	}

	$dataExport->setExportData($dataRows);
	$dataExport->output(true);
?>