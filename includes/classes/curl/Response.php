<?php
	class CurlResponse {
		
		public function __construct($CurlRequest){
			$this->CurlRequest = $CurlRequest;
			$this->response = curl_exec($this->CurlRequest->requestObj);
			
			$this->info = curl_getinfo($this->CurlRequest->requestObj);
			$this->info['errno'] = curl_errno($this->CurlRequest->requestObj);
			$this->info['error'] = curl_error($this->CurlRequest->requestObj);
			
			curl_close($this->CurlRequest->requestObj);
		}

		public function getInfo($key = ''){
			if (!empty($key)){
				$return = $this->info[$key];
			}else{
				$return = $this->info;
			}
		    return $return;
		}

		public function hasError(){
			return $this->getInfo('errno');
		}
		
		public function getError(){
			return $this->getInfo('error');
		}
		
		public function getResponse(){
			return $this->response;
		}
		
		public function getDataRaw(){
			return $this->CurlRequest->getDataRaw();
		}
	}
?>