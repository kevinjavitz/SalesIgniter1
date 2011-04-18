<?php
if (isset($_GET['dID'])){
	$Qdownload = Doctrine_Query::create()
	->from('ProductsDownloads')
	->where('download_id = ?', (int) $_GET['dID'])
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if ($Qdownload){
		/*
		 * Ordered Downloads
		 */
		if (isset($_GET['dID']) && isset($_GET['oID']) && isset($_GET['opID'])){
			$Qcheck = Doctrine_Query::create()
			->from('Orders o')
			->leftJoin('o.OrdersProducts op')
			->leftJoin('op.OrdersProductsDownload opd')
			->leftJoin('opd.ProductsDownloads pd')
			->where('opd.download_id = ?', (int) $_GET['dID'])
			->andWhere('o.customers_id = ?', $userAccount->getCustomerId())
			->andWhere('o.orders_id = ?', (int) $_GET['oID'])
			->andWhere('op.orders_products_id = ?', (int) $_GET['opID']);
			
			EventManager::notify('OrdersProductsDownloadGetCheckBeforeExecute', $Qcheck);
		
			$Result = $Qcheck->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if ($Result){
				$productId = (int) $Result[0]['OrdersProducts'][0]['OrdersProductsDownload'][0]['ProductsDownloads']['products_id'];
				$downloadId = (int) $Result[0]['OrdersProducts'][0]['OrdersProductsDownload'][0]['ProductsDownloads']['download_id'];
		
				$extDownload = $appExtension->getExtension('downloadProducts');
		
				$Download = $extDownload->getDownload($productId, $downloadId);
				if ($Download){
					$Provider = $extDownload->getProviderModule(
						$Download['ProductsDownloadProviders']['provider_module'],
						$Download['ProductsDownloadProviders']['provider_module_settings']
					);
					$success = $Provider->processDownload($Download);
				}
			}
		}
	}
}

if (isset($success) && ){
	if ($success === true){
		echo 'Your Download Should Begin Shortly.';
	}elseif (is_array($success)){
		header('Location: ' . $success['url']);
	}
}else{
	echo sysLanguage::get('TEXT_INFO_STREAM_PERMISSION_DENIED');
}
?>