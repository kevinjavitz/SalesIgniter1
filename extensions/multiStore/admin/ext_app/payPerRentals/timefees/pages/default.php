<?php
/*
	Multi Stores Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class multiStore_admin_payPerRentals_timefees_default extends Extension_multiStore {

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
		global $appExtension;
		$multiStoreTabs = htmlBase::newElement('tabs')->setId('storeTabs');
		$multiStoreTabs->addTabHeader('tab_global', array(
				'text' => 'Global'
			))->addTabPage('tab_global', array(
				'text' => $tabsObj->draw()
			));

		$stores = $this->getStoresArray();

		if (isset($_GET['tfID'])){
			$Qtime = Doctrine_Query::create()
				->from('StoresTimeFees')
				->where('timefees_id = ?', (int)$_GET['tfID'])
				->execute();
			if ($Qtime->count() > 0){
				$timeInfo = $Qtime->toArray(true);
			}
		}

		foreach($stores as $sInfo){

			if (isset($timeInfo)){
				$pInfo = $timeInfo[$sInfo['stores_id']];
			}

			$timefeesTabsObj = htmlBase::newElement('tabs')
			->addClass('timefeesTabs')
			->setId('timefeesTabs_'.$sInfo['stores_id']);

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

				$inputTable = htmlBase::newElement('table')
				->setCellPadding(2)
				->setCellSpacing(0);

				$htmlTimeFeeName = htmlBase::newElement('input')
					->setLabel(sysLanguage::get('TEXT_TIMEFEES'))
					->setLabelPosition('before')
					->setName('timefees_name_'.$sInfo['stores_id'])
					->setValue($pInfo['timefees_name']);

				$htmlTimeFeeFee = htmlBase::newElement('input')
					->setLabel(sysLanguage::get('TEXT_TIMEFEES_FEE'))
					->setLabelPosition('before')
					->setName('timefees_fee_'.$sInfo['stores_id'])
					->setValue($pInfo['timefees_fee']);

				$htmlTimeFeeStart = htmlBase::newElement('selectbox')
					->setLabel(sysLanguage::get('TEXT_TIMEFEES_START'))
					->setLabelPosition('before')
					->setName('timefees_start_'.$sInfo['stores_id']);
				$htmlTimeFeeStart->selectOptionByValue($pInfo['timefees_start']);

				$htmlTimeFeeEnd = htmlBase::newElement('selectbox')
					->setLabel(sysLanguage::get('TEXT_TIMEFEES_END'))
					->setLabelPosition('before')
					->setName('timefees_end_'.$sInfo['stores_id']);
				$htmlTimeFeeEnd->selectOptionByValue($pInfo['timefees_end']);

				$i = 0;
				while($i<24){
					if($i<10){
						$k = '0'.$i;
					}else{
						$k = $i;
					}
					$htmlTimeFeeStart->addOption($i,$k.':00');
					$htmlTimeFeeEnd->addOption($i,$k.':00');
					$i++;
				}

				$inputTable->addBodyRow(array(
						'columns' => array(
							array('colspan' => 2,'text' => $htmlTimeFeeName->draw())
						)
					));
				$inputTable->addBodyRow(array(
						'columns' => array(
							array('colspan' => 2,'text' => $htmlTimeFeeFee->draw())
						)
					));
				$inputTable->addBodyRow(array(
						'columns' => array(
							array('colspan' => 2,'text' => $htmlTimeFeeStart->draw())
						)
					));

				$inputTable->addBodyRow(array(
						'columns' => array(
							array('colspan' => 2,'text' => $htmlTimeFeeEnd->draw())
						)
					));


				$timefeesTabsObj->addTabHeader('timefeesTab1', array('text' => 'Fees'))
				->addTabPage('timefeesTab1', array('text' => $inputTable));

			$multiStoreTabs->addTabHeader('storeTabs_store_' . $sInfo['stores_id'], array(
					'text' => $sInfo['stores_name']
				))->addTabPage('storeTabs_store_' . $sInfo['stores_id'], array(
					'text' => 'Display Method: ' . $radioSet->draw() . '<br /><br />' . $timefeesTabsObj->draw()
				));
		}
		$tabsObj = $multiStoreTabs;
	}
}
?>