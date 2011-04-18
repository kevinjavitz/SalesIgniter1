<?php
	class CurlRequest {
		private $options = array();
		private $dataRaw = null;
		private $dataFormatted = null;
		
		public function __construct($url = null){
			if (!function_exists('curl_init')) trigger_error('The cURL extension is not installed.');
			
			$this->requestObj = curl_init();
			
			if (is_null($url) === false){
				$this->setUrl($url);
			}
			$this->setHeader(false);
			$this->setReturnTransfer(true);
			$this->setSendMethod('post');
			$this->setOption(CURLOPT_VERBOSE, true);
			$this->setOption(CURLOPT_SSL_VERIFYPEER, false); //Windows 2003 Compatibility
		}
		
		public function setSendMethod($method = 'get'){
			if ($method == 'post'){
				$this->setOption(CURLOPT_POST, true);
			}else{
				$this->setOption(CURLOPT_POST, false);
			}
		}
		
		public function formatData($format = 'url'){
			$data = $this->getDataRaw();
			if (is_array($data)){
				$dataStr = array();
				if ($format == 'url'){
					foreach($data as $k => $v) {
						$dataStr[] = $k . '=' . urlencode(str_replace(',', '', $v));
					}
					$dataStr = implode('&', $dataStr);
				}elseif ($format == 'xml'){
					$dataStr[] = '<?xml version="1.0"?>';
					foreach($data as $root => $tags){
						$dataStr[] = '<' . $root . '>';
						foreach($tags as $k => $v) {
							$dataStr[] = '<' . $k . '>' . $v . '</' . $k . '>';
						}
						$dataStr[] = '</' . $root . '>';
					}
					$dataStr = implode('', $dataStr);
				}
			}else{
				$dataStr = $data;
			}
			return $dataStr;
		}
		
		public function setData($data, $format = 'url', $dataBefore = '', $dataAfter = ''){
			$this->setDataRaw($data);
			$this->setDataFormatted($dataBefore . $this->formatData($format) . $dataAfter);
			if (isset($this->options[CURLOPT_POST]) && $this->options[CURLOPT_POST] === true){
				$this->setOption(CURLOPT_POSTFIELDS, $this->getDataFormatted());
			}
		}
		
		public function setDataRaw($val){
			$this->dataRaw = $val;
		}
		
		public function setDataFormatted($val){
			$this->dataFormatted = $val;
		}
		
		public function getDataRaw(){
			return $this->dataRaw;
		}
		
		public function getDataFormatted(){
			return $this->dataFormatted;
		}
		
		public function setUrl($url){
			$this->setOption(CURLOPT_URL, $url);
		}
		
		public function setHeader($val){
			$this->setOption(CURLOPT_HEADER, $val);
		}
		
		public function setReturnTransfer($val){
			$this->setOption(CURLOPT_RETURNTRANSFER, $val);
		}
		
		public function close(){
			curl_close($this->requestObj);
		}
		
		public function setOption($optionName, $optionValue){
			if (!@curl_setopt($this->requestObj, $optionName, $optionValue)){
				die($optionName . '::' . $optionValue);
			}
			
			$this->options[$optionName] = $optionValue;
		}
		
		public function execute(){
			$Response = new CurlResponse($this);
			return $Response;
		}
	}
?>