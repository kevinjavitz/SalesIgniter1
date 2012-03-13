<?php


	$inventory_centers = $appExtension->getExtension('inventoryCenters');
	//$langId = Session::get('languages_id');

	$invcent = Doctrine_Query::create()
				->from('ProductsInventoryCenters')
				->orderBy('inventory_center_name');

	if(Session::exists('isppr_city') && Session::get('isppr_city') != ''){
		$invcent->andWhere('inventory_center_city=?', Session::get('isppr_city'));
	}

	if(Session::exists('isppr_continent') && Session::get('isppr_continent') != ''){
		$invcent->andWhere('inventory_center_continent=?', Session::get('isppr_continent'));
	}

	if(Session::exists('isppr_state') && Session::get('isppr_state') != ''){
		$invcent->andWhere('inventory_center_state=?', Session::get('isppr_state'));
	}

	if(Session::exists('isppr_country') && Session::get('isppr_country') != ''){
		$invcent->andWhere('inventory_center_country=?', Session::get('isppr_country'));
	}

	$invcent = $invcent->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	$contentHtml = '<div class="main_list">';
	foreach($invcent as $invInfo){
		$f = true;
		$multiStore = $appExtension->getExtension('multiStore');
		if ($multiStore !== false && $multiStore->isEnabled() === true){
			$store = explode(';',$invInfo['inventory_center_stores']);
			if(in_array(Session::get('current_store_id'), $store)){
				$f = true;
			}else{
				$f = false;
			}
		}
		if($f){
			$contentHtml .= "<div class='list_inv'><b>Spot:</b> ".$invInfo['inventory_center_name']."<br/>";
			//$contentHtml .= '<b>Address:</b> '. $invInfo['inventory_center_address']."<br/>";
			$contentHtml .= "<a class='moreinfo' href='".itw_app_link('appExt=inventoryCenters&inv_id='.$invInfo['inventory_center_id'],'show_inventory','default')."'><b>More info</b></a>"."</div>";
		}
		//$contentHtml .= '<input type="hidden" name="inventory_center_address" value="'.$invInfo['inventory_center_address'].'"/>';
		//$script = "<script type='text/javascript' src='http://maps.google.com/maps?file=api&v=2&sensor=false&key=". EXTENSION_INVENTORY_CENTERS_GOOGLE_MAPS_API_KEY."'></script>";
	}
    $contentHtml .= '</div>';

	$contentHtml = stripslashes($contentHtml);

	EventManager::attachActionResponse(array(
				'success' => true,
				'data'     => $contentHtml
	), 'json');
?>