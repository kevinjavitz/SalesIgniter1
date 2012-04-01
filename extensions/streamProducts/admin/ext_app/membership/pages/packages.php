<?php
/*
	Stream Products Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class streamProducts_admin_membership_packages extends Extension_streamProducts {

	public function __construct(){
		parent::__construct('streamProducts');
	}
	
	public function load(){
		if ($this->isEnabled() === false) return;
		
		EventManager::attachEvents(array(
			'MembershipPackageEditWindowBeforeDraw'
		), null, $this);
	}
	
	public function MembershipPackageEditWindowBeforeDraw(&$infoBox, &$Package){
		$streamingAllowed = htmlBase::newElement('checkbox')
		->setName('streaming_allowed')
		->val('1')
		->setChecked(($Package->streaming_allowed == '1'));
		
		$numOfViews = htmlBase::newElement('input')
		->attr('size', '4')
		->setName('streaming_no_of_views')
		->val($Package->streaming_no_of_views);
		
		$viewsPer = htmlBase::newElement('selectbox')
		->setName('streaming_views_period')
		->addOption('B', 'Billing Period')
		->addOption('T', 'Time Period')
		->selectOptionByValue($Package->streaming_views_period);
		
		$viewsTime = htmlBase::newElement('input')
		->attr('size', '4')
		->setName('streaming_views_time')
		->val($Package->streaming_views_time);
		
		$timePeriod = htmlBase::newElement('selectbox')
		->setName('streaming_views_time_period')
		->addOption('D', 'Day')
		->addOption('W', 'Week')
		->addOption('M', 'Month')
		->selectOptionByValue($Package->streaming_views_time_period);
		
		$streamHours = htmlBase::newElement('input')
		->attr('size', '4')
		->setName('streaming_access_hours')
		->val($Package->streaming_access_hours);
		
		$infoBox->addContentRow('<hr><br>Streaming Info');
		$infoBox->addContentRow(sysLanguage::get('TEXT_ENTRY_ALLOW_STREAMING') . '<br>' . $streamingAllowed->draw());
		$infoBox->addContentRow(sysLanguage::get('TEXT_ENTRY_VIEWS_ALLOWED') . '<br>' . $numOfViews->draw());
		$infoBox->addContentRow(sysLanguage::get('TEXT_ENTRY_VIEWS_PER') . '<br>' . $viewsPer->draw());
		$infoBox->addContentRow(sysLanguage::get('TEXT_ENTRY_TIME_PERIOD'). '<br>' . $viewsTime->draw() . ' ' . $timePeriod->draw());
		$infoBox->addContentRow(sysLanguage::get('TEXT_ENTRY_STREAM_HOURS') . '<br>' . $streamHours->draw());
	}
}
