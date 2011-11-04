<?php
	$json = array('success' => false);
	
	if (isset($_POST['oID'])){
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
			
			$json = array(
				'success' => true,
				'config' => $config
			);
		}
	}
	
	EventManager::attachActionResponse($json, 'json');
?>