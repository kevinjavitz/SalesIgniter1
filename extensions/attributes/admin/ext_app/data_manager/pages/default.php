<?php
/*
	Prouct Attributes Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/


class attributes_admin_data_manager_default extends Extension_attributes {
	var $mapValues = array();
	var $mapOptions = array();
	var $productsRow = array();
	var $productsAttributes = array();
	var $nrGroups = 0;
	var $nrValues = 0;
	public function __construct(){
		parent::__construct();

		$mostAttributes = 0;
		$Qattributes = Doctrine_Query::create()
			->select('count(products_options_id) as total')
			->from('ProductsOptionsToProductsOptionsGroups')
			->groupBy('products_options_groups_id')
			->where('products_options_id > ?', '0')
			->andWhere('products_options_groups_id  > ?', '0')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		foreach($Qattributes as $aTotal){
			if ($aTotal['total'] > $mostAttributes){
				$mostAttributes = $aTotal['total'];
			}
		}
		$this->nrGroups = $mostAttributes + 1;

		$mostAttributes = 0;
		$Qattributes = Doctrine_Query::create()
			->select('count(products_options_values_id) as total')
			->from('ProductsOptionsValuesToProductsOptions')
			->groupBy('products_options_id')
			->where('products_options_id > ?', '0')
			->andWhere('products_options_values_id  > ?', '0')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		foreach($Qattributes as $aTotal){
			if ($aTotal['total'] > $mostAttributes){
				$mostAttributes = $aTotal['total'];
			}
		}

		$this->nrValues = $mostAttributes+1;

		$Qattributes = Doctrine_Query::create()
			->from('Products p')
			->leftJoin('p.ProductsAttributes a')
			->leftJoin('a.ProductsAttributesViews av')
			->leftJoin('a.ProductsOptionsGroups og')
			->leftJoin('a.ProductsOptions o')
			->leftJoin('o.ProductsOptionsToProductsOptionsGroups o2g')
			->leftJoin('o.ProductsOptionsDescription od')
			->leftJoin('a.ProductsOptionsValues ov')
			->leftJoin('ov.ProductsOptionsValuesToProductsOptions v2o')
			->leftJoin('ov.ProductsOptionsValuesDescription ovd')
			->orderBy('o2g.sort_order, v2o.sort_order')
			->execute()->toArray();



		$lID = Session::get('languages_id');
		foreach($Qattributes as $mainAttributes){
			$u = 0;
			$attr = array();
			$mapOptions = array();
			$options = array();
			$groupName = '';
			foreach($mainAttributes['ProductsAttributes'] as $attribute){
				$groupName = isset($attribute['ProductsOptionsGroups']['products_options_groups_name'])?$attribute['ProductsOptionsGroups']['products_options_groups_name']:'';
				$productRow['v_attribute_group'] = $groupName;

				//foreach($attribute['ProductsOptions'] as $j => $option){
				if(!isset($mapOptions[$attribute['ProductsOptions']['products_options_id']])){
					$mapOptions[$attribute['ProductsOptions']['products_options_id']] = count($mapOptions) + 1;
					$p = count($mapOptions);
				}else{
					$p = $mapOptions[$attribute['ProductsOptions']['products_options_id']];
				}

				/*if(!isset($mapValues[$attribute['ProductsOptionsValues']['products_options_values_id']])){
							$mapValues[$attribute['ProductsOptionsValues']['products_options_values_id']] = count($mapValues) + 1;
							$u = count($mapValues);
						}else{
							$u = $mapValues[$attribute['ProductsOptionsValues']['products_options_values_id']];
						} */
				$views = array();
				if (array_key_exists('ProductsAttributesViews', $attribute)){
					foreach($attribute['ProductsAttributesViews'] as $viewInfo){
						$views[] = $viewInfo['view_name'] . ':' . $viewInfo['view_image'];
					}
				}

				$u++;
				$attr[$u] = array(
					'option' => $p,
					'name' => $attribute['ProductsOptionsValues']['ProductsOptionsValuesDescription'][$lID]['products_options_values_name'],
					'image' => $attribute['options_values_image'],
					'views' => implode(';', $views),
					'price' => $attribute['options_values_price'],
					'purchasetypes' => $attribute['purchase_types'],
					'useinventory' => $attribute['use_inventory'],
					'sort' => $attribute['ProductsOptionsValues']['ProductsOptionsValuesToProductsOptions'][0]['sort_order']);

				$options[$p] = array('name'=> $attribute['ProductsOptions']['ProductsOptionsDescription'][$lID]['products_options_name'],
					'sort'=> $attribute['ProductsOptions']['ProductsOptionsToProductsOptionsGroups'][0]['sort_order']);

				//$productsAttributes[$attribute['products_id']][$u] = $attr;
			}
			$this->productsRow[$mainAttributes['products_id']] = array('group' => $groupName, 'options' => $options,'attributes' => $attr);
		}

		/*foreach($productsRow as $product_id => $options){
		   $tempOptions = array();
		   foreach($options as $k => $v){
				if($k >= $nrGroups){
					$p = $k-1;
					while($p > 0){
						if(!isset($options[$p])){
							$tempOptions[$p] = $options[$k];
							break;
						}
						$p--;
					}
				}else{
					$tempOptions[$k] = $v;
				}
		   }
			for($i=1;$i<$nrGroups;$i++ ){
				if(!isset($tempOptions[$i])){
					$tempOptions[$i] = array('name' =>'','sort' => '');
				}
			}
			$productsRow[$product_id] = $tempOptions;
		} */

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
		global $nrGroups, $nrValues;

		for($i=1; $i<$this->nrGroups; $i++){
			$dataExport->setHeaders(array(
					'v_option_' . $i,
					'v_option_' . $i . '_sort'
				));
		}

		if($this->nrGroups - 1 > 0){
			$dataExport->setHeaders(array(
					'v_attribute_group',
				));
		}
			
		for($i=1; $i<$this->nrValues; $i++){
			for($j=1; $j<$this->nrGroups; $j++){
				$dataExport->setHeaders(array(
					'v_attribute_' . $i.'_option_'.$j,
					'v_attribute_' . $i .'_option_'.$j. '_image',
					'v_attribute_' . $i .'_option_'.$j. '_views',
					'v_attribute_' . $i .'_option_'.$j. '_price',
					'v_attribute_' . $i .'_option_'.$j. '_purchasetypes',
					'v_attribute_' . $i .'_option_'.$j. '_useinventory',
					'v_attribute_' . $i .'_option_'.$j. '_sort'
				));
			}
		}
	}
	
	public function DataExportBeforeFileLineCommit(&$productRow){
		 if(isset($this->productsRow[$productRow['products_id']])){
			 $valArr = $this->productsRow[$productRow['products_id']];
			 $productRow['v_attribute_group'] = $valArr['group'];
			 foreach($valArr['options'] as $k => $v){
				 $productRow['v_option_' .$k] = $v['name'];
				 $productRow['v_option_' .$k .'_sort'] = $v['sort'];
			 }
			$countArr = array();
			 foreach($valArr['attributes'] as $v){
				 $p = $v['option'];
				 if(isset($countArr[$p])){
				    $countArr[$p]++;
				 }else{
					$countArr[$p] = 1;
				 }
				 $realCount = $countArr[$p].'_option_'.$p;
				 $productRow['v_attribute_' . $realCount] = $v['name'];
				 $productRow['v_attribute_' .  $realCount . '_image'] = $v['image'];
				 $productRow['v_attribute_' .  $realCount . '_views'] = $v['views'];
				 $productRow['v_attribute_' .  $realCount . '_price'] = $v['price'];
				 $productRow['v_attribute_' .  $realCount . '_purchasetypes'] = $v['purchasetypes'];
				 $productRow['v_attribute_' .  $realCount . '_useinventory'] = $v['useinventory'];
				 $productRow['v_attribute_' .  $realCount . '_sort'] = $v['sort'];

			 }

		 }
	}
	
	public function DataImportBeforeSave(&$items, &$Product){
		$ProductsAttributes =& $Product->ProductsAttributes;
		$ProductsAttributes->delete();
		$countOpt = 1;
		if (isset($items['v_attribute_group']) && !empty($items['v_attribute_group'])){
			$groupName = $items['v_attribute_group'];
		}
		$isAttribute = false;
		while(true){
			if (!isset($items['v_option_' . $countOpt])){
				break;
			}

			if (empty($items['v_option_' . $countOpt])){
				$countOpt++;
				continue;
			}

			$optionName = $items['v_option_' . $countOpt];

			$count = 1;
			$optCount = '_option_' . $countOpt;
			while(true){

				if (!isset($items['v_attribute_' . $count . $optCount])){
					break;
				}

				if (empty($items['v_attribute_' . $count . $optCount])){
					$count++;
					continue;
				}
				$image = $items['v_attribute_' . $count . $optCount . '_image'];
				$views = $items['v_attribute_' . $count . $optCount . '_views'];
				$price = $items['v_attribute_' . $count . $optCount . '_price'];
				$purchase_types = $items['v_attribute_' . $count . $optCount . '_purchasetypes'];
				$use_inventory = $items['v_attribute_' . $count . $optCount . '_useinventory'];
				$sort = $items['v_attribute_' . $count . $optCount . '_sort'];
				$valueName = $items['v_attribute_' . $count . $optCount];

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
					if (!isset($attributeCount)) {
						$attributeCount = 0;
					}

					$ProductsAttributes[$attributeCount]->groups_id = (isset($groupName)
						? $attribute['ProductsOptionsToProductsOptionsGroups'][0]['products_options_groups_id'] : null);
					$ProductsAttributes[$attributeCount]->options_id = $attribute['products_options_id'];
					$ProductsAttributes[$attributeCount]->options_values_id = $attribute['ProductsOptionsValuesToProductsOptions'][0]['products_options_values_id'];
					$ProductsAttributes[$attributeCount]->options_values_image = $image;
					$ProductsAttributes[$attributeCount]->options_values_price = abs($price);
					$ProductsAttributes[$attributeCount]->purchase_types = $purchase_types;
					$ProductsAttributes[$attributeCount]->use_inventory = $use_inventory;
					$ProductsAttributes[$attributeCount]->price_prefix = ($price >= 0 ? '+' : '-');
					$ProductsAttributes[$attributeCount]->sort_order = $sort;
					$isAttribute = true;
					if (!empty($views)){
						$parts = explode(';', $views);
						$ProductsAttributesViews =& $ProductsAttributes[$attributeCount]->ProductsAttributesViews;
						$ProductsAttributesViews->delete();
						foreach($parts as $i => $viewInfo){
							if (empty($viewInfo)) {
								continue;
							}

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
			$countOpt++;
		}
		if ($isAttribute){
			$Product->products_inventory_controller = 'attribute';
		}
	}

}
?>