<?php
class DownloadProviderAmazonCloudFront extends DownloadProviderModule {

	public function __construct($config = false) {
		/*
		 * Default title and description for modules that are not yet installed
		 */
		$this->setTitle('Amazon Cloud Front');
		$this->setDescription('Files are stored in Amazon S3 and downloaded using Amazon Cloud Front');
		
		$this->init('amazoncloudfront');
		
		if ($config !== false && is_array($config)){
			$this->setProviderConfig($config);
		}
	}
	
	public function getServerAddress(){
		return $this->getConfigData('MODULE_DOWNLOAD_PROVIDER_AMAZONCLOUDFRONT_SERVER_ADDRESS');
	}
	
	public function getBucketName(){
		return $this->getConfigData('MODULE_DOWNLOAD_PROVIDER_AMAZONCLOUDFRONT_BUCKET');
	}
	
	public function getBucketKey(){
		return $this->getConfigData('MODULE_DOWNLOAD_PROVIDER_AMAZONCLOUDFRONT_BUCKET_KEY');
	}
	
	public function getBucketSecret(){
		return $this->getConfigData('MODULE_DOWNLOAD_PROVIDER_AMAZONCLOUDFRONT_BUCKET_SECRET');
	}
	
	public function getBucketPath(){
		return $this->getConfigData('MODULE_DOWNLOAD_PROVIDER_AMAZONCLOUDFRONT_BUCKET_PATH');
	}
	
	public function getKeyPairId(){
		return $this->getConfigData('MODULE_DOWNLOAD_PROVIDER_AMAZONCLOUDFRONT_KEY_PAIR_ID');
	}
	
	public function getPrivateKey(){
		return openssl_get_privatekey($this->getConfigData('MODULE_DOWNLOAD_PROVIDER_AMAZONCLOUDFRONT_PRIVATE_KEY'));
	}

	public function getDownloadTypes(){
		return array(
			'http',
			'https'
		);
	}

	public function processDownload($dInfo){
		$protocol = $dInfo['download_type'];
		$filePath = $this->getBucketPath();
		$filePath .= $dInfo['file_name'];
	
		$expires = time()+60;
		$Policy = array(
			'Statement' => array(
				array(
					'Resource' => $protocol . '://' . $this->getServerAddress() . $filePath,
					'Condition' => array(
						'DateLessThan' => array(
							'AWS:EpochTime' => $expires
						)
					)
				)
			)
		);

		$PolicyJson = trim(preg_replace('/\s+/', '', json_encode($Policy)));
		$PolicyJson = str_replace('\/', '/', $PolicyJson);
		openssl_sign($PolicyJson, $Signature, $this->getPrivateKey());
		$Signature = str_replace("\n", '', strtr(base64_encode($Signature), array('+' => '-','=' => '_','/' => '~')));
		
		$getVars = array(
			'Key-Pair-Id=' . $this->getKeyPairId(),
			'Expires=' . $expires,
			'Signature=' . $Signature
		);
		
		return array(
			'url' => $protocol . '://' . $this->getServerAddress() . $filePath . '?' . implode('&', $getVars)
		);
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
	
	public function getFileBrowser(){
		if (!class_exists('S3')) require_once(dirname(__FILE__) . '/classes/S3.php');
			
		//instantiate the class
		$s3 = new S3($this->getBucketKey(), $this->getBucketSecret());
		
		$contents = $s3->getBucket($this->getBucketName());
		ksort($contents);
		
		$dirArr = array();
		foreach($contents as $file){
			if (stristr($file['name'], '/')){
				$e = explode('/', $file['name']);
				for($i=0; $i<sizeof($e); $i++){
					if (empty($e[$i])) continue;

					if (isset($e[$i-1])) $e1 = $e[$i-1];
					if (isset($e[$i-2])) $e2 = $e[$i-2];
					if (isset($e[$i-3])) $e3 = $e[$i-3];
					if (isset($e[$i-4])) $e4 = $e[$i-4];
					if (isset($e[$i-5])) $e5 = $e[$i-5];
					if (isset($e[$i-6])) $e6 = $e[$i-6];
					if (isset($e[$i-7])) $e7 = $e[$i-7];
					if (isset($e[$i-8])) $e8 = $e[$i-8];
					if (isset($e[$i-9])) $e9 = $e[$i-9];
					if (isset($e[$i-10])) $e10 = $e[$i-10];
					
					if ($i==0) $this->addDir($e[$i], &$dirArr);
					if ($i==1) $this->addDir($e[$i], &$dirArr[$e1]);
					if ($i==2) $this->addDir($e[$i], &$dirArr[$e2][$e1]);
					if ($i==3) $this->addDir($e[$i], &$dirArr[$e3][$e2][$e1]);
					if ($i==4) $this->addDir($e[$i], &$dirArr[$e4][$e3][$e2][$e1]);
					if ($i==5) $this->addDir($e[$i], &$dirArr[$e5][$e4][$e3][$e2][$e1]);
					if ($i==6) $this->addDir($e[$i], &$dirArr[$e6][$e5][$e4][$e3][$e2][$e1]);
					if ($i==7) $this->addDir($e[$i], &$dirArr[$e7][$e6][$e5][$e4][$e3][$e2][$e1]);
					if ($i==8) $this->addDir($e[$i], &$dirArr[$e8][$e7][$e6][$e5][$e4][$e3][$e2][$e1]);
					if ($i==9) $this->addDir($e[$i], &$dirArr[$e9][$e8][$e7][$e6][$e5][$e4][$e3][$e2][$e1]);
					if ($i==10) $this->addDir($e[$i], &$dirArr[$e10][$e9][$e8][$e7][$e6][$e5][$e4][$e3][$e2][$e1]);
				}
			}else{
				$dirArr[] = $file['name'];
			}
		}
		
		asort($dirArr);
		
		$rootFiles = array();
		foreach($dirArr as $idx => $info){
			if (is_numeric($idx)){
				$rootFiles[] = $info;
				unset($dirArr[$idx]);
			}
		}
		
		$list = '<ul style="list-style:none;padding:0;margin:0;">';
		$this->recurseDir($dirArr, &$list);
		$this->recurseDir($rootFiles, &$list);
		$list .= '</ul>';
		
		return $list;
	}
	
	public function addDir($dirName, &$rootDir){
		if (stristr(substr($dirName, -4), '.')){
			$rootDir[] = $dirName;
		}elseif (!isset($rootDir[$dirName])){
			$rootDir[$dirName] = array();
		}
	}
	
	public function recurseDir($dirArr, &$list, $filesPath = ''){
		foreach($dirArr as $dirName => $files){
			if (is_numeric($dirName)){
				$list .= '<li style="border: 1px solid transparent" data-file_path="' . $filesPath . $files . '">' . 
					'<span class="ui-icon ui-icon-document" style="vertical-align:middle;"></span>' . 
					'<span class="providerFile" style="line-height:16px;vertical-align:middle;">' . $files . '</span>' . 
				'</li>';
			}else{
				$list .= '<li style="border: 1px solid transparent">' . 
					'<span class="ui-icon ui-icon-folder-collapsed" style="vertical-align:middle;"></span>' . 
					'<span class="providerFolder" style="line-height:16px;vertical-align:middle;">' . $dirName . '</span>' . 
					'<ul style="list-style:none;padding:0;margin:0;margin-left:16px">';
					
				$this->recurseDir($files, &$list, $filesPath . $dirName . '/');
				
				$list .= '</ul></li>';
			}
		}
	}
}
?>