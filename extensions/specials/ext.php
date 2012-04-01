<?php
/*
	Product Specials Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class Extension_specials extends ExtensionBase {
			  
	public function __construct(){
		parent::__construct('specials');
	}
	
	public function init(){
		global $appExtension;
		if ($this->isEnabled() === false) return;
		
		EventManager::attachEvents(array(
			'ProductQueryBeforeExecute',
			'ProductNewPriceBeforeDisplay'
		), null, $this);
		
		EventManager::attachEvents(array(
			'ShoppingCartGetProductsModifyArray'
		), 'ShoppingCartContents', $this);

		if ($appExtension->isAdmin()){
			EventManager::attachEvents(array(
				'BoxCatalogAddLink'
			), null, $this);
		}

		Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->exec('update specials set status = 0, date_status_change = now() where status = 1 and expires_date > 0 and expires_date <= now()');
	}
	
	public function BoxCatalogAddLink(&$contents){
		if (sysPermissions::adminAccessAllowed('manage', 'default','specials') === true){
			$contents['children'][] = array(
				'link'       => itw_app_link('appExt=specials','manage','default','SSL'),
				'text' => sysLanguage::get('BOX_CATALOG_SPECIALS')
			);
		}
	}
	
	public function getSpecialsPrice($productClass){
		$productInfo = $productClass->productInfo;
		if (empty($productInfo['Specials'])) return;
		return $productInfo['Specials']['specials_new_products_price'];
	}
	
	public function ProductQueryBeforeExecute(&$productQuery){
		$productQuery->addSelect('sp.*')->leftJoin('p.Specials sp');
	}
	
	public function ShoppingCartGetProductsModifyArray(&$productArray, &$pInfo){
		$productInfo = $productArray['productClass']->productInfo;
		if (empty($productInfo['Specials'])) return;
		
		$productArray['final_price'] -= $productArray['price'];
		
		$productArray['price'] = $productInfo['Specials']['specials_new_products_price'];
		$productArray['final_price'] += $productArray['price'];
	}
	
	public function ProductNewPriceBeforeDisplay(&$specialPrice, &$price){
		global $currencies;

		if (sysConfig::get('EXTENSION_SPECIALS_SHOW_PRICE_METHOD') == 'replace'){
			$price = '<span class="specialProductsPrice">' . $currencies->format($specialPrice) . '</span>';
		}elseif (sysConfig::get('EXTENSION_SPECIALS_SHOW_PRICE_METHOD') == 'strikethrough_same'){
			$price = '<s>' . $price . '</s>&nbsp;<span class="specialProductsPrice">' . $currencies->format($specialPrice) . '</span>';
		}elseif (sysConfig::get('EXTENSION_SPECIALS_SHOW_PRICE_METHOD') == 'strikethrough_new'){
			$price = '<s>' . $price . '</s><br /><span class="specialProductsPrice">' . $currencies->format($specialPrice) . '</span>';
		}else{
			die('No special price display method selected.');
		}
	}
}
?>