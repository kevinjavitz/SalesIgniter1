<?php
/*
	Multi Stores Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class multiStore_admin_products_new_product extends Extension_multiStore {

	public function __construct(){
		parent::__construct('multiStore');
	}
	
	public function load(){
		if ($this->enabled === false) return;
		
		EventManager::attachEvents(array(
			'NewProductTabHeader',
			'NewProductTabBody',
			'NewProductSave'
		), null, $this);
	}
	
	public function NewProductSave(&$Product){
		$ProductsToStores =& $Product->ProductsToStores;
		$ProductsToStores->delete();
		
		if (isset($_POST['store'])){
			foreach($_POST['store'] as $storeId){
				$ProductsToStores[]->stores_id = $storeId;
			}
		}
	}
	
	public function NewProductTabHeader(){
		return '<li class="ui-tabs-nav-item"><a href="#tab_' . $this->getExtensionKey() . '"><span>' . 'Stores' . '</span></a></li>';
	}
	
	public function NewProductTabBody(&$pInfo){
		$contents = '';
		$Qstores = Doctrine_Query::create()
		->from('Stores')
		->orderBy('stores_name');
		
		if (isset($pInfo['products_id']) && $pInfo['products_id'] > 0){
			$Qproduct = Doctrine_Query::create()
			->from('ProductsToStores')
			->where('products_id = ?', $pInfo['products_id'])
			->execute(array(), Doctrine::HYDRATE_ARRAY);
			
			$curStores = array();
			foreach($Qproduct as $psInfo){
				$curStores[] = $psInfo['stores_id'];
			}
		}
		$Result = $Qstores->execute(array(), Doctrine::HYDRATE_ARRAY);
		foreach($Result as $sInfo){
			$checkbox = htmlBase::newElement('checkbox')
			->setId('store_' . $sInfo['stores_id'])
			->setName('store[]')
			->setValue($sInfo['stores_id'])
			->setLabel($sInfo['stores_name'])
			->setLabelPosition('after')
			->setChecked((isset($curStores) && in_array($sInfo['stores_id'], $curStores)));
			
			$contents .= $checkbox->draw() . '<br />';
		}
		return '<div id="tab_' . $this->getExtensionKey() . '">' . $contents . '</div>';
	}
}
?>