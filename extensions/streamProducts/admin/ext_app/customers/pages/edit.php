<?php
/*
	Stream Products Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class streamProducts_admin_customers_edit extends Extension_streamProducts {

	public function __construct(){
		parent::__construct('streamProducts');
	}
	
	public function load(){
		if ($this->isEnabled() === false) return;
		
		EventManager::attachEvents(array(
			'AdminCustomerEditBuildTabs'
		), null, $this);
	}
	
	public function AdminCustomerEditBuildTabs($Customer, &$tabsObj){
		$StreamingTable = htmlBase::newElement('table')
		->setCellPadding(3)
		->setCellSpacing(0)
		->css(array(
			'width' => '98%'
		));
		
		$CustomersMembership = $Customer->CustomersMembership;
		if ($CustomersMembership->ismember == 'M'){
			if ($CustomersMembership->Membership->streaming_views_period == 'T'){
				$viewsPer = $CustomersMembership->Membership->streaming_views_time . ' ';
				if ($CustomersMembership->Membership->streaming_views_time_period == 'D'){
					$viewsPer .= sysLanguage::get('TEXT_DAYS');
				}elseif ($CustomersMembership->Membership->streaming_views_time_period == 'W'){
					$viewsPer .= sysLanguage::get('TEXT_WEEKS');
				}elseif ($CustomersMembership->Membership->streaming_views_time_period == 'M'){
					$viewsPer .= sysLanguage::get('TEXT_MONTHS');
				}
			}else{
				$viewsPer = sysLanguage::get('TEXT_BILLING_PERIOD');
			}
			
			$Qviews = Doctrine_Query::create()
			->select('count(*) as total')
			->from('CustomersStreamingViews')
			->where('customers_id = ?', $Customer->customers_id)
			->andWhere('date_added >= ?', $CustomersMembership->membership_start_streaming)
			->andWhere('date_added <= ?', $CustomersMembership->membership_end_streaming)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			
			$StreamingTable->addBodyRow(array(
				'columns' => array(
					array('text' => sprintf(sysLanguage::get('TEXT_INFO_USED_STREAMS'), $Qviews[0]['total'], $viewsPer, tep_date_short($CustomersMembership->membership_start_streaming), tep_date_short($CustomersMembership->membership_end_streaming)))
				)
			));
			
			$StreamingTable->addBodyRow(array(
				'columns' => array(
					array('text' => sysLanguage::get('TEXT_INFO_RESET_VIEWS') . htmlBase::newElement('button')->usePreset('reset')->setHref(itw_app_link('action=resetStreaming&cID=' . $Customer->customers_id, 'customers', 'edit'))->draw())
				)
			));
			
			$Qviews = Doctrine_Query::create()
			->from('CustomersStreamingViews')
			->where('customers_id = ?', $Customer->customers_id)
			->orderBy('date_added DESC')
			->limit(30)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if ($Qviews){
				$StreamingHistoryTable = htmlBase::newElement('table')
				->setCellPadding(3)
				->setCellSpacing(0)
				->addClass('ui-widget ui-widget-content')
				->css(array(
					'width' => '98%'
				));

				$StreamingHistoryTable->addHeaderRow(array(
					'addCls' => 'ui-widget-header',
					'columns' => array(
						array('align' => 'left', 'text' => sysLanguage::get('TABLE_HEADING_PRODUCTS_NAME')),
						array('align' => 'left', 'text' => sysLanguage::get('TABLE_HEADING_DATE_VIEWED'))
					)
				));
				
				foreach($Qviews as $vInfo){
					$StreamingHistoryTable->addBodyRow(array(
						'columns' => array(
							array('text' => tep_get_products_name($vInfo['products_id'])),
							array('text' => tep_date_short($vInfo['date_added']))
						)
					));
				}
				
				$StreamingTable->addBodyRow(array(
					'columns' => array(
						array(
							'align' => 'center',
							'text' => sprintf(sysLanguage::get('TABLE_HEADING_VIEWS_HISTORY'), 30) . '<br>' . $StreamingHistoryTable->draw()
						)
					)
				));
			}
		}
		
		$tabsObj->addTabHeader('customerTab3', array('text' => sysLanguage::get('TAB_STREAMING_INFO')))
		->addTabPage('customerTab3', array('text' => $StreamingTable));
	}
}
?>