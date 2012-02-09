<?php
class UploadManagerException extends Exception
{

	private $errorDetail = 'less';

	private $errorType = 'warning';

	const CODE_0 = 'The file was uploaded successfully.';

	const CODE_1 = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';

	const CODE_2 = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';

	const CODE_3 = 'The uploaded file was only partially uploaded';

	const CODE_4 = 'No file was uploaded';

	const CODE_6 = 'Missing a temporary folder';

	const CODE_7 = 'Failed to write file to disk';

	const CODE_8 = 'File upload stopped by extension';

	const CODE_50 = 'File type is not allowed';

	const CODE_51 = 'Destination directory does not exist.';

	const CODE_52 = 'Destination directory is not writable.';

	const CODE_53 = 'File size exceeds the set limit for uploads.';

	const CODE_70 = 'chmod is not allowed.';

	const CODE_99 = 'Error Uploading File.';

	const CODE_ = 'Unknown error, file not saved.';

	public function __construct($code, $prepend = '') {
		parent::__construct(constant(__CLASS__ . '::CODE_' . $code) . $prepend, $code);

		$type = $this->getErrorType();
		if ($code == 0){
			$type = 'success';
		}
		elseif ($code > 0 && $code < 70) {
			$type = 'error';
		}
		elseif ($code > 69) {
			$type = 'warning';
		}
		$this->setErrorType($type);
	}

	public function setDetailLevel($val) {
		$this->errorDetail = $val;
	}

	public function getDetailLevel() {
		return $this->errorDetail;
	}

	public function setErrorType($val) {
		$this->errorType = $val;
	}

	public function getErrorType() {
		return $this->errorType;
	}
}

?>