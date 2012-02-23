<?php
	$ProductsToStores = $Product->ProductsToStores;
	$ProductsToStores->delete();
	if (isset($_POST['store'])){
		foreach($_POST['store'] as $storeId){
			$ProductsToStores[]->stores_id = $storeId;
		}
	}
	$multiStore = $appExtension->getExtension('multiStore');

	$stores1 = $multiStore->getStoresArray();

	foreach($stores1->toArray(true) as $sInfo){
		$sID = $sInfo['stores_id'];
		$Product->StoresPricing[$sID]->stores_id = $sID;
		$Product->StoresPricing[$sID]->show_method = $_POST['store_show_method'][$sID];
		if(in_array('reservation', $_POST['products_type'])){
			$_POST['products_type_store_'.$sID][] = 'reservation';
		}
		if ($_POST['store_show_method'][$sID] == 'use_custom'){
			$Product->StoresPricing[$sID]->products_type = implode(',',$_POST['products_type_store_'.$sID]);
			$Product->StoresPricing[$sID]->products_price = $_POST['products_price_'.$sID];
			$Product->StoresPricing[$sID]->products_price_used = $_POST['products_price_used_'.$sID];
			$Product->StoresPricing[$sID]->products_price_stream = $_POST['products_price_stream_'.$sID];
			$Product->StoresPricing[$sID]->products_price_download = $_POST['products_price_download_'.$sID];
			$Product->StoresPricing[$sID]->products_keepit_price = $_POST['products_keepit_price_'.$sID];
		}else{
			$Product->StoresPricing[$sID]->products_type = implode(',',$_POST['products_type']);
			$Product->StoresPricing[$sID]->products_price = $_POST['products_price'];
			$Product->StoresPricing[$sID]->products_price_used = $_POST['products_price_used'];
			$Product->StoresPricing[$sID]->products_price_stream = $_POST['products_price_stream'];
			$Product->StoresPricing[$sID]->products_price_download = $_POST['products_price_download'];
			$Product->StoresPricing[$sID]->products_keepit_price = $_POST['products_keepit_price'];
		}
	}
	$Product->StoresPricing->save();
	$Product->save();
?>