<?php
	class UploadFileMock extends UploadFile {

		private $fsPath = null;

		function __construct($fileName){
			$this->setName($fileName);
		}

		function getPath(){
			return $this->fsPath;
		}

		function moveTo($destination){
			if ($this->hasError() || !is_null($this->fsPath)){
				return null;
			}
			return $this->fsPath = $destination;
		}
	}
?>