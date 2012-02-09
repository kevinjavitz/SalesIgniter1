<?php
require(dirname(__FILE__) . '/uploadManager/Exception.php');
require(dirname(__FILE__) . '/uploadManager/Abstract.php');
require(dirname(__FILE__) . '/uploadManager/UploadFile.php');
require(dirname(__FILE__) . '/uploadManager/UploadFileMock.php');

class UploadManager implements SplSubject
{

	private $destination;

	private $permissions;

	private $extensions;

	private $status = 0;

	private $observers = array();

	/**
	 * @var UploadManagerException
	 */
	private $exceptionObj;

	/**
	 * @var SystemFTP
	 */
	private $ftpRes;

	const   ERROR_TYPE_NOT_ALLOWED = 50;

	const   ERROR_DESTINATION_DOES_NOT_EXIST = 51;

	const   ERROR_DESTINATION_NOT_WRITABLE = 52;

	const   ERROR_FILE_SIZE_TOO_LARGE = 53;

	const   ERROR_UPLOAD_ERROR = 99;

	const   WARNING_CHMOD_NOT_ALLOWED = 70;

	public function __construct($destination = '', $permissions = '777', $extensions = '') {
		$this->setDestination($destination);
		$this->setPermissions($permissions);
		$this->setExtensions($extensions);

		$this->ftpRes = new SystemFTP();
		$this->ftpRes->connect();
	}

	public function attach(SplObserver $obs) {
		$id = spl_object_hash($obs);

		$this->observers[$id] = $obs;
	}

	public function detach(SplObserver $obs) {
		$id = spl_object_hash($obs);

		unset($this->observers[$id]);
	}

	public function notify() {
		foreach($this->observers as $obs){
			$obs->update($this);
		}
	}

	public function processFile(UploadFile $file) {
		$this->assert(($this->extensionIsAllowed($file) === false), self::ERROR_TYPE_NOT_ALLOWED);
		$this->assert(($this->destinationExists() === false), self::ERROR_DESTINATION_DOES_NOT_EXIST);
		$this->assert(($this->destinationIsWritable() === false), self::ERROR_DESTINATION_NOT_WRITABLE);
		$this->assert(($file->hasError() !== UPLOAD_ERR_OK), $file->hasError());

		if ($this->status == 0){
			$file->setFtpRes($this->ftpRes);
			if (!$file->moveTo($this->destination)){
				$this->status = 0;
				$this->assert(true, self::ERROR_UPLOAD_ERROR);
			}else{
				$this->status = 0;
				$this->assert(true, 0);
				return true;
			}
		}
		return false;
	}

	public function assert($condition, $code) {
		if ($condition === true){
			$this->status = $code;
			$prepend = '';
			if ($this->ftpRes && $this->ftpRes->hasError()){
				$ftpErr = $this->ftpRes->getError();
				$prepend .= '<br>FTP Error: ' . $ftpErr['message'];
			}
			$this->exceptionObj = new UploadManagerException($code, $prepend);
			$this->notify();
		}
	}

	public function getException() {
		return $this->exceptionObj;
	}

	public function status() {
		return $this->status;
	}

	public function setExtensions($val) {
		if (!empty($val)){
			if (is_array($val)){
				$this->extensions = $val;
			}
			else {
				$this->extensions = array($val);
			}
		}
		else {
			$this->extensions = array();
		}
	}

	public function setPermissions($val) {
		$this->permissions = octdec($val);
	}

	public function setDestination($val) {
		$this->destination = $val;
	}

	public function getDestination() {
		return $this->destination;
	}

	public function extensionIsAllowed(UploadFile $file) {
		if (sizeof($this->extensions) > 0){
			$fileName = $file->getName();
			if (!in_array(strtolower(substr($fileName, strrpos($fileName, '.') + 1)), $this->extensions)){
				return false;
			}
		}
		return true;
	}

	public function destinationIsWritable() {
		if (!is_writeable($this->destination)){
			/* Using ftp user which is able to write to all folders/files */
			if ($this->ftpRes){
				return true;
			}
			return false;
		}
		return true;
	}

	public function destinationExists() {
		if (is_dir($this->destination)){
			return true;
		}
		return false;
	}
}

?>