<?php
	$Product->products_price_stream = (float)$_POST['products_price_stream'];

	$ProductsStreams = $Product->ProductsStreams;
	if (isset($_POST['stream_provider'])){
		$stillAvailable = array();
		foreach($_POST['stream_provider'] as $idx => $val){
			$stillAvailable[] = $idx;
			$streamId = $idx;
			$providerId = $val;
			$providerType = $_POST['stream_provider_type'][$idx];
			$fileName = $_POST['stream_file_name'][$idx];
			$displayName = $_POST['stream_display_name'][$idx];
			$isPreview = (isset($_POST['preview_stream'][$idx]) ? 1 : 0);
			
			$Stream = $ProductsStreams->get($streamId);
			$Stream->stream_id = $streamId;
			$Stream->provider_id = $providerId;
			$Stream->stream_type = $providerType;
			$Stream->file_name = $fileName;
			$Stream->display_name = $displayName;
			$Stream->is_preview = $isPreview;
			//$Stream->save();
			
			EventManager::notify('NewProductStreamExistsBeforeSave', &$Stream, $idx);
		}
		
		foreach($ProductsStreams->getKeys() as $key){
			if (!in_array($key, $stillAvailable)){
				$ProductsStreams->remove($key);
			}
		}
	}
	
	if (isset($_POST['stream_provider_new'])){
		foreach($_POST['stream_provider_new'] as $idx => $val){
			$providerId = $val;
			$providerType = $_POST['stream_provider_type_new'][$idx];
			$fileName = $_POST['stream_file_name_new'][$idx];
			$displayName = $_POST['stream_display_name_new'][$idx];
			$isPreview = (isset($_POST['preview_stream_new'][$idx]) ? 1 : 0);
			
			$Stream = $ProductsStreams->get(null);
			$Stream->provider_id = $providerId;
			$Stream->stream_type = $providerType;
			$Stream->file_name = $fileName;
			$Stream->display_name = $displayName;
			$Stream->is_preview = $isPreview;
			
			EventManager::notify('NewProductStreamNewBeforeSave', &$Stream, $idx);
			//$Product->ProductsStreams->add($Stream);
		}
	}
	
	//echo '<pre>';print_r($Product->toArray());itwExit();
	$Product->save();
?>