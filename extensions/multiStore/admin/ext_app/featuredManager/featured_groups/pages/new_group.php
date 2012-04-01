<?php
/*
	Multi Stores Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class multiStore_admin_featuredManager_featured_groups_new_group extends Extension_multiStore {

	public function __construct(){
		parent::__construct('multiStore');
	}
	
	public function load(){
		global $appExtension;
		if ($this->isEnabled() === false) return;
		$appExtension->registerAsResource(__CLASS__, $this);

		EventManager::attachEvents(array(
			'NewGroupTabHeader',
			'NewGroupTabBody'
		), null, $this);

	}
	
	public function NewGroupTabHeader(){
		return '<li class="ui-tabs-nav-item"><a href="#tab_' . $this->getExtensionKey() . '"><span>' . 'Stores' . '</span></a></li>';
	}
	
	public function NewGroupTabBody(&$gInfo){
		$contents = '';
		$Qstores = Doctrine_Query::create()
		->from('Stores')
		->orderBy('stores_name');
		
		if (isset($gInfo['banner_group_id']) && $gInfo['banner_group_id'] > 0){
			$Qproduct = Doctrine_Query::create()
			->from('FeaturedManagerGroupsToStores')
			->where('featured_group_id = ?', $gInfo['featured_group_id'])
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