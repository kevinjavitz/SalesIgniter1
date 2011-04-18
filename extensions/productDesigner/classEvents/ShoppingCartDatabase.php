<?php
	class ShoppingCartDatabaseActions_productDesigner {

		public function __construct(){
		}

		public function init(){
			EventManager::attachEvents(array(
				'InsertBasketBeforeProcess',
				'GetCartFromDatabase'
			), 'ShoppingCartProduct', $this);
		}
		
		public function InsertBasketBeforeProcess(&$insert, &$pInfo){
			if (isset($pInfo['predesign'])){
				$insert->design_info = serialize($pInfo['predesign']);
			}elseif (isset($pInfo['custom_design'])){
				$insert->design_info = serialize($pInfo['custom_design']);
			}
		}
		
		public function GetCartFromDatabase(&$contents, &$basketId, &$product){
			if (!empty($product['design_info'])){
				$designInfo = unserialize($product['design_info']);
				if (isset($designInfo['front'])){
					$contents['predesign'] = unserialize($product['design_info']);
				}else{
					$contents['custom_design'] = unserialize($product['design_info']);
				}
			}
		}
	}
?>