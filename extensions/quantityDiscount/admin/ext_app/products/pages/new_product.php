<?php
/*
	Quantity Discount Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class quantityDiscount_admin_products_new_product extends Extension_quantityDiscount {

	public function __construct(){
		parent::__construct();
	}
	
	public function load(){
		if ($this->enabled === false) return;
		
		EventManager::attachEvent('NewProductPricingTabBottom', null, $this);
	}

	public function exemptedPurchaseTypes(){
		return array('rental');
	}
	
	public function NewProductPricingTabBottom(&$Product, &$inputTable, &$typeName){
		if(in_array($typeName,$this->exemptedPurchaseTypes()))
			return false;
		if ($Product !== false && $Product['products_id'] > 0){
			$discounts = Doctrine_Query::create()
			->from('ProductsQuantityDiscounts')
			->where('products_id = ?', $Product['products_id'])
			->andWhere('purchase_type = ?', $typeName)
			->orderBy('quantity_from asc')
			->execute();
		}
		
		$mainTable = htmlBase::newElement('table')->setCellPadding(3)->setCellSpacing(0)->css('width', '450px');
		$mainTable->addHeaderRow(array(
			'addCls' => 'ui-widget-header',
			'columns' => array(
				array('colspan' => '4', 'align' => 'center', 'text' => 'Quantity Discounts')
			)
		));
		
		$mainTable->addHeaderRow(array(
			'addCls' => 'ui-state-default',
			'columns' => array(
				array('text' => sysLanguage::get('TABLE_HEADING_QUANTITY_FROM')),
				array('text' => sysLanguage::get('TABLE_HEADING_QUANTITY_TO')),
				array('text' => sysLanguage::get('TABLE_HEADING_PRICE_NET')),
				array('text' => sysLanguage::get('TABLE_HEADING_PRICE_GROSS'))
			)
		));
		
		for($i=1; $i<(EXTENSION_QUANTITY_DISCOUNT_LEVELS + 1); $i++){
			$qtyFromInput = htmlBase::newElement('input')->setName('discount_qty_from[' . $typeName . '][' . $i . ']')->attr('size', '3');
			$qtyToInput = htmlBase::newElement('input')->setName('discount_qty_to[' . $typeName . '][' . $i . ']')->attr('size', '3');
			
			$priceInput = htmlBase::newElement('input')
			->addClass('netPricing')
			->setId('discount_price_' . $typeName . '_' . $i)
			->setName('discount_price[' . $typeName . '][' . $i . ']')
			->attr('size', '6');
			
			$priceInputGross = htmlBase::newElement('input')
			->addClass('grossPricing')
			->setId('discount_price_' . $typeName . '_' . $i . '_gross')
			->setName('discount_price_gross[' . $typeName . '][' . $i . ']')
			->attr('size', '6');
			
			if (isset($discounts)){
				if (isset($discounts[$i-1])){
					$qtyFromInput->setValue($discounts[$i-1]->quantity_from);
					$qtyToInput->setValue($discounts[$i-1]->quantity_to);
					$priceInput->setValue($discounts[$i-1]->price);
					$priceInputGross->setValue($discounts[$i-1]->price);
				}
			}
			
			$mainTable->addBodyRow(array(
				'columns' => array(
					array('align' => 'center', 'text' => $qtyFromInput),
					array('align' => 'center', 'text' => $qtyToInput),
					array('align' => 'center', 'text' => $priceInput),
					array('align' => 'center', 'text' => $priceInputGross)
				)
			));
		}
		
		$inputTable->addBodyRow(array(
			'columns' => array(
				array('colspan' => 2, 'text' => $mainTable->draw())
			)
		));
	}
}
?>