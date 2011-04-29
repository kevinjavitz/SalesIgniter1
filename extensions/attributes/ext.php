<?php
/*
  Prouct Attributes Extension Version 1

  I.T. Web Experts, Rental Store v2
  http://www.itwebexperts.com

  Copyright (c) 2009 I.T. Web Experts

  This script and it's source is not redistributable
 */
require(dirname(__FILE__) . '/resources/utilityFunctions.php');
class Extension_attributes extends ExtensionBase {

	public function __construct(){
		parent::__construct('attributes');
		$this->inputKey = 'id';
		$this->validSearchKeys = array();
	}

	public function init(){
		global $App, $appExtension, $Template;
		if ($this->enabled === false)
			return;

		EventManager::attachEvents(array(
				'ProductQueryBeforeExecute',
				'OrderQueryBeforeExecute',
				'CheckoutProcessInsertOrderedProduct',
				'OrderClassLoadProduct',
				'OrderClassQueryFillProductArray',
				'ProductInfoBeforeDescription',
				'CheckoutProductAfterProductName',
				'OrderProductAfterProductName',
				'OrderProductAfterProductNameEdit',
				'ProductSearchValidateKey',
				'ProductSearchAddValidKeys',
				'ProductSearchQueryBeforeExecute',
				'SearchBoxAddGuidedOptions'
				), null, $this);

		/*
		 * Shopping Cart Actions --BEGIN--
		 */
		require(dirname(__FILE__) . '/classEvents/ShoppingCart.php');
		$eventClass = new ShoppingCart_attributes($this->inputKey);
		$eventClass->init();

		require(dirname(__FILE__) . '/classEvents/ShoppingCartProduct.php');
		$eventClass = new ShoppingCartProduct_attributes($this->inputKey);
		$eventClass->init();

		require(dirname(__FILE__) . '/classEvents/ShoppingCartDatabase.php');
		$eventClass = new ShoppingCartDatabase_attributes($this->inputKey);
		$eventClass->init();

		if ($appExtension->isAdmin()){
			EventManager::attachEvents(array(
					'BoxCatalogAddLink'
					), null, $this);
		}
		if($appExtension->isCatalog()){
			EventManager::attachEvent('ProductListingProductsBeforeShowPrice', null, $this);
		}
	}

	public function SearchBoxAddGuidedOptions(&$boxContent, $optionId){
		global $appExtension;
		$searchItemDisplay = 4;
		if ($appExtension->isInstalled('attributes') && $appExtension->isEnabled('attributes')){
			$ProductsAttributes = attributesUtil::organizeAttributeArray(attributesUtil::getAttributes(null, $optionId));
			foreach($ProductsAttributes as $optionId => $aInfo){
				$count = 0;
				$added = array();
				$dropArray = array();
				foreach($aInfo['ProductsOptionsValues'] as $vInfo){
					if (!in_array($vInfo['options_values_id'], $added)){
						$dropArray[] = array(
							'id' => $vInfo['options_values_id'],
							'text' => $vInfo['options_values_name']
						);
						$added[] = $vInfo['options_values_id'];
					}
				}

				foreach($dropArray as $attrInfo){
					$QproductCount = Doctrine_Query::create()
							->select('count(*) as total')
							->from('ProductsAttributes')
							->where('options_id = ?', $optionId)
							->andWhere('options_values_id = ?', $attrInfo['id'])
							->andWhere('purchase_types is not NULL and purchase_types <> "" ')
							->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

					$checkIcon = '<span class="ui-icon ui-icon-check" style="display:inline-block;height:14px;background:none;"></span>';
					$link = itw_app_link(tep_get_all_get_params(array('values[' . $optionId . ']', 'options[' . $optionId . ']')) . 'options[' . $optionId . ']=' . $optionId . '&values[' . $optionId . ']=' . $attrInfo['id'], 'products', 'search_result');
					if(isset($_GET['options'][$optionId]) && isset($_GET['values'][$optionId]) && $_GET['values'][$optionId] == $attrInfo['id']){
						$checkIcon = '<span class="ui-icon ui-icon-check" style="display:inline-block;height:14px;"></span>';
						$link = itw_app_link(tep_get_all_get_params(array('values[' . $optionId . ']', 'options[' . $optionId . ']')), 'products', 'search_result');
					}
					$icon = '<span class="ui-widget ui-widget-content ui-corner-all">' .
						$checkIcon .
						'</span>';

					$boxContent .= '<li style="padding-bottom:.3em;' . ($count > $searchItemDisplay ? 'display:none;' : '') . '">' .
					               $icon .
					               ' <a href="' . $link . '" data-url_param="options[' . $optionId . ']=' . $optionId . '&values[' . $optionId . ']=' . $attrInfo['id'] . '">' .
					               $attrInfo['text'] .
					               '</a> (' . $QproductCount[0]['total'] . ')' .
					               '</li>';

					$count++;
				}
				if ($count > $searchItemDisplay){
					$boxContent .= '<li class="searchShowMoreLink"><a href="#"><b>More</b></a></li>';
				}
			}
		}
	}

	public function ProductSearchAddValidKeys(&$validSearchKeys){
		$validSearchKeys[] = 'option_value_id';
	}

	public function ProductSearchValidateKey(&$key, &$totalErrors){
		if ($key == 'option_value_id' && isset($_GET[$key]) && !empty($_GET[$key])){
			$this->validSearchKeys[$key] = $_GET[$key];
		}
	}

	public function ProductSearchQueryBeforeExecute(&$Qproducts){
		$attributeDefined = false;
		if (isset($_GET['options']) && isset($_GET['values'])){
			$queryAdd = array();
			foreach($_GET['options'] as $i => $optionId){
				$valueId = $_GET['values'][$optionId];
				$queryAdd[] = '(pa.options_id = ' . $optionId . ' AND pa.options_values_id = ' . $valueId . ')';
			}
			$attributeDefined = true;
			$Qproducts->addSelect('pa.products_id')
					->leftJoin('p.ProductsAttributes pa')
					->andWhere('(' . implode(' and ', $queryAdd) . ')');
			if(isset($_GET['ptype']) && tep_not_null($_GET['ptype'])){
				$Qproducts->andWhere('FIND_IN_SET(?, pa.purchase_types) > 0', $_GET['ptype']);
			}else{
				$Qproducts->andWhere('pa.purchase_types is not NULL and pa.purchase_types <> "" ');
			}
		}
		if (isset($this->validSearchKeys) && sizeof($this->validSearchKeys) > 0){
			$fieldsSearch = array();
			foreach($this->validSearchKeys as $k => $v){
				$fieldsSearch[] = 'pa.options_values_id = "' . $v . '"';
			}
			if (!$attributeDefined){
				$Qproducts->leftJoin('p.ProductsAttributes pa');
			}
			$Qproducts->andWhere('((' . implode(') or (', $fieldsSearch) . '))');
			$Qproducts->andWhere('pa.groups_id is not NULL');
		}
	}

	public function BoxCatalogAddLink(&$contents){
		$contents['children'][] = array(
			'link' => itw_app_link('appExt=attributes','manage','default','SSL'),
			'text' => sysLanguage::get('BOX_CATALOG_CATEGORIES_PRODUCTS_ATTRIBUTES')
		);
	}

	function CheckoutProcessInsertOrderedProduct(&$cartProduct, &$products_ordered){
		if (!$cartProduct->hasInfo('attributes'))
			return;

		$langId = Session::get('languages_id');
		$attributes = $cartProduct->getInfo('attributes');
		reset($attributes);
		while(list($option, $value) = each($attributes)){
			$attribute = attributesUtil::getAttributes((int) $cartProduct->getIdString(), (int) $option, (int) $value);
			$attribute = $attribute[0];

			attributesUtil::insertOrderedProductAttribute($attribute);
			if (isset($attribute['ProductsOptions']['ProductsOptionsDescription'][$langId]) && isset($attribute['ProductsOptionsValues']['ProductsOptionsValuesDescription'][$langId])){
				$products_ordered .= "\t" .
					$attribute['ProductsOptions']['ProductsOptionsDescription'][$langId]['products_options_name'] .
					': ' .
					$attribute['ProductsOptionsValues']['ProductsOptionsValuesDescription'][$langId]['products_options_values_name']."\n";
			}else{
				$products_ordered .= "\t" . 'attribute_name_not_set_for_language:attribute_value_not_set_for_language';
			}
		}
		$products_ordered .= "\n";
	}

	public function ProductQueryBeforeExecute(&$productQuery){
		$productQuery->addSelect('pa.products_attributes_id')
			->leftJoin('p.ProductsAttributes pa');
	}

	public function OrderQueryBeforeExecute(&$orderQuery){
		$orderQuery->leftJoin('op.OrdersProductsAttributes op_a');
	}

	public function OrderClassQueryFillProductArray(&$pInfo, &$productArray){
		$OrdersProductsAttributes = $pInfo['OrdersProductsAttributes'];
		$subindex = 0;
		if (sizeof($OrdersProductsAttributes) > 0){
			foreach($OrdersProductsAttributes as $aInfo){
				$productArray['attributes'][$subindex] = array(
					'opAID' => $aInfo['orders_products_attributes_id'],
					'option_id' => $aInfo['options_id'],
					'value_id' => $aInfo['options_values_id'],
					'option' => $aInfo['products_options'],
					'value' => $aInfo['products_options_values'],
					'prefix' => $aInfo['price_prefix'],
					'price' => $aInfo['options_values_price']
				);

				$subindex++;
			}
		}
	}

	public function OrderClassLoadProduct(&$cartProduct, &$productArray){
		$attributes = $cartProduct->getInfo('attributes');
		if ($attributes !== false && sizeof($attributes) > 0){
			$productArray['attributes'] = array();

			$subindex = 0;
			$langId = Session::get('languages_id');
			reset($attributes);
			while(list($option, $value) = each($attributes)){
				$attribute = attributesUtil::getAttributes((int) $cartProduct->getIdString(), (int) $option, (int) $value);
				$attribute = $attribute[0];

				$productArray['attributes'][$subindex] = array(
					'option' => $attribute['ProductsOptions']['ProductsOptionsDescription'][$langId]['products_options_name'],
					'value' => $attribute['ProductsOptionsValues']['ProductsOptionsValuesDescription'][$langId]['products_options_values_name'],
					'option_id' => $option,
					'value_id' => $value,
					'prefix' => $attribute['price_prefix'],
					'price' => $attribute['options_values_price']
				);

				$subindex++;
			}
		}
	}

	public function OrderProductAfterProductName(&$orderedProduct){
		global $currencies;
		$html = '';
		if ($orderedProduct->hasInfo('OrdersProductsAttributes')){
			$attributes = $orderedProduct->getInfo('OrdersProductsAttributes');
			if (sizeof($attributes) > 0){
				$langId = Session::get('languages_id');
				foreach($attributes as $aInfo){
					$optionText = ' - ' . $aInfo['products_options'] . ': ' . $aInfo['products_options_values'];
					if ($aInfo['options_values_price'] != '0'){
						$optionText .= ' ( ' . $aInfo['price_prefix'] . $currencies->format($aInfo['options_values_price'] * $orderedProduct->getQuantity()) . ' )';
					}
					
					$html .= '<br>' . htmlBase::newElement('span')
					->css(array(
						'font-size' => '.8em',
						'font-style' => 'italic'
					))
					->html($optionText)
					->draw();
				}
			}
		}
		return $html;
	}

	public function OrderProductAfterProductNameEdit(&$orderedProduct){
		global $currencies;
		$html = '';
		if ($orderedProduct->hasInfo('OrdersProductsAttributes')){
			$attributes = $orderedProduct->getInfo('OrdersProductsAttributes');
			if (sizeof($attributes) > 0){
				$langId = Session::get('languages_id');
				foreach($attributes as $aInfo){
					$html .= '<br />' .
						'<small>&nbsp;<i> - ' .
						$aInfo['products_options'] .
						': ' .
						'<input type="text" class="ui-widget-content" name="product[' . $orderedProduct->getId() . '][attributes][' . $aInfo['options_id'] . '][value]" value="' . $aInfo['products_options_values'] . '">';

					//if ($aInfo['price'] != '0'){
					$html .= ' ( ' . '<select class="ui-widget-content" name="product[' . $orderedProduct->getId() . '][attributes][' . $aInfo['options_id'] . '][prefix]"><option value="+"' . ($aInfo['price_prefix'] == '+' ? ' selected="selected"' : '') . '>+</option><option value="-"' . ($aInfo['price_prefix'] == '-' ? ' selected="selected"' : '') . '>-</option></select> <input type="text" class="ui-widget-content" size="4" name="product[' . $orderedProduct->getId() . '][attributes][' . $aInfo['options_id'] . '][price]" value="' . ($aInfo['options_values_price'] * $orderedProduct->getQuantity()) . '"> )';
					//}
					$html .= '</i></small>';
				}
			}
		}else{
			$ProductsAttributes = attributesUtil::getAttributes(
				$orderedProduct->getProductsId(),
				null,
				null,
				$orderedProduct->getPurchaseType()
			);
			if ($ProductsAttributes){
				$Attributes = attributesUtil::organizeAttributeArray($ProductsAttributes);
				//print_r($Attributes);
				foreach($Attributes as $optionId => $aInfo){
					$valuesDrop = htmlBase::newElement('selectbox')
					->setName('product[' . $orderedProduct->getId() . '][attributes][' . $optionId . '][value]')
					->attr('attrval',$optionId)
					->addClass('ui-widget-content productAttribute');
					foreach($aInfo['ProductsOptionsValues'] as $vInfo){
						if (!isset($selectedPrefix)){
							$selectedPrefix = $vInfo['price_prefix'];
							$selectedPrice = $vInfo['options_values_price'];
						}
						$valuesDrop->addOption(
							$vInfo['options_values_id'],
							$vInfo['options_values_name']
						);
					}
					
					$prefixDrop = htmlBase::newElement('selectbox')
					->setName('product[' . $orderedProduct->getId() . '][attributes][' . $optionId . '][prefix]')
					->addClass('ui-widget-content')
					->selectOptionByValue($selectedPrefix);
					$prefixDrop->addOption('+', '+');
					$prefixDrop->addOption('-', '-');

					$priceInput = htmlBase::newElement('input')
					->setName('product[' . $orderedProduct->getId() . '][attributes][' . $optionId . '][price]')
					->val(($selectedPrice * $orderedProduct->getQuantity()))
					->addClass('ui-widget-content')->attr('size', '4');
					
					$html .= '<br />' .
						'<small>&nbsp;<i> - ' .
						$aInfo['options_name'] .
						': ' .
						$valuesDrop->draw() . 
						' ( ' . $prefixDrop->draw() . ' ' . $priceInput->draw() . ' )' .
						'</i></small>';
				}
			}
		}
		return $html;
	}

	public function CheckoutProductAfterProductName(&$cartProduct, &$orderedProduct = null){
		global $currencies, $order;
		if (is_null($orderedProduct)){
			$orderedProduct = '';
		}
		if (is_array($cartProduct)){

			if (isset($cartProduct['attributes'])){
				$attributes = $cartProduct['attributes'];
				if (sizeof($attributes) > 0){
					$langId = Session::get('languages_id');
					foreach($attributes as $aInfo){
						$orderedProduct .= '<br />' .
							'<small>&nbsp;<i> - ' .
							$aInfo['option'] .
							': ' .
							$aInfo['value'];

						if ($aInfo['price'] != '0'){
							$orderedProduct .= ' ( ' . $aInfo['prefix'] . $currencies->format($aInfo['price'] * $cartProduct['quantity'], true, $order->info['currency'], $order->info['currency_value']) . ' )';
						}
						$orderedProduct .= '</i></small>';
					}
				}
			}
		}else{

			if ($cartProduct->hasInfo('attributes')){
				$attributes = $cartProduct->getInfo('attributes');
				if (sizeof($attributes) > 0){
					$langId = Session::get('languages_id');
					while(list($option, $value) = each($attributes)){
						$attribute = attributesUtil::getAttributes((int) $cartProduct->getIdString(), (int) $option, (int) $value);
						$attribute = $attribute[0];

						$orderedProduct .= '<br />' .
							'<small>&nbsp;<i> - ' .
							$attribute['ProductsOptions']['ProductsOptionsDescription'][$langId]['products_options_name'] .
							': ' .
							$attribute['ProductsOptionsValues']['ProductsOptionsValuesDescription'][$langId]['products_options_values_name'];

						if ($attribute['options_values_price'] != '0'){
							$orderedProduct .= ' ( ' . $attribute['price_prefix'] . $currencies->format($attribute['options_values_price'] * $cartProduct->getQuantity(), true, $order->info['currency'], $order->info['currency_value']) . ' )';
						}
						$orderedProduct .= '</i></small>';
					}
				}
			}
		}
		return $orderedProduct;
	}

	public function ProductInfoBeforeDescription(){
		return $this->drawAttributes();
	}

	public function ProductListingProductsBeforeShowPrice(&$product){
		global $appExtension, $currencies;
		//$product = &$settings['productCls'];

		$Attributes = $this->drawAttributes(array('productCls' => $product, 'return_array' => '1'));
		$output = '';

		//var_dump($AttributesOutput);
		if(is_array($Attributes)){
			$output = '<div class="productListingColBoxContent_attributes ui-corner-bottom-big">';
			$table = htmlBase::newElement('table')
					->setCellPadding(3)
					->setCellSpacing(0);

			foreach($Attributes as $optionId => $attribute)
			{
				$attributeName = '';
				$attributeValuesText = '';
				$attributeName = $attribute['options_name'];
				if(is_array($attribute['ProductsOptionsValues'])){
					$attributeValues = array();
					$ctr = 0;
					$index = 0;
					foreach($attribute['ProductsOptionsValues'] as $attributeKey => $attributeValue)
					{
						$attributeValues[] = htmlBase::newElement('a')
								->attr('href', itw_app_link('options[' . $attributeKey . ']=' . $attributeValue['options_values_id'] . '&values[' . $attributeKey . ']=' . $optionId, 'products', 'search_result'))
								->html($attributeValue['options_values_name'])->draw();
						if($ctr >= 7){
							$attributeValuesText .= implode(', ', $attributeValues) . '<br>';
							$attributeValues = array();
							$ctr = 0;
						}
						$ctr++;
					}
					$attributeValuesText .= implode(', ', $attributeValues) . '<br>';
				}
				$table->addBodyRow(array(
				                        'columns' => array(
					                        array('addCls' => 'productListingColBoxContent_attributesName', 'text' => '<b>' . $attributeName . ': </b>'),
					                        array('addCls' => 'productListingColBoxContent_attributesValue', 'text' => $attributeValuesText, 'align' => 'left')
				                        )
				                   ));
			}

			$output .= $table->draw();
			$output .= '</div>';
		}
		return $output;

	}

	public function drawAttributes($settings = null){
		global $appExtension, $currencies;
		$product = &$settings['productCls'];

		if (!isset($product->productInfo['ProductsAttributes']) || sizeof($product->productInfo['ProductsAttributes']) <= 0)
			return;

		if (isset($settings['purchase_type'])){
			$ProductsAttributes = attributesUtil::getAttributes($product->productInfo['products_id'], null, null, $settings['purchase_type']);
		}else{
			$ProductsAttributes = attributesUtil::getAttributes($product->productInfo['products_id']);
		}

		$Attributes = array();
		foreach($ProductsAttributes as $attribute){
			if (!array_key_exists($attribute['options_id'], $Attributes)){
				$Attributes[$attribute['options_id']] = array(
					'options_name' => $attribute['ProductsOptions']['ProductsOptionsDescription'][Session::get('languages_id')]['products_options_name'],
					'option_type' => $attribute['ProductsOptions']['option_type'],
					'use_image' => $attribute['ProductsOptions']['use_image'],
					'use_multi_image' => $attribute['ProductsOptions']['use_multi_image'],
					'update_product_image' => $attribute['ProductsOptions']['update_product_image'],
					'ProductsOptionsValues' => array()
				);
			}

			$curArray = array(
				'options_values_id' => $attribute['options_values_id'],
				'options_values_price' => $attribute['options_values_price'],
				'options_values_name' => $attribute['ProductsOptionsValues']['ProductsOptionsValuesDescription'][Session::get('languages_id')]['products_options_values_name'],
				'price_prefix' => $attribute['price_prefix']
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

		if (is_null($settings) === false && isset($settings['return_array'])){
			return $Attributes;
		}
		if (sizeof($Attributes) <= 0)
			return '';

		require_once(sysConfig::getDirFsCatalog() . 'includes/classes/template.php');
		$templateFile = 'product_info_table.tpl';
		$templateDir = dirname(__FILE__) . '/catalog/ext_app/product/template/';
		if(is_null($settings) === false && isset($settings['template_file'])){
			$templateFile = $settings['template_file'];
		}
		if (is_null($settings) === false && isset($settings['template_dir'])){
			$templateDir = $settings['template_dir'];
		}
		$attributesTemplate = new Template($templateFile, $templateDir);
		$attributesTemplate->setTemplateFile($templateFile, $templateDir);

		$attributesTemplate->setVars(array(
			'attributes' => $Attributes
		));

		return $attributesTemplate->parse();
	}
}
?>