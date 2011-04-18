<?php
	$Product->products_price_download = (float)$_POST['products_price_download'];
	
	$ProductsDownloads = $Product->ProductsDownloads;
	if (isset($_POST['download_provider'])){
		$stillAvailable = array();
		foreach($_POST['download_provider'] as $idx => $val){
			$stillAvailable[] = $idx;
			$downloadId = $idx;
			$providerId = $val;
			$providerType = $_POST['download_provider_type'][$idx];
			$fileName = $_POST['download_file_name'][$idx];
			$displayName = $_POST['download_display_name'][$idx];
			
			$Download = $ProductsDownloads->get($downloadId);
			$Download->download_id = $downloadId;
			$Download->provider_id = $providerId;
			$Download->download_type = $providerType;
			$Download->file_name = $fileName;
			$Download->display_name = $displayName;
			
			EventManager::notify('NewProductDownloadExistsBeforeSave', &$Download, $idx);
		}
		
		foreach($ProductsDownloads->getKeys() as $key){
			if (!in_array($key, $stillAvailable)){
				$ProductsDownloads->remove($key);
			}
		}
	}
	
	if (isset($_POST['download_provider_new'])){
		foreach($_POST['download_provider_new'] as $idx => $val){
			$providerId = $val;
			$providerType = $_POST['download_provider_type_new'][$idx];
			$fileName = $_POST['download_file_name_new'][$idx];
			$displayName = $_POST['download_display_name_new'][$idx];
			
			$Download = $ProductsDownloads->get(null);
			$Download->provider_id = $providerId;
			$Download->download_type = $providerType;
			$Download->file_name = $fileName;
			$Download->display_name = $displayName;
			
			EventManager::notify('NewProductDownloadNewBeforeSave', &$Download, $idx);
		}
	}
	
	//echo '<pre>';print_r($Product->toArray());itwExit();
	$Product->save();
?>