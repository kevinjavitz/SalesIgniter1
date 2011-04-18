<?php
if (isset($_GET['sID'])){
	$Qstream = Doctrine_Query::create()
	->from('ProductsStreams')
	->where('stream_id = ?', (int) $_GET['sID'])
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if ($Qstream && $Qstream[0]['is_preview'] == 0){
		/*
		 * Ordered Streams
		 */
		if (isset($_GET['sID']) && isset($_GET['oID']) && isset($_GET['opID'])){
			$Qcheck = Doctrine_Query::create()
			->from('Orders o')
			->leftJoin('o.OrdersProducts op')
			->leftJoin('op.OrdersProductsStream ops')
			->leftJoin('ops.ProductsStreams ps')
			->where('ops.stream_id = ?', (int) $_GET['sID'])
			->andWhere('o.customers_id = ?', $userAccount->getCustomerId())
			->andWhere('o.orders_id = ?', (int) $_GET['oID'])
			->andWhere('op.orders_products_id = ?', (int) $_GET['opID']);
			
			EventManager::notify('OrdersProductsStreamViewCheckBeforeExecute', $Qcheck);
		
			$Result = $Qcheck->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if ($Result){
				$content = htmlBase::newElement('div')
				->setId('streamPlayer')
				->attr('data-oID', $Result[0]['orders_id'])
				->attr('data-opID', $Result[0]['OrdersProducts'][0]['orders_products_id'])
				->attr('data-pID', $Result[0]['OrdersProducts'][0]['products_id'])
				->attr('data-sID', $Result[0]['OrdersProducts'][0]['OrdersProductsStream'][0]['stream_id'])
				->css(array(
					'display' => 'block',
					'width' => '350px',
					'height' => '270px',
					'margin-left' => 'auto',
					'margin-right' => 'auto'
				));
			}
		}
		/*
		 * Membership Streams
		 */
		elseif (isset($_GET['sID']) && $userAccount->isLoggedIn()){
			$QmembershipCheck = Doctrine_Query::create()
			->from('CustomersMembership')
			->where('customers_id = ?', $userAccount->getCustomerId())
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if ($QmembershipCheck){
				$Qstream = Doctrine_Query::create()
				->from('ProductsStreams')
				->where('stream_id = ?', (int) $_GET['sID'])
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
				if ($Qstream){
					$content = htmlBase::newElement('div')
					->setId('streamPlayer')
					->attr('data-pID', $Qstream[0]['products_id'])
					->attr('data-sID', $Qstream[0]['stream_id'])
					->css(array(
						'display' => 'block',
						'width' => '350px',
						'height' => '270px',
						'margin-left' => 'auto',
						'margin-right' => 'auto'
					));
				}
			}
		}
	}
}

if (isset($content)){			
	$pageContent->set('pageContent', $content->draw());
}else{
	$pageContent->set('pageContent', sysLanguage::get('TEXT_INFO_STREAM_PERMISSION_DENIED'));
}
?>