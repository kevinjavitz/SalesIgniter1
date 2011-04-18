<?php
	class ShoppingCartProduct_productDesigner {

		public function __construct(){
		}

		public function init(){
			EventManager::attachEvents(array(
				//'AddToCart',
				'ProductNameAppend',
				'ProductImageBeforeShow'
			), 'ShoppingCartProduct', $this);
		}
		
		public function ProductNameAppend(&$cartProduct){
			global $currencies, $appExtension;
			$extDesigner = $appExtension->getExtension('productDesigner');
			if ($cartProduct->hasInfo('predesign')){
				$img = '';
				$dataArray = $cartProduct->getInfo('predesign');
				unset($dataArray['images_id']);
				if (isset($dataArray['front'])){
					$img .= '<br /><small>-&nbsp;<i>Front Design' . $extDesigner->getPredesignCostStr('front', $dataArray) . ':</i></small><br /><img src="' . $extDesigner->buildProductPredesignUrl('front', $dataArray) . '" />';
				}
				if (isset($dataArray['back'])){
					$img .= '<br /><small>-&nbsp;<i>Back Design' . $extDesigner->getPredesignCostStr('back', $dataArray) . ':</i></small><br /><img src="' . $extDesigner->buildProductPredesignUrl('back', $dataArray) . '" />';
				}
				return $img;
			}
		
			if ($cartProduct->hasInfo('custom_design')){
				$img = '<br /><small>-&nbsp;<i>Design:</i></small><br /><img src="' . $this->buildProductDesignUrl($cartProduct->getIdString(), $cartProduct->getPurchaseType(), $cartProduct->getInfo('custom_design')) . '" />';
				return $img;
			}
		}
		
		public function ProductImageBeforeShow(&$image, &$product){
			global $appExtension;
			$extDesigner = $appExtension->getExtension('productDesigner');
			if ($product->hasInfo('predesign')){
				$predesign = $product->getInfo('predesign');
				$predesign['products_id'] = $product->getIdString();
			
				$image = $extDesigner->buildProductPredesignUrl('front', $predesign);
			}elseif ($product->hasInfo('custom_design')){
				$image = $extDesigner->buildProductDesignUrl($product->getIdString(), $product->getPurchaseType(), $product->getInfo('custom_design'), 1);
			}
		}

		public function AddToCart(&$pInfo, &$productClass, &$purchaseTypeClass){
		}
	}
?>