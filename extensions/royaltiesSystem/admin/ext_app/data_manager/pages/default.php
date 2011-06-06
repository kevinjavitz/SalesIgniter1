<?php
/*
	Prouct Attributes Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class royaltiesSystem_admin_data_manager_default extends Extension_royaltiesSystem {

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

	public function DataExportFullQueryBeforeExecute(&$query){
	}

	public function DataExportFullQueryFileLayoutHeader(&$dataExport){
		$purchaseTypes = false;
		$royaltiesProducts = Doctrine_Query::create()
				->select('purchase_type')
				->from('RoyaltiesSystemProductsRoyalties')
				->where('purchase_type <> ?', 'stream')
				->andWhere('purchase_type <> ?', 'download')
				->groupBy('purchase_type')
				->execute();
		foreach($royaltiesProducts as $purchase_type){
			$purchaseTypes[] = 'royalties_royalty_fee_' . $purchase_type['purchase_type'];
			$purchaseTypes[] = 'royalties_content_provider_id_' . $purchase_type['purchase_type'];
		}
		$dataExport->setHeaders($purchaseTypes);
	}

	public function DataExportBeforeFileLineCommit(&$productRow){
		$royaltiesProducts = Doctrine_Query::create()
				->from('RoyaltiesSystemProductsRoyalties')
				->where('products_id = ?', $productRow['products_id'])
				->execute()->toArray();
		if ($royaltiesProducts){
			foreach($royaltiesProducts as $i => $royaltiesProduct){
				if (isset($royaltiesProduct['content_provider_id'])){
					$productRow['royalties_content_provider_id_' . $royaltiesProduct['purchase_type']] = $royaltiesProduct['content_provider_id'];
					$productRow['royalties_royalty_fee_' . $royaltiesProduct['purchase_type']] = $royaltiesProduct['royalty_fee'];
				}
			}
		}
	}

	public function DataImportBeforeSave(&$items, &$Product){
		$royaltiesSystemProductsRoyalties =& $Product->RoyaltiesSystemProductsRoyalties;
		$royaltiesSystemProductsRoyalties->delete();

		$i = 0;
		if(isset($items['royalties_royalty_fee_new']) && !empty($items['royalties_royalty_fee_new'])){
			$royaltiesSystemProductsRoyalties[$i]->content_provider_id = $items['royalties_content_provider_id_new'];
			$royaltiesSystemProductsRoyalties[$i]->royalty_fee = $items['royalties_royalty_fee_new'];
			$royaltiesSystemProductsRoyalties[$i]->purchase_type = 'new';
			$i++;
		}
		if(isset($items['royalties_royalty_fee_used']) && !empty($items['royalties_royalty_fee_used'])){
			$royaltiesSystemProductsRoyalties[$i]->content_provider_id = $items['royalties_content_provider_id_used'];
			$royaltiesSystemProductsRoyalties[$i]->royalty_fee = $items['royalties_royalty_fee_used'];
			$royaltiesSystemProductsRoyalties[$i]->purchase_type = 'used';
			$i++;
		}
		if(isset($items['royalties_royalty_fee_rental']) && !empty($items['royalties_royalty_fee_rental'])){
			$royaltiesSystemProductsRoyalties[$i]->content_provider_id = $items['royalties_content_provider_id_rental'];
			$royaltiesSystemProductsRoyalties[$i]->royalty_fee = $items['royalties_royalty_fee_rental'];
			$royaltiesSystemProductsRoyalties[$i]->purchase_type = 'rental';
		}
		$Product->save();
	}
}
?>