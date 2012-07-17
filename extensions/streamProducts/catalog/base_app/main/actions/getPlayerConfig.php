<?php
	$json = array('success' => false);
	
	
	if (isset($_GET['provider'])) {
		$provider_id = (int)$_GET['provider'];		
		
		$extStream = $appExtension->getExtension('streamProducts');
		$Provider = $extStream->getProviderModuleById($provider_id);
		
		if ($Provider!==null) {
			$config = $Provider->getFlowplayerConfig(array(
				'stream_type' => $_GET['type'],
				'file_name' => $_GET['file'],
			));
			$config['width'] = sysConfig::get('EXTENSION_STREAMPRODUCTS_STREAMER_WIDTH');
			$json = array(
				'success' => true,
				'config' => $config
			);
		} else {
			$json = array('success'=>false);
		}
		
	} elseif (isset($_POST['oID'])){
		$orderId = (int) $_POST['oID'];
		$orderProductId = (int) $_POST['opID'];
		$productId = (int) $_POST['pID'];
		$streamId = (int) $_POST['sID'];
		
		$extStream = $appExtension->getExtension('streamProducts');
		
		$Stream = $extStream->getStream($productId, $streamId);
		if ($Stream){
			$Provider = $extStream->getProviderModule(
				$Stream['ProductsStreamProviders']['provider_module'],
				$Stream['ProductsStreamProviders']['provider_module_settings']
			);
			$StreamInfo = $Stream;
			$StreamInfo['oID'] = $orderId;
			$StreamInfo['opID'] = $orderProductId;
			$config = $Provider->getFlowplayerConfig($StreamInfo);
			$config['width'] = sysConfig::get('EXTENSION_STREAMPRODUCTS_STREAMER_WIDTH');
			$json = array(
				'success' => true,
				'config' => $config
			);
		}
	}elseif (isset($_POST['pID']) && isset($_POST['sID'])){
		$productId = (int) $_POST['pID'];
		$streamId = (int) $_POST['sID'];
		
		$extStream = $appExtension->getExtension('streamProducts');
		
		$Stream = $extStream->getStream($productId, $streamId);
		if ($Stream){
			$Provider = $extStream->getProviderModule(
				$Stream['ProductsStreamProviders']['provider_module'],
				$Stream['ProductsStreamProviders']['provider_module_settings']
			);
			$config = $Provider->getFlowplayerConfig($Stream);
			$config['width'] = sysConfig::get('EXTENSION_STREAMPRODUCTS_STREAMER_WIDTH');
			$json = array(
				'success' => true,
				'config' => $config
			);
		}
	}elseif (isset($_GET['pID']) && isset($_GET['sID'])){
		$productId = (int) $_GET['pID'];
		$streamId = (int) $_GET['sID'];

		$extStream = $appExtension->getExtension('streamProducts');

		$PreviewStream = $extStream->getPreview($productId);
		if ($streamId == $PreviewStream['stream_id']){
			$Provider = $extStream->getProviderModule(
				$PreviewStream['ProductsStreamProviders']['provider_module'],
				$PreviewStream['ProductsStreamProviders']['provider_module_settings']
			);
			$config = $Provider->getFlowplayerConfig($PreviewStream);
			$config['width'] = sysConfig::get('EXTENSION_STREAMPRODUCTS_STREAMER_WIDTH');

			$json = array(
				'success' => true,
				'config' => $config
			);
		}
	}
	
	EventManager::attachActionResponse($json, 'json');
?>