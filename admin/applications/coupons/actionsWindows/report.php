<?php
	$Qcoupon = Doctrine_Query::create()
	->select('coupon_name')
	->from('CouponsDescription')
	->where('coupon_id = ?', (int) $_GET['cID'])
	->andWhere('language_id = ?', Session::get('languages_id'))
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	
	$infoBox = htmlBase::newElement('infobox');
	$infoBox->setHeader('<b>' . sprintf(sysLanguage::get('TEXT_INFO_HEADING_VOUCHER_REPORT'), $Qcoupon[0]['coupon_name']) . '</b>');
	$infoBox->setButtonBarLocation('top');

	$backButton = htmlBase::newElement('button')->addClass('backButton')->usePreset('back');

	$infoBox->addButton($backButton);

	$listTable = htmlBase::newElement('table')
	->setCellSpacing(0)
	->setCellPadding(2)
	->addClass('ui-widget ui-widget-content')
	->css(array(
		'width' => '80%'
	));
		
	$listTable->addHeaderRow(array(
		'addCls' => 'ui-state-hover',
		'columns' => array(
			array('align' => 'left', 'text' => sysLanguage::get('TABLE_HEADING_CUSTOMER_ID')),
			array('align' => 'left', 'text' => sysLanguage::get('TABLE_HEADING_CUSTOMER_NAME')),
			array('align' => 'left', 'text' => sysLanguage::get('TABLE_HEADING_CUSTOMER_IP_ADDRESS')),
			array('align' => 'left', 'text' => sysLanguage::get('TABLE_HEADING_REDEEM_DATE')),
			array('align' => 'left', 'text' => sysLanguage::get('TABLE_HEADING_TOTAL_CUSTOMER_REDEMPTIONS')),
			array('align' => 'left', 'text' => sysLanguage::get('TABLE_HEADING_TOTAL_REDEMPTIONS'))
		)
	));
	
	$QredeemTrack = Doctrine_Query::create()
	->from('CouponRedeemTrack r')
	->leftJoin('r.Customers c')
	->where('r.coupon_id = ?', (int) $_GET['cID'])
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if (!empty($QredeemTrack)){
		$totalRedemptions = sizeof($QredeemTrack);
		foreach($QredeemTrack as $rInfo){
			$QcountCustomers = Doctrine_Query::create()
			->select('count(*) as total')
			->from('CouponRedeemTrack')
			->where('coupon_id = ?', (int) $rInfo['coupon_id'])
			->andWhere('customer_id = ?', (int) $rInfo['customer_id'])
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			$listTable->addBodyRow(array(
				'columns' => array(
					array('text' => $rInfo['customer_id']),
					array('text' => $rInfo['Customers']['customers_firstname'] . ' ' . $rInfo['Customers']['customers_lastname']),
					array('text' => $rInfo['redeem_ip']),
					array('text' => tep_date_short($rInfo['redeem_date'])),
					array('text' => $QcountCustomers[0]['total']),
					array('text' => $totalRedemptions)
				)
			));
		}
	}
	$infoBox->addContentRow($listTable);
	
	EventManager::attachActionResponse($infoBox->draw(), 'html');
?>