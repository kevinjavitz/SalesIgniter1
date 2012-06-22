<?php
	class ShoppingCartProduct_attributes {

		public function __construct($inputKey){
			$this->inputKey = $inputKey;
		}

		public function init(){
			EventManager::attachEvents(array(
				'AddToCart',
				'ProductNameAppend'
			), 'ShoppingCartProduct', $this);
		}
		
		public function ProductNameAppend(&$cartProduct){
			$pID_string = $cartProduct->getIdString();
			$langId = Session::get('languages_id');
			$return = '';
			// Push all attributes information in an array
			if ($cartProduct->hasInfo('attributes')){
				$attributes = $cartProduct->getInfo('attributes');
				if (is_array($attributes)){
					while (list($option, $value) = each($attributes)) {
						$attribute = attributesUtil::getAttributes((int)$pID_string, (int)$option, (int)$value);
						$attribute = $attribute[0];
						/*
						$productArray[$option]['products_options_name'] = $attribute['ProductsOptions']['ProductsOptionsDescription'][$langId]['products_options_name'];
						$productArray[$option]['options_values_id'] = $value;
						$productArray[$option]['products_options_values_name'] = $attribute['ProductsOptionsValues']['ProductsOptionsValuesDescription'][$langId]['products_options_values_name'];
						$productArray[$option]['options_values_price'] = $attribute['options_values_price'];
						$productArray[$option]['price_prefix'] = $attribute['price_prefix'];
						*/
						if (isset($attribute['ProductsOptions']['ProductsOptionsDescription'][$langId]) && isset($attribute['ProductsOptionsValues']['ProductsOptionsValuesDescription'][$langId])){
							$return .= '<br><small><i> - ' .
							(!empty($attribute['ProductsOptions']['ProductsOptionsDescription'][$langId]['products_options_front_name'])?$attribute['ProductsOptions']['ProductsOptionsDescription'][$langId]['products_options_front_name']:$attribute['ProductsOptions']['ProductsOptionsDescription'][$langId]['products_options_name']) . ': ' .
							(!empty($attribute['ProductsOptionsValues']['ProductsOptionsValuesDescription'][$langId]['products_options_front_values_name'])?$attribute['ProductsOptionsValues']['ProductsOptionsValuesDescription'][$langId]['products_options_front_values_name']:$attribute['ProductsOptionsValues']['ProductsOptionsValuesDescription'][$langId]['products_options_values_name']) .
							'</i></small>' . tep_draw_hidden_field($this->inputKey . '[' . $pID_string . '][' . $option . ']', $value);
						}
					}
				}
			}
			return $return;
		}

		public function AddToCart(&$pInfo, &$productClass, &$purchaseTypeClass){
			$attributes = false;
			if (isset($pInfo['attributes'])){
				$attributes = $pInfo['attributes'];
				unset($pInfo['attributes']);
			}elseif (isset($_POST[$this->inputKey][$pInfo['purchase_type']])){
				$attributes = $_POST[$this->inputKey][$pInfo['purchase_type']];
			}
			
			if ($attributes !== false){
				if (is_array($attributes)){
					reset($attributes);
					while(list($option, $value) = each($attributes)){
						$pInfo['attributes'][$option] = $value;
					}

					$pInfo['id_string'] = attributesUtil::getProductIdString($pInfo['id_string'], $pInfo['attributes']);
					$pInfo['aID_string'] = attributesUtil::getAttributeString($pInfo['attributes']);
					$pInfo['final_price'] += attributesUtil::getAttributesPrice($pInfo['id_string'], $pInfo['purchase_type'], $pInfo['attributes']);
				}
			}
		}
	}
?>