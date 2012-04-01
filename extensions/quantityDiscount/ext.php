<?php
/*
	Quantity Discount Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class Extension_quantityDiscount extends ExtensionBase {

	public function __construct(){
		parent::__construct('quantityDiscount');
		if (!defined('EXTENSION_QUANTITY_DISCOUNT_LEVELS')){
			define('EXTENSION_QUANTITY_DISCOUNT_LEVELS', 5);
		}
	}
	
	public function init(){
		global $App, $appExtension, $Template;
		if ($this->isEnabled() === false) return;
		
			EventManager::attachEvents(array(
				'AddToContentsBeforeProcess'
			), null, $this);
			
		if ($appExtension->isAdmin()){
		}
	}
	
	public function getProductsDiscounts($pId){
		$discounts = Doctrine_Query::create()
		->from('ProductsQuantityDiscounts')
		->where('products_id = ?', $pId)
		->orderBy('quantity_from asc')
		->execute();
		return $discounts;
	}
	
	public function AddToContentsBeforeProcess($pID_string, $cartProduct){
		global $appExtension;
		$productClass = $cartProduct->productClass;
		$quantity = $cartProduct->getQuantity();
		$productPrice = $cartProduct->getPrice();
		$productFinalPrice = $cartProduct->getFinalPrice();
		
		$discounts = $this->getProductsDiscounts($productClass->getID());
		if ($discounts->count() > 0){
			$productCurrentPrice = $productPrice;
			$specialsExt = $appExtension->getExtension('specials');
			if ($specialsExt !== false){
				$newPrice = $specialsExt->getSpecialsPrice($productClass);
				if ($newPrice > 0){
					$productCurrentPrice = $newPrice;
				}
			}
			
			foreach($discounts as $discount){
				if ($quantity >= $discount->quantity_from && $quantity <= $discount->quantity_to){
					if ($productCurrentPrice > $discount->price){
						$productCurrentPrice = $discount->price;
					}
				}
			}
			
			if ($productPrice > $productCurrentPrice){
				$cartProduct->subtractFromPrice($productPrice);
				$cartProduct->subtractFromFinalPrice($productPrice);
				
				$cartProduct->addToPrice($productCurrentPrice);
				$cartProduct->addToFinalPrice($productCurrentPrice);
			}
		}
	}
	
	public function showQuantityTable($settings){
		global $currencies, $appExtension;
		$productId = $settings['product_id'];
		$purchaseType = $settings['purchase_type'];
		$productClass = $settings['productClass'];
		
		if ($purchaseType != 'new') return '';
		
		$discounts = $this->getProductsDiscounts($productId);
		if ($discounts->count() > 0){
			$table = htmlBase::newElement('table')
			->css(array(
				'margin-left' => 'auto',
				'margin-right' => 'auto'
			))
			->setCellPadding(3)
			->setCellSpacing(0)
			->addClass('ui-widget ui-widget-content ui-corner-all');
			
			$table->addHeaderRow(array(
				'columns' => array(
					array(
						'addCls' => 'ui-widget-header ui-corner-top',
						'css' => array(
							'border-top' => 'none',
							'border-left' => 'none',
							'border-right' => 'none'
						),
						'text' => 'Quantity Discounts',
						'colspan' => '5'
					)
				)
			));
		
			$priceRow = array();
			$qtyRow = array();
			
			$purchaseTypeNew = $productClass->getPurchaseType($purchaseType);
			$productCurrentPrice = $purchaseTypeNew->getPrice();
			$specialsExt = $appExtension->getExtension('specials');
			if ($specialsExt !== false){
				$newPrice = $specialsExt->getSpecialsPrice($productClass);
				if ($newPrice > 0){
					$productCurrentPrice = $newPrice;
				}
			}
			
			$numDiscounts = sizeof($discounts);
			foreach($discounts as $i => $discount){
				$displayPrice = $discount->price;
				if ($productCurrentPrice < $displayPrice){
					$displayPrice = $productCurrentPrice;
				}
				
				
				if ($i == 0 && $numDiscounts == 1){
					$addCls = 'ui-corner-bottom';
				}elseif ($i == 0){
					$addCls = 'ui-corner-bl';
				}elseif ($i+1 == $numDiscounts){
					$addCls = '';
					$addCls = 'ui-corner-br';
				}else{
					$addCls = '';
				}
				
				$priceRow[] = array('addCls' => $addCls, 'css' => array('border' => '1px solid #ececec'), 'text' => $currencies->format($displayPrice));
				$qtyRow[] = array('text' => $discount->quantity_from . '-' . $discount->quantity_to);
			}

			$table->addBodyRow(array(
				'addCls' => 'ui-state-default',
				'columns' => $qtyRow
			));

			$table->addBodyRow(array(
				'columns' => $priceRow
			));
			
			return $table->draw();
		}
		return;
	}
}
?>