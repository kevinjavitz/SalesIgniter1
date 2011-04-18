<?php
	$headersKept = array();
	$headingData = array();
	
	$status = $_POST['products_listing_status'];
	foreach($status as $listingId => $colStatus){
		if ($listingId == 'new'){
			foreach($colStatus as $randomKey => $newColStatus){
				$headingData[] = array(
					'heading'       => $_POST['products_listing_name']['new'][$randomKey],
					//'headingAlign'  => $_POST['products_listing_heading_align']['new'][$randomKey],
					'status'        => $newColStatus,
					'sortOrder'     => $_POST['products_listing_sort_order']['new'][$randomKey],
					'allowSort'     => '1',
					'defaultSorting'=> $_POST['products_listing_default_sorting']['new'][$randomKey],
					'sortKey'       => $_POST['products_listing_sort_key']['new'][$randomKey],
					'sortSql'       => $_POST['products_listing_sort_key']['new'][$randomKey],
					'listingModule' => $_POST['products_listing_module']['new'][$randomKey],
					'templates'     => $_POST['products_listing_template']['new'][$randomKey]
				);
			}
		}else{
			$headersKept[] = $listingId;			
			$headingData[] = array(
				'listing_id'    => $listingId,
				'heading'       => $_POST['products_listing_name'][$listingId],
				//'headingAlign'  => $_POST['products_listing_heading_align'][$listingId],
				'status'        => $colStatus,
				'sortOrder'     => $_POST['products_listing_sort_order'][$listingId],
				'allowSort'     => '1',
				'defaultSorting'     => $_POST['products_listing_default_sorting'][$listingId],
				'sortKey'       => $_POST['products_listing_sort_key'][$listingId],
				'sortSql'       => $_POST['products_listing_sort_key'][$listingId],
				'listingModule' => $_POST['products_listing_module'][$listingId],
				'templates'     => (isset($_POST['products_listing_template'][$listingId]) ? $_POST['products_listing_template'][$listingId] : array())
			);
		}
	}
	
	$Qdelete = Doctrine_Query::create()
	->delete('ProductsListing')
	->where('products_listing_allow_sort = 1');
	if (sizeof($headersKept) > 0){
		$Qdelete->whereNotIn('products_listing_id', $headersKept);
	}
	$Qdelete->execute();
	
	if (sizeof($headingData) > 0){
		$ProductsListing = Doctrine_Core::getTable('ProductsListing');
		foreach($headingData as $data){
			if (isset($data['listing_id'])){
				$listingCol = $ProductsListing->findOneByProductsListingId($data['listing_id']);
			}else{
				$listingCol = $ProductsListing->create();
			}
			
			$listingCol->products_listing_status = $data['status'];
			$listingCol->products_listing_sort_order = $data['sortOrder'];
			$listingCol->products_listing_allow_sort = $data['allowSort'];
			$listingCol->products_listing_sort_key = $data['sortKey'];
			$listingCol->products_listing_default_sorting = $data['defaultSorting'];
			$listingCol->products_listing_sort_column = $data['sortSql'];
			//$listingCol->products_listing_heading_align = $data['headingAlign'];
			$listingCol->products_listing_module = $data['listingModule'];
			$listingCol->products_listing_template = implode(',', $data['templates']);
			
			foreach($data['heading'] as $langId => $text){
				$listingCol->ProductsListingDescription[$langId]->products_listing_heading_text = $text;
				$listingCol->ProductsListingDescription[$langId]->language_id = $langId;
			}
			
			$listingCol->save();
		}
	}
	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action')), null, 'product_sort_listing'), 'redirect');
?>