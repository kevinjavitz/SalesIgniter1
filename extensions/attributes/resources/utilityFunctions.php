<?php
	class attributesUtil {

		public static function getAttributes($pId = null, $optionId = null, $valueId = null, $purchaseType = null, $usesInventory = null){
			$Query = Doctrine_Query::create()
			->from('ProductsAttributes a')
			->leftJoin('a.ProductsAttributesViews v')
			->leftJoin('a.ProductsOptions o')
			->leftJoin('o.ProductsOptionsDescription od')
			->leftJoin('a.ProductsOptionsValues ov')
			->leftJoin('ov.ProductsOptionsValuesDescription ovd')
			->leftJoin('ov.ProductsOptionsValuesToProductsOptions v2o')
			->orderBy('a.sort_order, v2o.sort_order');

			if (is_null($pId) === false){
				$Query->andWhere('a.products_id = ?', (int)$pId);
			}

			if (is_null($optionId) === false){
				$Query->andWhere('a.options_id = ?', (int)$optionId);
			}

			if (is_null($valueId) === false){
				$Query->andWhere('a.options_values_id = ?', (int)$valueId);
			}

			if (is_null($purchaseType) === false){
				$Query->andWhere('FIND_IN_SET("' . $purchaseType . '", a.purchase_types) > 0');
			}

			if (is_null($usesInventory) === false){
				$Query->andWhere('a.use_inventory = ?', ($usesInventory === true ? '1' : '0'));
			}

			$Result = $Query->execute()->toArray();
			return $Result;
		}

		public static function insertOrderedProductAttribute($attribute){
			global $order;
			$langId = Session::get('languages_id');

			$OrdersProductsAttributes = Doctrine_Core::getTable('OrdersProductsAttributes');

			$newRecord = $OrdersProductsAttributes->create();

			$newRecord->orders_id = $order->newOrder['orderID'];
			$newRecord->orders_products_id = $order->newOrder['currentOrderedProduct']['id'];
			$newRecord->options_id = $attribute['ProductsOptions']['products_options_id'];
			$newRecord->options_values_id = $attribute['ProductsOptionsValues']['products_options_values_id'];
			if (isset($attribute['ProductsOptions']['ProductsOptionsDescription'][$langId])){
				$newRecord->products_options = $attribute['ProductsOptions']['ProductsOptionsDescription'][$langId]['products_options_name'];
			}else{
				$newRecord->products_options = 'attribute_name_not_defined_for_language';
			}
			if (isset($attribute['ProductsOptionsValues']['ProductsOptionsValuesDescription'][$langId])){
				$newRecord->products_options_values = $attribute['ProductsOptionsValues']['ProductsOptionsValuesDescription'][$langId]['products_options_values_name'];
			}else{
				$newRecord->products_options_values  = 'attribute_value_not_defined_for_language';
			}
			$newRecord->options_values_price = $attribute['options_values_price'];
			$newRecord->price_prefix = $attribute['price_prefix'];

			$newRecord->save();
		}

		public static function productHasAttributes($productId, $purchaseType){
			$Qcheck = Doctrine_Query::create()
			->select('count(*) as total')
			->from('ProductsAttributes')
			->where('products_id = ?', $productId)
			->andWhere('FIND_IN_SET("' . $purchaseType . '", purchase_types) > 0')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if ($Qcheck){
				return ($Qcheck[0]['total'] > 0);
			}
			return false;
		}

		public static function getAttributesPrice($pID_string, $purchaseType, $attributes = null){
			$attributes_price = 0;
			if (is_null($attributes) === false) {
				reset($attributes);
				while (list($option, $value) = each($attributes)){
					$attribute = self::getAttributes((int)self::getProductId($pID_string), (int)$option, (int)$value, $purchaseType);
					if ($attribute){
						$attribute = $attribute[0];
						if ($attribute['price_prefix'] == '+') {
							$attributes_price += $attribute['options_values_price'];
						} else {
							$attributes_price -= $attribute['options_values_price'];
						}
					}
				}
			}
			return $attributes_price;
		}

		public static function organizeAttributeArray($ProductsAttributes){
			$Attributes = array();
			foreach($ProductsAttributes as $attribute){
				if (!array_key_exists($attribute['options_id'], $Attributes)){
					$Attributes[$attribute['options_id']] = array(
					'options_name'          => $attribute['ProductsOptions']['ProductsOptionsDescription'][Session::get('languages_id')]['products_options_name'],
					'option_type'           => $attribute['ProductsOptions']['option_type'],
					'use_image'             => $attribute['ProductsOptions']['use_image'],
					'use_multi_image'       => $attribute['ProductsOptions']['use_multi_image'],
					'update_product_image'  => $attribute['ProductsOptions']['update_product_image'],
					'ProductsOptionsValues' => array()
					);
				}

				$curArray = array(
				'options_values_id'    => $attribute['options_values_id'],
				'options_values_price' => $attribute['options_values_price'],
				'options_values_name'  => $attribute['ProductsOptionsValues']['ProductsOptionsValuesDescription'][Session::get('languages_id')]['products_options_values_name'],
				'price_prefix'         => $attribute['price_prefix']
				);

				if ($attribute['ProductsOptions']['use_image'] == '1'){
					if ($attribute['ProductsOptions']['use_multi_image'] == '1'){
						$curArray['ProductsAttributesViews'] = array();
						foreach($attribute['ProductsAttributesViews'] as $viewInfo){
							$curArray['ProductsAttributesViews'][] = array(
							'view_name' => $viewInfo['view_name'],
							'view_image' => $viewInfo['view_image']
							);
						}
					}else{
						$curArray['options_values_image'] = $attribute['options_values_image'];
					}
				}

				$Attributes[$attribute['options_id']]['ProductsOptionsValues'][] = $curArray;
			}
			return $Attributes;
		}

		public static function getProductId($pID_string){
			$pieces = explode('{', $pID_string);

			if (is_numeric($pieces[0])){
				return $pieces[0];
			}
			return false;
		}

		public static function getProductIdString($pID, $attributes){
			if (is_numeric($pID)){
				$pID_string = $pID;
			}else{
				$pID_string = self::getProductId($pID);
			}

			if (is_numeric($pID_string)){
				$attributes_check = true;
				$attributes_ids = '';
				if (strpos($pID, '{') !== false){
					$result = self::getAttributeString($pID);
				}else{
					$result = self::getAttributeString($attributes);
				}

				if ($result !== false){
					$pID_string .= $result;
				}
			}else{
				return false;
			}
			return $pID_string;
		}

		public static function getAttributeString($aID_string){
			$attributes_ids = '';
			if (!empty($aID_string)){
				if (is_array($aID_string)){
					reset($aID_string);
					while (list($option, $value) = each($aID_string)){
						if (is_numeric($option) && is_numeric($value)){
							$attributes_ids .= '{' . (int)$option . '}' . (int)$value;
						}else{
							return false;
							break;
						}
					}
				}else{
					// strpos()+1 to remove up to and including the first { which would create an empty array element in explode()
					$attributes = self::splitStringToArray($aID_string);
					foreach($attributes as $k => $v){
						if (is_numeric($k) && is_numeric($v)){
							$attributes_ids .= '{' . (int)$k . '}' . (int)$v;
						}else{
							return false;
							break;
						}
					}
				}
			}
			return $attributes_ids;
		}

		public static function splitStringToArray($aID_string){
			$attributeArray = array();
			$aID_array = explode('{', substr($aID_string, strpos($aID_string, '{')+1));
			foreach($aID_array as $k => $v){
				$pair = explode('}', $v);
				if(isset($pair[0]) && isset($pair[1])){
					$attributeArray[(int)$pair[0]] = (int)$pair[1];
				}
			}
			return $attributeArray;
		}

		public static function permutateAttributesFromString($aID_string){
			$arr = self::splitAttributeString($aID_string);
			//print_r($arr);
			return getArrayPermutation($arr);
		}

		public static function splitAttributeString($aID_string){
			$attributeArray = array();
			$attributes = self::splitStringToArray($aID_string);
			foreach($attributes as $k => $v){
				$attributeArray[] = '{' . (int)$k . '}' . (int)$v;
			}
			return $attributeArray;
		}
	}
?>