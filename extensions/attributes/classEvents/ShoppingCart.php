<?php
	class ShoppingCart_attributes {

		public function __construct($inputKey){
			$this->inputKey = $inputKey;
		}

		public function init(){
			EventManager::attachEvents(array(
				'AddToCartPrepare',
				'AddToCartAllow',
				'AddToCartBeforeAction',
				'UpdateProductPrepare'
			), 'ShoppingCart', $this);
		}
		
		public function AddToCartBeforeAction(&$pID_info, &$pInfo, &$cartProduct){
			if (isset($pID_info['attributes'])){
				$pInfo['attributes'] = $pID_info['attributes'];
			}
		}
		
		public function AddToCartPrepare(&$pID_strings){
			global $messageStack;
			$pID = attributesUtil::getProductId($pID_strings[0]['id']);
			$purchaseType = $pID_strings[0]['purchaseType'];
			
			if (array_key_exists($this->inputKey, $_POST) && array_key_exists($purchaseType, $_POST[$this->inputKey])){
				if (array_key_exists(3, $_POST[$this->inputKey][$purchaseType])){
					$old_pID_string = $pID_strings[0];
					$pID_strings = array();
				
					$Attributes = $_POST[$this->inputKey][$purchaseType];
					foreach($_POST[$this->inputKey][$purchaseType][3] as $valueId => $qty){
						if ($qty[0] > 0){
							$Attributes[3] = $valueId;
							$pID_strings[] = array(
								'id'           => attributesUtil::getProductIdString($pID, $Attributes),
								'purchaseType' => $purchaseType,
								'qty'          => $qty[0],
								'attributes'   => $Attributes
							);
						}
					}
				}else{
					$postAttribs = $_POST[$this->inputKey][$purchaseType];
					$pID_strings[0]['id'] = attributesUtil::getProductIdString($pID, $postAttribs);
					$pID_strings[0]['attributes'] = $_POST['id'][$purchaseType];
				}
			}else{
				if (attributesUtil::productHasAttributes($pID, $purchaseType)){
					$messageStack->addSession('pageStack', 'You must select the options in the box.', 'warning');
					tep_redirect(itw_app_link(tep_get_all_get_params(array('action')), 'product', 'info'));
				}else{
					$pID_strings[0]['id'] = $pID;
				}
			}
		}

		public function UpdateProductPrepare(&$pID_string, &$pInfo){
			global $dontRun;
			if (isset($dontRun) && $dontRun === true){
				return;
			}

			$attributes = (isset($_POST[$this->inputKey][$pInfo['purchase_type']][$pID_string]) ? $_POST[$this->inputKey][$pID_string] : false);
			if ($attributes !== false){
				if (is_array($attributes)){
					reset($attributes);
					while(list($option, $value) = each($attributes)){
						$pInfo['attributes'][$option] = $value;
					}
				}
			}
		}

		public function AddToCartAllow($pID_string, $qty, $purchaseType){
			$attributes = (isset($_POST[$this->inputKey][$purchaseType]) ? $_POST[$this->inputKey][$purchaseType] : false);
			if ($attributes !== false){
				$product = new Product(attributesUtil::getProductId($pID_string));
				$purchaseType = $product->getPurchaseType($purchaseType);
				if (is_null($purchaseType) === false){
					$inventoryCls = $purchaseType->getInventoryClass();
					if ($inventoryCls->getControllerName() == 'attribute'){
						$invController = $inventoryCls->getController();
						$invController->setIdString(attributesUtil::getAttributeString($attributes));
						if ($invController->hasInventory() === false){
							if (sysConfig::get('EXTENSION_ATTRIBUTES_CART_CHECK') == 'False'){
								return false;
							}
						}
					}
				}
			}
			return true;
		}
	}
?>