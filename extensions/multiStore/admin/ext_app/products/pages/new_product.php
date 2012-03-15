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
		//Admin stores
		$Admin = Doctrine_Core::getTable('Admin')->find(Session::get('login_id'));
		$adminStores =explode(',',$Admin->admins_stores);
		foreach($Result as $sInfo){
			if(!in_array($sInfo['stores_id'], $adminStores)) continue;
			$isChecked = (isset($curStores) && in_array($sInfo['stores_id'], $curStores));

			/*if($isChecked == false){
				if(count($adminStores) == 1){
					$isChecked = true;
				}
			}*/

			$checkbox = htmlBase::newElement('checkbox')
			->setId('store_' . $sInfo['stores_id'])
			->setName('store[]')
			->setValue($sInfo['stores_id'])
			->setLabel($sInfo['stores_name'])
			->setLabelPosition('after')
			->setChecked($isChecked);
			
			$contents .= $checkbox->draw() . '<br />';
		}
		return '<div id="tab_' . $this->getExtensionKey() . '">' . $contents . '</div>';
	}

	public function loadTabsPricing(&$tabsObj, $pricingTabsInfo){
		$multiStoreTabs = htmlBase::newElement('tabs')->setId('storeTabs');
		$multiStoreTabs->addTabHeader('tab_global', array(
				'text' => 'Global'
			))->addTabPage('tab_global', array(
				'text' => $tabsObj->draw()
			));

		$stores = $this->getStoresArray();

		if (isset($_GET['pID'])){
			$Qproduct = Doctrine_Query::create()
				->from('StoresPricing')
				->where('products_id = ?', (int)$_GET['pID'])
				->execute();
			if ($Qproduct->count() > 0){
				$productInfo = $Qproduct->toArray(true);
			}
		}
		$pricingTabsInfo = array(
			'new' => 'New',
			'used' => 'Used',
			'stream' => 'Streaming',
			'member_stream' => sysLanguage::get('TEXT_STREAMING_MEMBERSHIP'),
			'download' => 'Download',
			'rental' => 'Member Rental'
		);
		foreach($stores as $sInfo){

			if (isset($productInfo)){
				$pInfo = $productInfo[$sInfo['stores_id']];
			}


			if (isset($pInfo)){
				$currentTypes = explode(',', $pInfo['products_type']);
			}

			if (isset($pInfo)){
				$currentAllowOverbooking = explode(',', $pInfo['allow_overbooking']);
			}


			$pricingTabsObj = htmlBase::newElement('tabs')
			->addClass('pricingTabs')
			->setId('pricingTabs_'.$sInfo['stores_id']);

			$radioSet = htmlBase::newElement('radio');
			$radioSet->addGroup(array(
					'name' => 'store_show_method[' . $sInfo['stores_id'] . ']',
					'addCls' => 'showMethod',
					'data' => array(
						array(
							'label' => 'Use Global',
							'value' => 'use_global',
						),
						array(
							'label' => 'Use Custom',
							'value' => 'use_custom'
						)
					)
				));

			if (isset($pInfo)){
				$radioSet->setChecked($pInfo['show_method']);
			}else{
				$radioSet->setChecked('use_global');
			}

			foreach($pricingTabsInfo as $pricingTypeName => $pricingTypeText){
				$productTypeEnabled = htmlBase::newElement('checkbox')
					->setName('products_type_store_'.$sInfo['stores_id'].'[]')
					->setValue($pricingTypeName);

				if (isset($currentTypes) && in_array($pricingTypeName, $currentTypes)){
					$productTypeEnabled->setChecked(true);
				}
				$inputTable = htmlBase::newElement('table')
					->setCellPadding(2)
					->setCellSpacing(0);

				$inputTable->addBodyRow(array(
						'columns' => array(
							array('text' => sysLanguage::get('TEXT_PRODUCTS_ENABLED')),
							array('text' => $productTypeEnabled->draw())
						)
					));
				if($pricingTypeName == 'new' || $pricingTypeName == 'used'){
					$allowOverbookingEnabled = htmlBase::newElement('checkbox')
						->setName('allow_overbooking_store_'.$sInfo['stores_id'].'[]')
						->setValue($pricingTypeName);

					if (isset($currentAllowOverbooking) && in_array($pricingTypeName, $currentAllowOverbooking)){
						$allowOverbookingEnabled->setChecked(true);
					}
					$inputTable->addBodyRow(array(
							'columns' => array(
								array('text' => sysLanguage::get('TEXT_PRODUCTS_ALLOW_OVERBOOKING')),
								array('text' => $allowOverbookingEnabled->draw())
							)
					));
				}



				if($pricingTypeName !== 'rental' && $pricingTypeName !== 'member_stream'){
					$inputNet = htmlBase::newElement('input')->addClass('netPricing');
					$inputGross = htmlBase::newElement('input')->addClass('grossPricing');
					if ($pricingTypeName == 'new'){
						$inputNet->setName('products_price_'.$sInfo['stores_id'])
							->setId('products_price_'.$sInfo['stores_id']);

						$inputGross->setName('products_price_gross_'.$sInfo['stores_id'])
							->setId('products_price_'.$sInfo['stores_id'].'_gross');
						if (isset($pInfo) && $pInfo['show_method'] == 'use_custom'){
							$inputNet->val($pInfo['products_price']);
							$inputGross->html($pInfo['products_price']);
						}
					}else{
						$inputNet->setName('products_price_' . $pricingTypeName.'_'.$sInfo['stores_id'])
						->setId('products_price_' . $pricingTypeName.$sInfo['stores_id'])							;

						$inputGross->setName('products_price_' . $pricingTypeName . '_gross')
						->setId('products_price_' . $pricingTypeName .$sInfo['stores_id'].'_gross');
						if (isset($pInfo) && $pInfo['show_method'] == 'use_custom'){
							$inputNet->val($pInfo['products_price_' . $pricingTypeName]);
							$inputGross->html($pInfo['products_price_' . $pricingTypeName]);
						}
					}

					$inputTable->addBodyRow(array(
							'columns' => array(
								array('text' => sysLanguage::get('TEXT_PRODUCTS_PRICE_NET')),
								array('text' => $inputNet->draw())
							)
						));
					$inputTable->addBodyRow(array(
							'columns' => array(
								array('text' => sysLanguage::get('TEXT_PRODUCTS_PRICE_GROSS')),
								array('text' => $inputGross->draw())
							)
						));

				}elseif($pricingTypeName == 'rental'){
					$inputNet = htmlBase::newElement('input')->addClass('netPricing');
					$inputNet->setName('products_keepit_price'.'_'.$sInfo['stores_id'])
						->setId('products_keepit_price_'.$sInfo['stores_id']);

					$inputTable->addBodyRow(array(
							'columns' => array(
								array('text' => sysLanguage::get('TEXT_PRODUCTS_PRICE_NET')),
								array('text' => $inputNet->draw())
							)
						));

				}

				EventManager::notify('NewProductPricingTabBottom', (isset($pInfo) ? $pInfo : false), &$inputTable, &$pricingTypeName);

				$pricingTabsObj->addTabHeader('productPricingTab_' . $pricingTypeName, array('text' => $pricingTypeText))
					->addTabPage('productPricingTab_' . $pricingTypeName, array('text' => $inputTable));
			}

			$multiStoreTabs->addTabHeader('storeTabs_store_' . $sInfo['stores_id'], array(
					'text' => $sInfo['stores_name']
				))->addTabPage('storeTabs_store_' . $sInfo['stores_id'], array(
					'text' => 'Display Method: ' . $radioSet->draw() . '<br /><br />' . $pricingTabsObj->draw()
				));
		}
		$tabsObj = $multiStoreTabs;
	}
}
?>