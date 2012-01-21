<?php
/*
	Prouct Attributes Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class attributes_admin_data_manager_default extends Extension_attributes {

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
		$mostAttributes = 0;
		$Qattributes = Doctrine_Query::create()
		->select('count(products_attributes_id) as total')
		->from('ProductsAttributes')
		->groupBy('products_id')
		->execute();
		foreach($Qattributes as $aTotal){
			if ($aTotal['total'] > $mostAttributes){
				$mostAttributes = $aTotal['total'];
			}
		}
			
		for($i=1; $i<$mostAttributes+1; $i++){
			$dataExport->setHeaders(array(
				'v_attribute_' . $i,
				'v_attribute_' . $i . '_image',
				'v_attribute_' . $i . '_views',
				'v_attribute_' . $i . '_price',
				'v_attribute_' . $i . '_sort'
			));
		}
	}
	
	public function DataExportBeforeFileLineCommit(&$productRow){
		$Qattributes = Doctrine_Query::create()
		->from('ProductsAttributes a')
		->leftJoin('a.ProductsAttributesViews av')
		->leftJoin('a.ProductsOptionsGroups og')
		->leftJoin('a.ProductsOptions o')
		->leftJoin('o.ProductsOptionsDescription od')
		->leftJoin('a.ProductsOptionsValues ov')
		->leftJoin('ov.ProductsOptionsValuesDescription ovd')
		->where('a.products_id = ?', $productRow['products_id'])
		->execute()->toArray();
		if ($Qattributes){

			$lID = Session::get('languages_id');
			foreach($Qattributes as $i => $attribute){
				if (isset($attribute['ProductsOptionsGroups']) && isset($attribute['ProductsOptions']['ProductsOptionsDescription']) && isset($attribute['ProductsOptionsValues']['ProductsOptionsValuesDescription'])){
					$crumb = $attribute['ProductsOptionsGroups']['products_options_groups_name'] . '>' . $attribute['ProductsOptions']['ProductsOptionsDescription'][$lID]['products_options_name'] . '>' . $attribute['ProductsOptionsValues']['ProductsOptionsValuesDescription'][$lID]['products_options_values_name'];

					$views = array();
					if (array_key_exists('ProductsAttributesViews', $attribute)){
						foreach($attribute['ProductsAttributesViews'] as $viewInfo){
							$views[] = $viewInfo['view_name'] . ':' . $viewInfo['view_image'];
						}
					}

					$realCount = $i+1;
					$productRow['v_attribute_' . $realCount] = $crumb;
					$productRow['v_attribute_' . $realCount . '_image'] = $attribute['options_values_image'];
					$productRow['v_attribute_' . $realCount . '_views'] = implode(';', $views);
					$productRow['v_attribute_' . $realCount . '_price'] = $attribute['options_values_price'];
					$productRow['v_attribute_' . $realCount . '_sort'] = $attribute['sort_order'];
				}
			}
		}
	}
	
	public function DataImportBeforeSave(&$items, &$Product){
		$ProductsAttributes =& $Product->ProductsAttributes;
		$ProductsAttributes->delete();
		if (isset($items['v_attribute_1']) && !empty($items['v_attribute_1'])){
			$end = false;
			$count = 1;
			while($end === false){
				if (!isset($items['v_attribute_' . $count])){
					$end = true;
					continue;
				}
				
				if (empty($items['v_attribute_' . $count])){
					$count++;
					continue;
				}
				
				$crumb = explode('>', $items['v_attribute_' . $count]);
				$image = $items['v_attribute_' . $count . '_image'];
				$views = $items['v_attribute_' . $count . '_views'];
				$price = $items['v_attribute_' . $count . '_price'];
				$sort = $items['v_attribute_' . $count . '_sort'];
				
				$optionName = $crumb[0];
				$valueName = $crumb[1];
				if (sizeof($crumb) > 2){
					$groupName = $crumb[0];
					$optionName = $crumb[1];
					$valueName = $crumb[2];
				}
				
				$Query = Doctrine_Query::create()
				->select('o.products_options_id, v2o.products_options_values_id')
				->from('ProductsOptions o')
				->leftJoin('o.ProductsOptionsDescription od')
				->leftJoin('o.ProductsOptionsValuesToProductsOptions v2o')
				->leftJoin('v2o.ProductsOptionsValues ov')
				->leftJoin('ov.ProductsOptionsValuesDescription ovd')
				->where('od.products_options_name = ?', $optionName)
				->andWhere('ovd.products_options_values_name = ?', $valueName);
				if (isset($groupName)){
					$Query->addSelect('o2g.products_options_groups_id')
					->leftJoin('o.ProductsOptionsToProductsOptionsGroups o2g')
					->leftJoin('o2g.ProductsOptionsGroups og')
					->andWhere('og.products_options_groups_name = ?', $groupName);
				}
				
				$Result = $Query->fetchOne();
				if ($Result){
					$attribute = $Result->toArray();
					//print_r($attribute);exit;
					if (!isset($attributeCount)) $attributeCount = 0;
					
					$ProductsAttributes[$attributeCount]->groups_id = (isset($groupName) ? $attribute['ProductsOptionsToProductsOptionsGroups'][0]['products_options_groups_id'] : null);
					$ProductsAttributes[$attributeCount]->options_id = $attribute['products_options_id'];
					$ProductsAttributes[$attributeCount]->options_values_id = $attribute['ProductsOptionsValuesToProductsOptions'][0]['products_options_values_id'];
					$ProductsAttributes[$attributeCount]->options_values_image = $image;
					$ProductsAttributes[$attributeCount]->options_values_price = abs($price);
					$ProductsAttributes[$attributeCount]->price_prefix = ($price >= 0 ? '+' : '-');
					$ProductsAttributes[$attributeCount]->sort_order = $sort;
					
					if (!empty($views)){
						$parts = explode(';', $views);
						$ProductsAttributesViews =& $ProductsAttributes[$attributeCount]->ProductsAttributesViews;
						$ProductsAttributesViews->delete();
						foreach($parts as $i => $viewInfo){
							if (empty($viewInfo)) continue;
							
							$infoArr = explode(':', $viewInfo);
							$viewName = $infoArr[0];
							$viewImage = $infoArr[1];
							
							$ProductsAttributesViews[$i]->view_name = $viewName;
							$ProductsAttributesViews[$i]->view_image = $viewImage;
						}
					}
					$attributeCount++;
				}
				$count++;
			}
		}
	}
}
?>