<?php
if(!class_exists('Product')){
	require(sysConfig::getDirFsCatalog() . 'includes/classes/product.php');
}
	
	class OrderCreatorProductProduct extends Product {
		
		public function getPurchaseType($typeName, $forceEnable = false){
			global $appExtension;
			$className = 'OrderCreatorProductPurchaseType' . ucfirst($typeName);
			if (!class_exists($className)){
				$purchaseTypesPath = 'classes/product/purchase_types/';
				$baseFilePath = sysConfig::getDirFsCatalog() . 'extensions/orderCreator/admin/' . $purchaseTypesPath;
				if (file_exists($baseFilePath . $typeName . '.php')){
					require($baseFilePath . $typeName . '.php');
				}else{
					$extFilePath = sysConfig::getDirFsCatalog() . 'extensions/';
					$Exts = $appExtension->getExtensions();
					foreach($Exts as $extName => $extCls){
						if (file_exists($extFilePath . $extName . '/catalog/' . $purchaseTypesPath . $typeName . '.php')){
							require($extFilePath . $extName . '/catalog/' . $purchaseTypesPath . $typeName . '.php');
							break;
						}
					}
				}
			}
				
			$purchaseType = null;
			if (class_exists($className)){
				$purchaseType = new $className($this, $forceEnable);
			}
			
			if (is_null($purchaseType)){
				$purchaseType = parent::getPurchaseType($typeName, $forceEnable);
			}
			
			return $purchaseType;
		}
	}
?>