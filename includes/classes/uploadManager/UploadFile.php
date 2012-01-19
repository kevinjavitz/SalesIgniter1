<?php
class UploadFile extends UploadFileAbstract
{

	private $fsPath = null;

	/**
	 * @var SystemFtp
	 */
	private $ftpRes = false;

	public function __construct($formFieldName) {
		if (!isset($_FILES[$formFieldName])){
			throw new UploadFileException("unknown $formFieldName");
		}

		$fileObj = $_FILES[$formFieldName];

		$this->setSize($fileObj['size']);
		$this->setName($fileObj['name']);
		$this->setType($fileObj['type']);
		$this->setTempName($fileObj['tmp_name']);
		$this->setError($fileObj['error']);
	}

	public function setFtpRes(SystemFtp &$ftpRes){
		$this->ftpRes =& $ftpRes;
	}

	public function getPath() {
		return $this->fsPath;
	}

	public function moveTo($destination) {
		$success = true;
		if (is_null($this->fsPath) === false){
			$success = false;
		}else{
			if (substr($destination, -1) != '/') {
				$destination .= '/';
			}

			if (is_dir($destination)){
				$destination .= $this->getName();
			}

			if ($this->ftpRes){
				if (!$this->ftpRes->copyFile($this->getTempName(), $destination)){
					$success = false;
				}
			}else{
				if (!move_uploaded_file($this->getTempName(), $destination)){
					$success = false;
				}
			}

			if ($success === true && is_file($destination)){
				$this->fsPath = realpath($destination);
			}
		}

		return $success;
	}
}

?>