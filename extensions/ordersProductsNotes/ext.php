<?php
/*
	Orders Products Notes Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class Extension_ordersProductsNotes extends ExtensionBase {

	public function __construct(){
		parent::__construct('ordersProductsNotes');
	}
	       
	public function init(){
		global $App, $appExtension, $Template;
		if ($this->isEnabled() === false) return;
		
		require(dirname(__FILE__) . '/classEvents/ShoppingCart.php');
		$eventClass = new ShoppingCart_ordersProductsNotes();
		$eventClass->init();
			
		EventManager::attachEvents(array(
			'ShoppingCartListingAddHeaderColumn',
			'ShoppingCartListingAddNewBodyColumn',
			'ShoppingCartListingAddBodyColumn',
			'ShoppingCartListingAddBodyColumnText',
			'InsertOrderedProductBeforeSave',
			'CheckoutProcessInsertOrderedProduct',
			'OrderClassQueryFillProductArray',
			'OrderProductAfterProductName',
		), null, $this);
	}

	public function ShoppingCartListingAddBodyColumn(&$productRows, $cartProduct){
			$rInfo = $cartProduct->getInfo();
			$el = '';
			if (array_key_exists('note', $rInfo)){
				$el = $rInfo['note'];
				array_splice($productRows, 2, 0, array(
						'text'   => $el)

				);
			}

	}

	public function ShoppingCartListingAddBodyColumnText(&$shoppingCartBodyRow, $rInfo){

	}
	
	public function ShoppingCartListingAddHeaderColumn(&$shoppingCartHeader){
		array_splice($shoppingCartHeader, 2, 0, array(array(
		    'addCls'  => 'main',
			'align' =>'left',
			'text' => '<b>Comment</b>'			
		)));
	}
	
	public function ShoppingCartListingAddNewBodyColumn(&$shoppingCartBodyRow, $product){
			$rInfo = $product->getInfo();
			$inputElement = htmlBase::newElement('input')
			->setName('products_note[' . $product->getIdString() . ']')
			->addClass('productsNote')
			->css('width', '100%');
			if (array_key_exists('note', $rInfo)){
				$inputElement->setValue($rInfo['note']);
			}
			array_splice($shoppingCartBodyRow, 2, 0, array(array(
				'addCls' => 'productListing-data',
				'text' => $inputElement->draw(),
				'attr' => array('align' => 'left', 'valign' => 'top')
			)));

	}
	
	public function InsertOrderedProductBeforeSave(&$newOrdersProduct, &$product){
		$rInfo = $product->getInfo();
		if (array_key_exists('note', $rInfo)){
			$newOrdersProduct->orders_products_notes = addslashes(strip_tags(stripslashes($rInfo['note'])));
		}
	}
	
	public function CheckoutProcessInsertOrderedProduct(&$product, &$orderedProducts){
		$rInfo = $product->getInfo();
		if (array_key_exists('note', $rInfo) && strlen($rInfo['note']) > 0){
			$orderedProducts .= 'Customer Note: ' . $rInfo['note'] . "\n";
		}
	}
	
	public function OrderProductAfterProductName(&$product){			   
		if (array_key_exists('note', $product)){
			return '<br><nobr><small>&nbsp;<i> - Customer Note: ' . stripslashes($product['note']) . '</i></small></nobr>';
		}
		return;
	}
	
	public function OrderClassQueryFillProductArray(&$pInfo, &$product){		
		if (array_key_exists('orders_products_notes', $pInfo)){
			$product['note'] = $pInfo['orders_products_notes'];
		}
	}
}
?>