<?php
if (isset($_POST['sID'])){
	$Qstream = Doctrine_Query::create()
	->from('ProductsStreams')
	->where('stream_id = ?', (int) $_POST['sID'])
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if ($Qstream && $Qstream[0]['is_preview'] == 0){
		/*
		 * Ordered Streams
		 */
		if (isset($_POST['sID']) && isset($_POST['oID']) && isset($_POST['opID'])){
			$Qcheck = Doctrine_Query::create()
			->select('o.orders_id, op.orders_products_id, ops.orders_products_stream_id')
			->from('Orders o')
			->leftJoin('o.OrdersProducts op')
			->leftJoin('op.OrdersProductsStream ops')
			->where('ops.stream_id = ?', (int) $_POST['sID'])
			->andWhere('o.customers_id = ?', $userAccount->getCustomerId())
			->andWhere('o.orders_id = ?', (int) $_POST['oID'])
			->andWhere('op.orders_products_id = ?', (int) $_POST['opID']);
			
			EventManager::notify('OrdersProductsStreamUpdateViewsCheckBeforeExecute', &$Qcheck);
		
			$Result = $Qcheck->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if ($Result){
				$OrdersStream = Doctrine_Core::getTable('OrdersProductsStream')->find($Result[0]['OrdersProducts'][0]['OrdersProductsStream'][0]['orders_products_stream_id']);
				$OrdersStream->stream_count += 1;
			
				EventManager::notify('OrdersProductsStreamUpdateViewsBeforeSave', &$OrdersStream);
			
				$OrdersStream->save();
			
				EventManager::notify('OrdersProductsStreamUpdateViewsAfterSave', &$OrdersStream);
			}
		}
		/*
		 * Membership Streams
		 */
		elseif (isset($_POST['sID']) && $userAccount->isLoggedIn()){
			$QmembershipCheck = Doctrine_Query::create()
			->from('CustomersMembership')
			->where('customers_id = ?', $userAccount->getCustomerId())
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if ($QmembershipCheck){
				$Qstream = Doctrine_Query::create()
				->from('ProductsStreams')
				->where('stream_id = ?', (int) $_POST['sID'])
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
				if ($Qstream){
					$View = new CustomersStreamingViews();
					$View->customers_id = $userAccount->getCustomerId();
					$View->products_id = $Qstream[0]['products_id'];
					$View->stream_id = $Qstream[0]['stream_id'];
			
					EventManager::notify('CustomersMembershipStreamUpdateViewsBeforeSave', &$View);
			
					$View->save();
			
					EventManager::notify('CustomersMembershipStreamUpdateViewsAfterSave', &$View);
				}
			}
		}
	}
}

EventManager::attachActionResponse(array(
	'success' => true
), 'json');
?>