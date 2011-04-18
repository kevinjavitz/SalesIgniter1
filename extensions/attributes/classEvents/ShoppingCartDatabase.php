<?php
	class ShoppingCartDatabase_attributes {

		public function __construct($inputKey){
			$this->inputKey = $inputKey;
		}

		public function init(){
			EventManager::attachEvents(array(
				'InsertBasketBeforeProcess',
				'GetCartFromDatabase'
			), 'ShoppingCartDatabase', $this);
		}
		
		public function InsertBasketBeforeProcess(&$insert, &$pInfo){
			if (isset($pInfo['attributes']) && is_array($pInfo['attributes'])){
				$idx = 0;
				foreach($pInfo['attributes'] as $oID => $vID){
					$insert->CustomersBasketAttributes[$idx]->products_options_id = $oID;
					$insert->CustomersBasketAttributes[$idx]->products_options_value_id = $vID;
					$idx++;
				}
			}
		}
		
		public function GetCartFromDatabase(&$cartContent, $basketId, $product){
			$Qattribute = Doctrine_Query::create()
			->select('ba.products_options_id, ba.products_options_value_id')
			->from('CustomersBasketAttributes ba')
			->where('ba.customers_basket_id = ?', $basketId)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			foreach($Qattribute as $aInfo){
				$cartContent['attributes'][$aInfo['products_options_id']] = $aInfo['products_options_value_id'];
			}
		}
	}
?>