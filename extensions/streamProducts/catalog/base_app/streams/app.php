<?php
/*
	Stream Products Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

$appContent = $App->getAppContentFile();

if ($App->getAppPage() == 'view'){
	$App->addJavascriptFile('streamer/flowplayer/flowplayer-3.2.4.min.js');
}

if ($App->getAppPage() == 'listing'){
	$error = false;
	if ($userAccount->isLoggedIn() === true){
		$Qaccount = Doctrine_Query::create()
			->from('CustomersMembership c')
			->leftJoin('c.Membership m')
			->where('c.customers_id = ?', $userAccount->getCustomerId())
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Qaccount > 0){
			if ($Qaccount[0]['Membership']['streaming_allowed'] == '1'){
				$Qcheck = Doctrine_Query::create()
					->select('count(*) as total')
					->from('CustomersStreamingViews')
					->where('customers_id = ?', $userAccount->getCustomerId())
					->andWhere('date_added >= ?', $Qaccount[0]['membership_start_streaming'])
					->andWhere('date_added <= ?', $Qaccount[0]['membership_end_streaming'])
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
				if ($Qcheck[0]['total'] >= $Qaccount[0]['Membership']['streaming_no_of_views']){
					$messageStack->addSession('pageStack', sysLanguage::get('TEXT_EXCEEDED_VIEWS'), 'warning');
					$error = true;
				}
			} else{
				$messageStack->addSession('pageStack', sprintf(sysLanguage::get('TEXT_NOT_ALLOWED_STREAMING'), itw_app_link('checkoutType=rental', 'checkout', 'default', 'SSL')), 'warning');
				$error = true;
			}
		} else{
			$messageStack->addSession('pageStack', sprintf(sysLanguage::get('TEXT_NOT_RENTAL_CUSTOMER'), itw_app_link('checkoutType=rental', 'checkout', 'default', 'SSL'), itw_app_link(null, 'account', 'login')), 'warning');
			$error = true;
		}
	} else{
		$messageStack->addSession('pageStack', sprintf(sysLanguage::get('TEXT_NOT_RENTAL_CUSTOMER'), itw_app_link('checkoutType=rental', 'checkout', 'default', 'SSL'), itw_app_link(null, 'account', 'login')), 'warning');
		$error = true;
	}

	if ($error === true){
		tep_redirect(itw_app_link('products_id=' . (int)$_GET['pID'], 'product', 'info'));
	} else{
		$Qstreams = Doctrine_Query::create()
			->from('ProductsStreams')
			->where('products_id = ?', (int)$_GET['pID'])
			->andWhere('is_preview = ?', 0)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Qstreams && sizeof($Qstreams) == 1){
			tep_redirect(itw_app_link('appExt=streamProducts&pID=' . (int)$_GET['pID'] . '&sID=' . $Qstreams[0]['stream_id'], 'streams', 'view'));

		}

		$QproductsName = Doctrine_Query::create()
			->select('products_name')
			->from('ProductsDescription')
			->where('products_id = ?', (int)$_GET['pID'])
			->andWhere('language_id = ?', Session::get('languages_id'))
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	}
}
?>