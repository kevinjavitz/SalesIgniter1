<?php
/*
	Royalties System Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class Extension_pointsRewards extends ExtensionBase {

	public function __construct(){
		parent::__construct('pointsRewards');
	}

	public function init(){
		global $appExtension;
	}

	public function getPointsEarned() {
		global $userAccount;
		$finalTotal = 0;
		$QpointsTable = Doctrine_Query::create()
				->select('sum(points) as totalPoints')
				->from('pointsRewardsPointsEarned ')
				->where('customers_id= ?', (int)$userAccount->getCustomerId());
		if(sysConfig::get('EXTENSION_POINTS_REWARDS_SYSTEM_POINTS_ON_SAME_PURCHASETYPE') == 'True'){
			//$QpointsTable->andWhere('purchase_type = "reservation"');
		}
		$Qpoints = $QpointsTable->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		$QpointsDeductedTable = Doctrine_Query::create()
				->select('sum(points) as totalPoints')
				->from('pointsRewardsPointsDeducted ')
				->where('customers_id= ?', (int)$userAccount->getCustomerId());
		if(sysConfig::get('EXTENSION_POINTS_REWARDS_SYSTEM_POINTS_ON_SAME_PURCHASETYPE') == 'True'){
			//$QpointsDeductedTable->andWhere('purchase_type = "reservation"');
		}
		$QpointsDeducted = $QpointsDeductedTable->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Qpoints) {
			$finalTotal = $Qpoints[0]['totalPoints'] - $QpointsDeducted[0]['totalPoints'];
			return $finalTotal;
		}
		return $finalTotal;
	}
}
?>