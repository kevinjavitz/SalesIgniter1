<?php
/*
	Multi Stores Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class multiStore_admin_data_manager_default extends Extension_multiStore {

	public function __construct(){
		parent::__construct();
	}
	
	public function load(){
		if ($this->isEnabled() === false) return;
		
		EventManager::attachEvents(array(
			'DataExportFullQueryBeforeExecute',
			'DataExportFullQueryFileLayoutHeader',
			'DataExportBeforeFileLineCommit',
			'DataImportBeforeSave'
		), null, $this);
	}
	
	public function DataImportBeforeSave(&$items, &$Product){
		global $appExtension;
		if (!isset($items['v_store_id'])) return;

		$stores = explode(',', $items['v_store_id']);
				
		$ProductsToStores =& $Product->ProductsToStores;
		$ProductsToStores->delete();
		foreach($stores as $storeId){
			$ProductsToStores[]->stores_id = $storeId;
		}

		$multiStore = $appExtension->getExtension('multiStore');

		$stores1 = $multiStore->getStoresArray();

		foreach($stores1->toArray(true) as $sInfo){
			$sID = $sInfo['stores_id'];
			$Product->StoresPricing[$sID]->stores_id = $sID;
			$Product->StoresPricing[$sID]->show_method = isset($items['v_store_use_global_'.$sID])?$items['v_store_use_global_'.$sID]:'use_global';

			if ($Product->StoresPricing[$sID]->show_method == 'use_custom'){
				$Product->StoresPricing[$sID]->products_type = isset($items['v_store_product_types_'.$sID])?$items['v_store_product_types_'.$sID]:'';
				$Product->StoresPricing[$sID]->products_price = isset($items['v_store_price_new_'.$sID])?$items['v_store_price_new_'.$sID]:'';
				$Product->StoresPricing[$sID]->products_price_used = isset($items['v_store_price_used_'.$sID])?$items['v_store_price_used_'.$sID]:'';
				$Product->StoresPricing[$sID]->products_price_stream = isset($items['v_store_price_stream_'.$sID])?$items['v_store_price_stream_'.$sID]:'';
				$Product->StoresPricing[$sID]->products_price_download = isset($items['v_store_price_download_'.$sID])?$items['v_store_price_download_'.$sID]:'';
				$Product->StoresPricing[$sID]->products_keepit_price = isset($items['v_store_price_keepit_'.$sID])?$items['v_store_price_keepit_'.$sID]:'';
			}else{
				$Product->StoresPricing[$sID]->products_type = isset($items['v_products_type'])?$items['v_products_type']:'';
				$Product->StoresPricing[$sID]->products_price = isset($items['v_products_price'])?$items['v_products_price']:'';
				$Product->StoresPricing[$sID]->products_price_used = isset($items['v_products_price_used'])?$items['v_products_price_used']:'';
				$Product->StoresPricing[$sID]->products_price_stream = isset($items['v_products_price_stream'])?$items['v_products_price_stream']:'';
				$Product->StoresPricing[$sID]->products_price_download = isset($items['v_products_price_download'])?$items['v_products_price_download']:'';
				$Product->StoresPricing[$sID]->products_keepit_price = isset($items['products_keepit_price'])?$items['products_keepit_price']:'';
			}
		}
		$Product->StoresPricing->save();
		$Product->save();

	}
	
	public function DataExportFullQueryFileLayoutHeader(&$dataExport){
		global $appExtension;
		$dataExport->setHeaders(array(
			'v_store_id'
		));
		$multiStore = $appExtension->getExtension('multiStore');
		if ($multiStore !== false && $multiStore->isEnabled() === true){
			foreach($multiStore->getStoresArray() as $storesId => $store){
				$dataExport->setHeaders(array(
						'v_store_use_global_'.$store['stores_id'],
						'v_store_product_types_'.$store['stores_id'],
						'v_store_price_new_'.$store['stores_id'],
						'v_store_price_used_'.$store['stores_id'],
						'v_store_price_stream_'.$store['stores_id'],
						'v_store_price_download_'.$store['stores_id'],
						'v_store_price_keepit_'.$store['stores_id']
				));
			}
		}

	}
	
	public function DataExportFullQueryBeforeExecute(&$query){
		$query->addSelect('(SELECT group_concat(p2s.stores_id) FROM ProductsToStores p2s WHERE p2s.products_id = p.products_id) as v_store_id');
	}
	
	public function DataExportBeforeFileLineCommit(&$productRow){
		$stores = $this->getStoresArray();

		$Qproduct = Doctrine_Query::create()
		->from('StoresPricing')
		->where('products_id = ?', (int)$productRow['products_id'])
		->execute();
		if ($Qproduct->count() > 0){
			$productInfo = $Qproduct->toArray(true);
		}


		foreach($stores as $sInfo){

			if (isset($productInfo)){
				$pInfo = $productInfo[$sInfo['stores_id']];
				$productRow['v_store_use_global_'.$sInfo['stores_id']] = isset($pInfo['show_method'])?$pInfo['show_method']:'use_global';
				$productRow['v_store_product_types_'.$sInfo['stores_id']] = isset($pInfo['products_type'])?$pInfo['products_type']:'';
				$productRow['v_store_price_new_'.$sInfo['stores_id']] = isset($pInfo['products_price'])?$pInfo['products_price']:0;
				$productRow['v_store_price_used_'.$sInfo['stores_id']] = isset($pInfo['products_price_used'])?$pInfo['products_price_used']:0;
				$productRow['v_store_price_stream_'.$sInfo['stores_id']] = isset($pInfo['products_price_stream'])?$pInfo['products_price_stream']:0;
				$productRow['v_store_price_download_'.$sInfo['stores_id']] = isset($pInfo['products_download'])?$pInfo['products_download']:0;
				$productRow['v_store_price_keepit_'.$sInfo['stores_id']] = isset($pInfo['products_keepit_price'])?$pInfo['products_keepit_price']:0;

			}

		}

	}
}
?>