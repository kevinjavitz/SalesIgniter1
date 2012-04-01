<?php
class attributes_admin_orderCreator_default_new extends Extension_attributes {
	
	public function __construct(){
		parent::__construct();
	}

	public function load(){
		global $App;
		if ($this->isEnabled() === false) return;
		
		EventManager::attachEvents(array(
			'OrderCreatorAddProductToEmail',
			'OrderCreatorProductAddToCollection'
		), null, $this);
	}
	
	public function OrderCreatorAddProductToEmail($opInfo, &$products_ordered){
		global $currencies;
		if (isset($opInfo->OrdersProductsAttributes)){
			foreach($opInfo->OrdersProductsAttributes as $aInfo){
				$products_ordered .= $aInfo->products_options . ': ' . $aInfo->products_options_values;
				if ($aInfo->options_values_price > 0){
					$products_ordered .= ' ( ' . $aInfo->price_prefix . ' ' . $currencies->format($aInfo->options_values_price) . ' )'."\n";
				}
			}
		}
	}
	
	public function OrderCreatorProductAddToCollection($Product, &$OrderedProduct){
		if ($Product->hasInfo('attributes')){
			$langId = Session::get('languages_id');
			$Attributes = $Product->getInfo('attributes');
			foreach($Attributes as $optionId => $oInfo){
				$aInfo = attributesUtil::getAttributes(
					(int) $Product->getProductsId(),
					(int) $optionId,
					(int) $oInfo['value']
				);
				if (isset($aInfo[0]['ProductsOptions']['ProductsOptionsDescription'][$langId]) && isset($aInfo[0]['ProductsOptionsValues']['ProductsOptionsValuesDescription'][$langId])){
					$Attribute = new OrdersProductsAttributes();
					$Attribute->options_values_price = $oInfo['price'];
					$Attribute->price_prefix = $oInfo['prefix'];
					$Attribute->options_id = $optionId;
					$Attribute->options_values_id = $oInfo['value'];
					$Attribute->products_options = $aInfo[0]['ProductsOptions']['ProductsOptionsDescription'][$langId]['products_options_name'];
					$Attribute->products_options_values = $aInfo[0]['ProductsOptionsValues']['ProductsOptionsValuesDescription'][$langId]['products_options_values_name'];
					$OrderedProduct->OrdersProductsAttributes->add($Attribute);
				}
				

			}
		}
	}
}
?>