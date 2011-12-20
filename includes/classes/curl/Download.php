<?php
/**
 * Created by Stephen Walker.
 * Date: 12/11/10
 * Time: 2:50 PM
 */
class CurlDownload{

	/**
	 * @var string Url to use
	 */
	private $url = '';

	/**
	 * @var string Authorization method
	 */
	private $authMethod = 'post';

	/**
	 * @var string User to use for authorization
	 */
	private $user = '';

	/**
	 * @var string Password to use for authorization
	 */
	private $password = '';

	/**
	 * @var string Local folder to store the downloaded file in
	 */
	private $localFolder = 'tmp/';

	/**
	 * @var string Local folder to store the downloaded file in, from ftp
	 */
	private $localFolderFtpPath = '';

	/**
	 * @var string Local file name to store the downloaded file as
	 */
	private $localFileName = '';

	/**
	 * @var bool Use curl progress reports
	 */
	private $useProgressBar = true;

	/**
	 * @var string Progress bar to use for reports
	 */
	private $progressBarName = '';

	/**
	 * @var string Data to send with the curl request
	 */
	private $requestData = array();

	/**
	 * @var int Packet size for the download
	 */
	private $packetSize = 512;

	/**
	 * Class constructor
	 * @param string $server
	 * @param string $user
	 * @param string $password
	 */
	public function __construct($url = '', $user = '', $password = ''){
		if (!empty($url)){
			$this->url = $url;
		}

		if (!empty($user)){
			$this->user = $user;
		}

		if (!empty($password)){
			$this->password = $password;
		}
	}

	/**
	 * Set the url to use
	 * @param string $url
	 * @return void
	 */
	public function setUrl($url){
		$this->url = $url;
	}

	/**
	 * Set the authorization method to use
	 * @param string $method (post, get, htaccess)
	 * @return void
	 */
	public function setAuthMethod($method){
		$this->authMethod = $method;
	}

	/**
	 * Set the user to use for the authorization
	 * @param string $user
	 * @return void
	 */
	public function setUser($user){
		$this->user = $user;
	}

	/**
	 * Set the password to use for the authorization
	 * @param string $password
	 * @return void
	 */
	public function setPassword($password){
		$this->password = $password;
	}

	/**
	 * Set the local folder to store the download in
	 * @param string $folder
	 * @return void
	 */
	public function setLocalFolder($folder){
		$this->localFolder = $folder;
		$this->localFolderFtpPath = str_replace(sysConfig::getDirFsCatalog(), '', $this->localFolder);
	}

	/**
	 * Set the local file name to store the download as
	 * @param string $fileName
	 * @return void
	 */
	public function setLocalFileName($fileName){
		$this->localFileName = $fileName;
	}

	/**
	 * Use the curl progress reports
	 * @param bool $val
	 * @return void
	 */
	public function useProgressBar($val){
		$this->useProgressBar = $val;
	}

	/**
	 * Set the progress bar name to store the reports
	 * @param string $name
	 * @return void
	 */
	public function setProgressBarName($name){
		$this->progressBarName = $name;
	}

	/**
	 * Set the data to send with the request
	 * @param array $data
	 * @return void
	 */
	public function setRequestData(array $data){
		$this->requestData = $data;
	}

	/**
	 * Download the file
	 * @return void
	 */
	public function download(){
		$error = false;
		$errorMsg = '';

		$Ftp = new SystemFTP();
		$Ftp->connect();
		$Ftp->makeWritable($this->localFolderFtpPath);

		$File = fopen($this->localFolder . $this->localFileName, 'w+');
		ftruncate($File, 0);
		$Url = $this->url;
		if ($this->authMethod == 'htaccess'){
			$Url = $this->user . ':' . $this->password . '@' . $Url;
		}

		$RequestObj = new CurlRequest($Url);
		$RequestObj->setOption(CURLOPT_BINARYTRANSFER, true);
		$RequestObj->setOption(CURLOPT_FILE, $File);
		$RequestObj->setOption(CURLOPT_BUFFERSIZE, $this->packetSize);
		$data = $this->requestData;
		if ($this->authMethod != 'htaccess'){
			if ($this->authMethod == 'post'){
				$RequestObj->setOption(CURLOPT_POST, true);
			}
			$data['username'] = $this->user;
			$data['password'] = $this->password;
		}

		$dataStr = '';
		foreach($data as $k => $v){
			$dataStr .= $k . '=' . $v . '&';
		}

		$RequestObj->setData(substr($dataStr, 0, -1));
		if ($this->useProgressBar === true){
			$RequestObj->setOption(CURLOPT_NOPROGRESS, false);
			$RequestObj->setOption(CURLOPT_PROGRESSFUNCTION, array(&$this, 'report'));
		}

		$ResponseObj = $RequestObj->execute();

		if (is_writable($this->localFolder)){
			$Ftp->unmakeWritable($this->localFolderFtpPath);
		}

		if ($ResponseObj->hasError() || $error === true){
			echo '<pre>';
			echo $errorMsg;
			print_r($ResponseObj->getInfo());
			die($ResponseObj->getError());
		}
		else{
			fclose($File);
			unset($File);
		}
		$Ftp->disconnect();
	}

	/**
	 * Report function for curl progress reports
	 * @param int $download_size
	 * @param int $downloaded
	 * @param int $upload_size
	 * @param int $uploaded
	 * @return int
	 */
	public function report($download_size, $downloaded, $upload_size, $uploaded){
		if ($downloaded > 0 && $download_size > 0){
			$percent = ($downloaded / $download_size);
		}
		else{
			$percent = 0;
		}

		$downloadedDone = ($downloaded > 0 ? number_format(($downloaded / 1024) / 1024, 2) : 0);
		$downloadedLeft = ($download_size > 0 ? number_format(($download_size / 1024) / 1024, 2) : 0);
		if ($downloadedLeft == 0){
			$message = 'Connecting To Download Site';
		}
		else{
			$message = 'Downloading ' . $this->localFileName . ' To ' . $this->localFolder .
			'<br>Download Speed: N/A' .
			'<br>Total Downloaded: ' . $downloadedDone . '/' . $downloadedLeft . ' MB';
		}
		Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->exec('update progress_bar set message = "' . $message . '", percentage = "' . ($percent * 100) . '" where name = "' . $this->progressBarName . '"');
		return 0;
	}
}
