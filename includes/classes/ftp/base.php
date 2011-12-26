<?php
class SystemFtpSplFileInfo extends SplFileInfo
{

	public function getExt() {
		if (method_exists($this, 'getExtension')){
			return parent::getExtension();
		}
		else {
			if (strpos($this->getBasename(), '.') > 0){
				return substr($this->getBasename(), -(strrpos($this->getBasename(), '.')));
			}
			else {
				return '';
			}
		}
	}

	public function isDir() {
		return ($this->getExt() == '');
	}

	public function isFile() {
		return ($this->getExt() != '');
	}

	public function isWritable() {
		return is_writable(sysConfig::getDirFsCatalog() . $this->getPathname());
	}

	public function isReadable() {
		return is_readable(sysConfig::getDirFsCatalog() . $this->getPathname());
	}

	public function getPerms() {
		return substr(sprintf('%o', fileperms(sysConfig::getDirFsCatalog() . $this->getPathname())), -4);
	}

	public function exists() {
		if ($this->isFile() === true){
			return (file_exists(sysConfig::getDirFsCatalog() . $this->getPathname()) === true);
		}
		else {
			return (is_dir(sysConfig::getDirFsCatalog() . $this->getPathname()) === true);
		}
	}

	public function updateContentFromString($contents, $truncate = false) {
		$FileObj = new SplFileObject(sysConfig::getDirFsCatalog() . $this->getPathname(), 'w');
		if ($truncate === true){
			$FileObj->ftruncate(0);
		}
		$FileObj->fwrite($contents);
		$FileObj = null;
		unset($FileObj);
	}
}

class SystemFTP
{

	private $usePASV = true;

	private $ftpConn = false;

	private $error = array();

	public function __construct() {
	}

	public function hasError(){
		return !empty($this->error);
	}

	public function getError(){
		return $this->error;
	}

	public function connect() {
		$this->ftpConn = ftp_connect(sysConfig::get('SYSTEM_FTP_SERVER'), 21);
		if ($this->ftpConn === false){
			$this->error = array(
				'type' => 'fatal',
				'message' => sysLanguage::get('TEXT_ERROR_FTP_UNABLE_TO_CONNECT')
			);
		}

		$login = ftp_login(
			$this->ftpConn,
			sysConfig::get('SYSTEM_FTP_USERNAME'),
			sysConfig::get('SYSTEM_FTP_PASSWORD')
		);
		if ($login === false){
			$this->error = array(
				'type' => 'fatal',
				'message' => sysLanguage::get('TEXT_ERROR_INCORRECT_FTP_LOGIN')
			);
		}

		if ($this->usePASV === true){
			ftp_pasv($this->ftpConn, true);
		}

		/*
		 * @TODO: How do i handle users who's home directory is not the one that includes public_html?????
		 */
		ftp_chdir($this->ftpConn, 'public_html');

		/*
		 * Get to the catalog directory for all relative paths
		 */
		if (sysConfig::getDirWsCatalog() != '' && sysConfig::getDirWsCatalog() != '/'){
			$folders = sysConfig::getDirWsCatalog();
			$folders = explode('/', $folders);
			foreach($folders as $fName){
				if (empty($fName)) {
					continue;
				}

				ftp_chdir($this->ftpConn, $fName);
			}
		}
	}

	public function cleanPath($filePath) {
		return str_replace(sysConfig::getDirFsCatalog(), '', $filePath);
	}

	public function disconnect() {
		ftp_close($this->ftpConn);
	}

	public function makeWritable($filePath) {
		$this->checkPath($filePath);

		return $this->changePermissions($filePath, '777');
	}

	public function unmakeWritable($filePath) {
		if (is_dir(sysConfig::getDirFsCatalog() . $filePath)){
			$perms = '755';
		}
		else {
			$perms = '644';
		}
		return $this->changePermissions($filePath, $perms);
	}

	public function deleteDir($filePath) {
		ftp_rmdir($this->ftpConn, $this->cleanPath($filePath));
	}

	public function deleteFile($filePath) {
		ftp_delete($this->ftpConn, $this->cleanPath($filePath));
	}

	public function copyFile($from, $to) {
		$success = true;
		$fileCheck = $this->checkPath($to);

		if ($fileCheck->exists() === true){
			if (!$this->makeWritable($fileCheck->getPathname())){
				$success = false;
			}
		}
		else {
			if ($fileCheck->isWritable() === false){
				if (!$this->makeWritable($fileCheck->getPath())){
					$success = false;
				}
			}

			if ($success === true){
				$tmpHandle = fopen($from, 'r');
				if (!ftp_fput($this->ftpConn, $fileCheck->getPathname(), $tmpHandle, FTP_BINARY)){
					$success = false;
				}
				fclose($tmpHandle);

				if ($success === true && $fileCheck->isWritable() === true){
					$this->unmakeWritable($fileCheck->getPath());
				}
			}
		}
		return $success;
	}

	public function updateFileFromString($filePath, $fileContent) {
		$fileCheck = $this->checkPath($filePath);
		if ($fileCheck->isDir() === true) {
			return;
		}

		if ($fileCheck->exists() === false){
			$tmpHandle = tmpfile();
			ftp_fput($this->ftpConn, $fileCheck->getPathname(), $tmpHandle, FTP_ASCII);
			fclose($tmpHandle);
		}

		if ($fileCheck->isWritable() === false){
			$this->makeWritable($fileCheck->getPathname());
		}

		$fileCheck->updateContentFromString($fileContent, true);

		if ($fileCheck->isWritable() === true){
			$this->unmakeWritable($fileCheck->getPathname());
		}
		$fileCheck = null;
		unset($fileCheck);
	}

	public function checkPath($filePath) {
		$fileCheck = new SystemFtpSplFileInfo($this->cleanPath($filePath));

		$FileLoop = $fileCheck->getPathInfo('SystemFtpSplFileInfo');
		$checkArr = array(
			$fileCheck
		);
		while($FileLoop->getPath() != ''){
			$checkArr[] = $FileLoop;
			$FileLoop = $FileLoop->getPathInfo('SystemFtpSplFileInfo');
		}
		$checkArr[] = $FileLoop;
		$FileLoop = null;
		unset($FileLoop);

		$checkArr = array_reverse($checkArr);
		foreach($checkArr as $fInfo){
			if ($fInfo->isFile() === true || $fInfo->exists() === true) {
				continue;
			}

			ftp_mkdir($this->ftpConn, $fInfo->getPathname());
		}
		$checkArr = null;
		$fInfo = null;
		unset($checkArr);
		unset($fInfo);

		return $fileCheck;
	}

	private function changePermissions($filePath, $perms) {
		$perms = '0' . $perms;
		$ftpCmd = ftp_chmod($this->ftpConn, eval("return({$perms});"), $this->cleanPath($filePath));
		$success = true;
		if ($ftpCmd === false){
			$success = false;
			$this->error = array(
				'type' => 'fatal',
				'message' => sprintf(
					sysLanguage::get('TEXT_ERROR_CANNOT_CHANGE_PERMS'),
					$filePath
				)
			);
		}
		return $success;
	}
}