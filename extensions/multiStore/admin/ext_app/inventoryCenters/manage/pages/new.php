<?php
/*
	Multi Stores Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class multiStore_admin_inventoryCenters_manage_new extends Extension_multiStore {

	public function __construct(){
		parent::__construct('multiStore');
	}
	
	public function load(){
		global $appExtension;
		if ($this->isEnabled() === false) return;
		$appExtension->registerAsResource(__CLASS__, $this);

		EventManager::attachEvents(array(
			'InventoryCentersAfterDescription'
		), null, $this);

	}
	
	public function InventoryCentersAfterDescription(&$Inventory){
		$contents = '';
		$Qstores = Doctrine_Query::create()
		->from('Stores')
		->orderBy('stores_name');
		if($Inventory != null){
			$curStores = explode(';',$Inventory->inventory_center_stores);
		}
		$Result = $Qstores->execute(array(), Doctrine::HYDRATE_ARRAY);
		foreach($Result as $sInfo){
			$checkbox = htmlBase::newElement('checkbox')
			->setId('store_' . $sInfo['stores_id'])
			->setName('inventory_center_stores[]')
			->setValue($sInfo['stores_id'])
			->setLabel($sInfo['stores_name'])
			->setLabelPosition('after')
			->setChecked((isset($curStores) && in_array($sInfo['stores_id'], $curStores)));
			
			$contents .= $checkbox->draw() . '<br />';
		}
		$returnVal = '<tr>' .
			'<td>' . sysLanguage::get('ENTRY_STORES') . '</td>' .
			'<td>' . $contents . '</td>' .
		'</tr>';
		return $returnVal;
	}
}
?>