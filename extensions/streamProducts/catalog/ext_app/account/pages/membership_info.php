<?php
/*
	Stream Products Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class streamProducts_catalog_account_membership_info extends Extension_streamProducts {

	public function __construct(){
		parent::__construct('streamProducts');
	}
	
	public function load(){
		if ($this->enabled === false) return;
		
		EventManager::attachEvents(array(
			'AccountMembershipInfoAddToTable'
		), null, $this);
	}
	
	public function AccountMembershipInfoAddToTable($membership){
		global $userAccount;
		
		$return = '';
		if ($membership->planInfo['streaming_allowed'] == '1'){
			if ($membership->planInfo['streaming_views_period'] == 'T'){
				$viewsPer = $membership->planInfo['streaming_views_time'] . ' ';
				if ($membership->planInfo['streaming_views_time_period'] == 'D'){
					$viewsPer .= 'Day(s)';
				}elseif ($membership->planInfo['streaming_views_time_period'] == 'W'){
					$viewsPer .= 'Week(s)';
				}elseif ($membership->planInfo['streaming_views_time_period'] == 'M'){
					$viewsPer .= 'Month(s)';
				}
			}else{
				$viewsPer = 'Billing Period';
			}
			
			$Qviews = Doctrine_Query::create()
			->select('count(*) as total')
			->from('CustomersStreamingViews')
			->where('customers_id = ?', $userAccount->getCustomerId())
			->andWhere('date_added >= ?', date('Y-m-d', $membership->membershipInfo['membership_start_streaming']))
			->andWhere('date_added <= ?', date('Y-m-d', $membership->membershipInfo['membership_end_streaming']))
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			
			$return = '<tr>' . 
				'<td class="main">Streaming Information:</td>' . 
				'<td class="main">Your plan allows ' . $membership->planInfo['streaming_no_of_views'] . ' Views per ' . $viewsPer . '</td>' . 
			'</tr>' . 
			'<tr>' . 
				'<td class="main"></td>' . 
				'<td class="main">You have used ' . $Qviews[0]['total'] . ' Views this period which is from ' . strftime(sysLanguage::getDateFormat('long'), $membership->membershipInfo['membership_start_streaming']) . ' - ' . strftime(sysLanguage::getDateFormat('long'), $membership->membershipInfo['membership_end_streaming']) . '</td>' . 
			'</tr>';
		}
		return $return;
	}
}
?>