<?php
if (isset($_POST['dID'])){
	$Qdownload = Doctrine_Query::create()
	->from('ProductsDownloads')
	->where('download_id = ?', (int) $_POST['dID'])
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if ($Qdownload){
		/*
		 * Ordered Downloads
		 */
		if (isset($_POST['dID']) && isset($_POST['oID']) && isset($_POST['opID'])){
			$Qcheck = Doctrine_Query::create()
			->select('o.orders_id, op.orders_products_id, opd.orders_products_download_id')
			->from('Orders o')
			->leftJoin('o.OrdersProducts op')
			->leftJoin('op.OrdersProductsDownload opd')
			->where('opd.download_id = ?', (int) $_POST['dID'])
			->andWhere('o.customers_id = ?', $userAccount->getCustomerId())
			->andWhere('o.orders_id = ?', (int) $_POST['oID'])
			->andWhere('op.orders_products_id = ?', (int) $_POST['opID']);
			
			EventManager::notify('OrdersProductsDownloadUpdateDownloadsCheckBeforeExecute', &$Qcheck);
		
			$Result = $Qcheck->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if ($Result){
				$OrdersDownload = Doctrine_Core::getTable('OrdersProductsDownload')->find($Result[0]['OrdersProducts'][0]['OrdersProductsDownload'][0]['orders_products_download_id']);
				$OrdersDownload->download_count += 1;
			
				EventManager::notify('OrdersProductsDownloadUpdateDownloadsBeforeSave', &$OrdersDownload);
			
				$OrdersDownload->save();
			
				EventManager::notify('OrdersProductsDownloadUpdateDownloadsAfterSave', &$OrdersDownload);
			}
		}
	}
}

EventManager::attachActionResponse(array(
	'success' => true
), 'json');
?>