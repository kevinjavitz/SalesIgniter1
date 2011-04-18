<?php
class StreamProviderAmazonCloudFront extends StreamProviderModule {

	public function __construct($config = false) {
		/*
		 * Default title and description for modules that are not yet installed
		 */
		$this->setTitle('Amazon Cloud Front');
		$this->setDescription('Streams are stored in Amazon S3 and streamed using Amazon Cloud Front');
		
		$this->init('amazoncloudfront');
		
		if ($config !== false && is_array($config)){
			$this->setProviderConfig($config);
		}
	}
	
	public function getServerAddress(){
		return $this->getConfigData('MODULE_STREAM_PROVIDER_AMAZONCLOUDFRONT_SERVER_ADDRESS');
	}
	
	public function getBucketName(){
		return $this->getConfigData('MODULE_STREAM_PROVIDER_AMAZONCLOUDFRONT_BUCKET');
	}
	
	public function getBucketPath(){
		return $this->getConfigData('MODULE_STREAM_PROVIDER_AMAZONCLOUDFRONT_BUCKET_PATH');
	}
	
	public function getKeyPairId(){
		return $this->getConfigData('MODULE_STREAM_PROVIDER_AMAZONCLOUDFRONT_KEY_PAIR_ID');
	}
	
	public function getPrivateKey(){
		return $this->getConfigData('MODULE_STREAM_PROVIDER_AMAZONCLOUDFRONT_PRIVATE_KEY');
	}

	public function getStreamTypes(){
		return array(
			'http',
			'rtmp',
			'rtmpe'
		);
	}

	public function getFlowplayerConfig($sInfo){
		$protocol = $sInfo['stream_type'];
		$ext = substr($sInfo['file_name'], -3);
		$filePath = substr($this->getBucketPath(), 1, strlen($this->getBucketPath())) . substr($sInfo['file_name'], 0, -4);

		$expires = time()+60;
		$Policy = array(
			'Statement' => array(
				array(
					'Resource' => $filePath,
					'Condition' => array(
						'DateLessThan' => array(
							'AWS:EpochTime' => $expires
						)
					)
				)
			)
		);

		$PolicyJson = str_replace('\/', '/', json_encode($Policy));
		$PolicyJson = $this->decode_uhex($PolicyJson);
		$PolicyEncoded = strtr(base64_encode($PolicyJson), '+=/', '-_~');
		
		// Generate the signature
		openssl_sign($PolicyJson, $Signature, $this->getPrivateKey());
		$Signature = strtr(base64_encode($Signature), '+=/', '-_~');

		$getVars = array(
			'Key-Pair-Id=' . $this->getKeyPairId(),
			'Expires=' . $expires,
			'Signature=' . $Signature
		);
		
		$config = array(
/*			'key' => 'Commercial Key',
			'logo' => array(
				'url' => '/streamer/flowplayer/images/logo.png',
				'fullscreenOnly' => false,
				'top' => 20,
				'right' => 20,
				'opacity' => 0.4,
				'displayTime' => 0,
				'linkUrl' => itw_app_link(null, 'index', 'default')
			),*/
		);
		
		if ($protocol != 'http'){
			$config['plugins'] = array(
				'controls' => array(
					'url' => 'streamer/flowplayer/flowplayer.controls-3.2.3.swf'
				),
				$protocol => array(
					'url' => 'streamer/flowplayer/flowplayer.rtmp-3.2.3.swf',
					'netConnectionUrl' => $protocol . '://' . $this->getServerAddress() . '/cfx/st'
				)
			);
			
			$config['clip'] = array(
				'url' => $ext . ':' . urlencode($filePath . '?' . implode('&', $getVars)),
				'provider' => $protocol,
				'autoPlay' => false,
				'autoBuffering' => false
			);
		}else{
			$config['plugins'] = array(
				'controls' => array(
					'url' => 'streamer/flowplayer/flowplayer.controls-3.2.3.swf'
				)
			);
			
			$config['clip'] = array(
				'url' => urlencode($protocol . '://' . $this->getServerAddress() . '/cfx/st/' . $filePath . '?' . implode('&', $getVars)),
				'autoPlay' => false,
				'autoBuffering' => false
			);
		}

		return $config;
	}

	/**
	 * Function: decode_uhex()
	 * 	Decodes \uXXXX entities into their real unicode character equivalents.
	 *
	 * Parameters:
	 * 	$s - _string_ (Required) The string to decode.
	 *
	 * Returns:
	 * 	_string_ The decoded string.
	 */
	private function decode_uhex($s){
		preg_match_all('/\\\u([0-9a-f]{4})/i', $s, $matches);
		$matches = $matches[count($matches) - 1];
		$map = array();
		
		foreach ($matches as $match){
			if (!isset($map[$match])){
				$map['\u' . $match] = html_entity_decode('&#' . hexdec($match) . ';', ENT_NOQUOTES, 'UTF-8');
			}
		}
		
		return str_replace(array_keys($map), $map, $s);
	}
}
?>