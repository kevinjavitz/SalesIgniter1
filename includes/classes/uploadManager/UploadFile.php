<?php
	class UploadFile extends UploadFileAbstract {

		private $fsPath = null;

		public function __construct($formFieldName){
			if (!isset($_FILES[$formFieldName])) {
				throw new UploadFileException("unknown $formFieldName");
			}
			
			$fileObj = $_FILES[$formFieldName];
			
			$this->setSize($fileObj['size']);
			$this->setName($fileObj['name']);
			$this->setType($fileObj['type']);
			$this->setTempName($fileObj['tmp_name']);
			$this->setError($fileObj['error']);
		}

		public function getPath(){
			return $this->fsPath;
		}

		public function moveTo($destination){

			if (is_null($this->fsPath) === false){
				return null;
			}

			if (substr($destination, -1) != '/') $destination .= '/';
			
			if (is_dir($destination)) {
				$destination .= $this->getName();
			}

			move_uploaded_file($this->getTempName(), $destination);

			if (is_file($destination)) {
				$this->fsPath = realpath($destination);
				return $this->fsPath;
			}

			return null;
		}
	}
?>