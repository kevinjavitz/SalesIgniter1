<?php
/*
	Multi Stores Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class multiStore_admin_infoPages_manage_newContentBlock extends Extension_multiStore {

	public function __construct(){
		parent::__construct('multiStore');
	}
	
	public function load(){
		global $appExtension;
		if ($this->isEnabled() === false) return;
		$appExtension->registerAsResource(__CLASS__, $this);
		
		$resourceName = 'appExtensionResource:' . __CLASS__;
	}
	
	public function loadTabs(&$tabsObj){
		$multiStoreTabs = htmlBase::newElement('tabs')->setId('storeTabs');
		$multiStoreTabs->addTabHeader('tab_global', array(
			'text' => 'Global'
		))->addTabPage('tab_global', array(
			'text' => $tabsObj->draw()
		));
		
		$stores = $this->getStoresArray();

		if (isset($_GET['pID'])){
			$Qpage = Doctrine_Query::create()
			->from('StoresPages p')
			->leftJoin('p.StoresPagesDescription')
			->where('p.pages_id = ?', (int)$_GET['pID'])
			->execute();
			if ($Qpage->count() > 0){
				$pagesInfo = $Qpage->toArray(true);
			}
		}
		
		foreach($stores as $sInfo){
			if (isset($pagesInfo)){
				$pInfo = $pagesInfo[$sInfo['stores_id']];
			}
			
			$radioSet = htmlBase::newElement('radio');
			$radioSet->addGroup(array(
				'name' => 'store_show_method[' . $sInfo['stores_id'] . ']',
				'addCls' => 'showMethod',
				'checked' => 'use_global',
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
			}
			
			$storeTabsLang = htmlBase::newElement('tabs')
			->addClass('storeTabs_langTabs')
			->setId('storeTabs_langTabs_' . $sInfo['stores_id']);
			
			if (!isset($pInfo) || (isset($pInfo) && $pInfo['show_method'] != 'use_custom')){
				$storeTabsLang->hide();
			}
			
			foreach(sysLanguage::getLanguages() as $lInfo){
				$lID = $lInfo['id'];
				
				$titleInput = htmlBase::newElement('input')
				->attr('language_id', $lID)
				->addClass('titleInput')
				->setName('store_pages_title[' . $sInfo['stores_id'] . '][' . $lID . ']');
				
				$ckField = htmlBase::newElement('ck_editor')
				->attr('language_id', $lID)
				->setName('store_pages_html_text[' . $sInfo['stores_id'] . '][' . $lID . ']')
				->attr('cols', 90)
				->attr('rows', 20);
				
				if (isset($pInfo) && $pInfo['show_method'] == 'use_custom'){
					$titleInput->val($pInfo['StoresPagesDescription'][$lID]['pages_title']);
					$ckField->html($pInfo['StoresPagesDescription'][$lID]['pages_html_text']);
				}
				
				$storeTabsLang->addTabHeader('storeTabs_langTab_' . $sInfo['stores_id'] . '_' . $lID, array(
					'text' => $lInfo['showName']()
				))->addTabPage('storeTabs_langTab_' . $sInfo['stores_id'] . '_' . $lID, array(
					'text' => sysLanguage::get('TEXT_PAGES_TITLE') . '&nbsp;' . $titleInput->draw() . '<br /><br />' . 
					          $ckField->draw() . '<br />' . 
					          sysLanguage::get('TEXT_PAGES_PAGE_NOTE')
				));
			}
			
			$multiStoreTabs->addTabHeader('storeTabs_store_' . $sInfo['stores_id'], array(
				'text' => $sInfo['stores_name']
			))->addTabPage('storeTabs_store_' . $sInfo['stores_id'], array(
				'text' => 'Display Method: ' . $radioSet->draw() . '<br /><br />' . $storeTabsLang->draw()
			));
		}
		$tabsObj = $multiStoreTabs;
	}
}
?>