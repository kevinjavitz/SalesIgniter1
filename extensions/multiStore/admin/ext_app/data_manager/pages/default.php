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
		if ($this->enabled === false) return;
		
		EventManager::attachEvents(array(
			'DataExportFullQueryBeforeExecute',
			'DataExportFullQueryFileLayoutHeader',
			'DataExportBeforeFileLineCommit',
			'DataImportBeforeSave'
		), null, $this);
	}
	
	public function DataImportBeforeSave(&$items, &$Product){
		if (!isset($items['v_store_id'])) return;

		$stores = explode(',', $items['v_store_id']);
				
		$ProductsToStores =& $Product->ProductsToStores;
		$ProductsToStores->delete();
		foreach($stores as $storeId){
			$ProductsToStores[]->stores_id = $storeId;
		}
	}
	
	public function DataExportFullQueryFileLayoutHeader(&$dataExport){
		$dataExport->setHeaders(array(
			'v_store_id'
		));
	}
	
	public function DataExportFullQueryBeforeExecute(&$query){
		$query->addSelect('(SELECT group_concat(p2s.stores_id) FROM ProductsToStores p2s WHERE p2s.products_id = p.products_id) as v_store_id');
	}
	
	public function DataExportBeforeFileLineCommit(&$productRow){
	}
}
?>