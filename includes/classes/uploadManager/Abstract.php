<?php
	abstract class UploadFileAbstract {
		private $fileInfo = array(
			'type'     => null,
			'size'     => 0,
			'name'     => null,
			'error'    => 0,
			'tmp_name' => null
		);

		abstract function moveTo($str);
		
		public function hasError(){
			return $this->fileInfo['error'];
		}
		
		public function setType($val){
			$this->fileInfo['type'] = $val;
		}
		
		public function setSize($val){
			$this->fileInfo['size'] = $val;
		}
		
		public function setName($val){
			$this->fileInfo['name'] = $val;
		}
		
		public function setTempName($val){
			$this->fileInfo['tmp_name'] = $val;
		}
		
		public function setError($val){
			$this->fileInfo['error'] = $val;
		}
		
		public function getType(){
			return $this->fileInfo['type'];
		}
		
		public function getSize(){
			return $this->fileInfo['size'];
		}
		
		public function getName(){
			return $this->fileInfo['name'];
		}
		
		public function getTempName(){
			return $this->fileInfo['tmp_name'];
		}
	}
?>