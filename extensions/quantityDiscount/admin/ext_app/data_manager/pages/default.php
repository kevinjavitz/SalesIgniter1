<?php
/*
	Quantity Discount Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class quantityDiscount_admin_data_manager_default extends Extension_quantityDiscount {

	public function __construct(){
		parent::__construct();
	}
	
	public function load(){
		global $appExtension;
		if ($this->isEnabled() === false) return;
		
		EventManager::attachEvents(array(
			'DataExportFullQueryBeforeExecute',
			'DataExportFullQueryFileLayoutHeader',
			'DataExportBeforeFileLineCommit',
			'DataImportBeforeSave',
			'DataImportProductLogBeforeExecute',
		), null, $this);
	}
	
	public function DataImportProductLogBeforeExecute(&$Product, &$productLogArr){
	}
	
	public function DataExportFullQueryBeforeExecute(&$query){
		$query->addSelect('(SELECT count(*) from ProductsQuantityDiscounts pqd where pqd.products_id = p.products_id) as quantityDiscounts');
	}
	
	public function DataExportFullQueryFileLayoutHeader(&$dataExport){
		$headers = array();
		for($i=1; $i<(EXTENSION_QUANTITY_DISCOUNT_LEVELS + 1); $i++){
			$headers[] = 'v_quantity_discount_' . $i . '_from';
			$headers[] = 'v_quantity_discount_' . $i . '_to';
			$headers[] = 'v_quantity_discount_' . $i . '_price';
		}
		$dataExport->setHeaders($headers);
	}
	
	public function DataExportBeforeFileLineCommit(&$productRow){
		if ($productRow['quantityDiscounts'] > 0){
			$discounts = Doctrine_Query::create()
			->from('ProductsQuantityDiscounts')
			->where('products_id = ?', $productRow['products_id'])
			->orderBy('quantity_from asc')
			->execute();
			
			$i=1;
			foreach($discounts as $dInfo){
				$productRow['v_quantity_discount_' . $i . '_from'] = $dInfo->quantity_from;
				$productRow['v_quantity_discount_' . $i . '_to'] = $dInfo->quantity_to;
				$productRow['v_quantity_discount_' . $i . '_price'] = $dInfo->price;
				$i++;
			}
			
			if ($i < (EXTENSION_QUANTITY_DISCOUNT_LEVELS + 1)){
				for($j=$i; $j<(EXTENSION_QUANTITY_DISCOUNT_LEVELS + 1); $j++){
					$productRow['v_quantity_discount_' . $j . '_from'] = '0';
					$productRow['v_quantity_discount_' . $j . '_to'] = '0';
					$productRow['v_quantity_discount_' . $j . '_price'] = '0.0000';
				}
			}
		}
	}
	
	public function DataImportBeforeSave(&$items, &$Product){
		$Product->ProductsQuantityDiscounts->delete();
		if (isset($items['v_quantity_discount_1_from'])){
			$qtyCount = 0;
			for($i=1; $i<(EXTENSION_QUANTITY_DISCOUNT_LEVELS + 1); $i++){
				$qtyFrom = $items['v_quantity_discount_' . $i . '_from'];
				$qtyTo = $items['v_quantity_discount_' . $i . '_to'];
				$price = $items['v_quantity_discount_' . $i . '_price'];
				if ($qtyFrom > 0 && $qtyTo > 0){
					$Product->ProductsQuantityDiscounts[$qtyCount]->quantity_from = $qtyFrom;
					$Product->ProductsQuantityDiscounts[$qtyCount]->quantity_to = $qtyTo;
					$Product->ProductsQuantityDiscounts[$qtyCount]->price = $price;
					$qtyCount++;
				}
			}
		}
	}
}
?>