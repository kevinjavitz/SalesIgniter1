<?php
class StreamProviderAmazons3 extends StreamProviderModule {

	public function __construct($config = false) {
		/*
		 * Default title and description for modules that are not yet installed
		 */
		$this->setTitle('Amazon S3');
		$this->setDescription('Streams are stored in Amazon S3');
		
		$this->init('amazons3');
		
		if ($config !== false && is_array($config)){
			$this->setProviderConfig($config);
		}
	}
	
	public function getBucketName(){
		return $this->getConfigData('MODULE_STREAM_PROVIDER_AMAZONS3_BUCKET');
	}
	
	public function getBucketPath(){
		return $this->getConfigData('MODULE_STREAM_PROVIDER_AMAZONS3_BUCKET_PATH');
	}
	
	public function getKey(){
		return $this->getConfigData('MODULE_STREAM_PROVIDER_AMAZONS3_ACCESS_KEY');
	}
	
	public function getSecret(){
		return $this->getConfigData('MODULE_STREAM_PROVIDER_AMAZONS3_SECRET_KEY');
	}

	public function getStreamTypes(){
		return array(
			'http'
		);
	}

	public function getFlowplayerConfig($sInfo){
		$protocol = $sInfo['stream_type'];
		$filePath = $this->getBucketName() . $this->getBucketPath() . $sInfo['file_name'];

		$expires = time()+60;
		$string_to_sign = 'GET' . "\n\n\n" .
			$expires . "\n" .
			'/' . $filePath;

		$getVars = array(
			'AWSAccessKeyId=' . $this->getKey(),
			'Expires=' . $expires,
			'Signature=' . urlencode($this->amazon_hmac($string_to_sign))
		);

		return array(
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
			'plugins' => array(
				'controls' => array(
					'url' => 'streamer/flowplayer/flowplayer.controls-3.2.3.swf'
				)
			),
			'clip' => array(
				'url' => urlencode($protocol . '://s3.amazonaws.com/' . $filePath . '?' . implode('&', $getVars)),
				'autoPlay' => false,
				'autoBuffering' => false
			)
		);
	}

	public function amazon_hmac($stringToSign){
		// helper function binsha1 for amazon_hmac (returns binary value of sha1 hash)
		if (!function_exists('binsha1')){
			if (version_compare(phpversion(), "5.0.0", ">=")){
				function binsha1($d) { return sha1($d, true); }
			}else{
				function binsha1($d) { return pack('H*', sha1($d)); }
			}
		}
		
		$aws_secret = $this->getSecret();
		
		if (strlen($aws_secret) == 40) $aws_secret = $aws_secret . str_repeat(chr(0), 24);
		
		$ipad = str_repeat(chr(0x36), 64);
		$opad = str_repeat(chr(0x5c), 64);
		
		$hmac = binsha1(($aws_secret^$opad) . binsha1(($aws_secret^$ipad) . $stringToSign));
		
		return base64_encode($hmac);
	}
	
	public function getFileBrowser(){
		if (!class_exists('S3')) require_once(dirname(__FILE__) . '/classes/S3.php');
			
		//instantiate the class
		$s3 = new S3($this->getKey(), $this->getSecret());
		
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