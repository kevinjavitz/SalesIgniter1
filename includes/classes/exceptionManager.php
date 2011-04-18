<?php
define('E_USER_FATAL', 17);
require(dirname(__FILE__) . '/exceptionManager/AbstractParser.php');
/**
 * Exception Manager for exception reports
 * @package ExceptionManager
 */
class ExceptionManager {
	/**
	 * Messages to be shown on the next output of messages
	 * @var null
	 */
	private $sessionMessages = null;

	/**
	 * Allow exceptions to be reported
	 * @var bool
	 */
	private $acceptReports = false;

	/**
	 * Display formatted reports
	 * @var bool
	 */
	private $displayErrors = false;

	/**
	 * Array of messages indexed by their type
	 * @var array
	 */
	private $messages = array('Fatal' => array(), 'Error' => array(), 'Warning' => array(), 'Notice' => array());

	/**
	 * Array of icon css classes indexed by exception type
	 * @var array
	 */
	private $typeIcons = array('Fatal' => 'ui-icon-circle-check', 'Error' => 'ui-icon-circle-close', 'Warning' => 'ui-icon-alert', 'Notice' => 'ui-icon-alert');

	/**
	 * Array of error codes indexed by exception types
	 * @var array
	 */
	private $errorCodes = array('Fatal' => array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_FATAL), 'Error' => array(E_USER_ERROR, E_RECOVERABLE_ERROR), 'Warning' => array(E_WARNING, E_CORE_WARNING, E_USER_WARNING, E_DEPRECATED, E_USER_DEPRECATED), 'Notice' => array(E_NOTICE, E_COMPILE_WARNING, E_USER_NOTICE, E_STRICT));

	/**
	 * Array of exception types indexes by their error code
	 * @var array
	 */
	private $errorCodesDescription = array(E_ERROR => 'Error', E_USER_FATAL => 'User Fatal Error', E_PARSE => 'Parse Error', E_CORE_ERROR => 'Core Error', E_COMPILE_ERROR => 'Compile Error', E_USER_ERROR => 'User Error', E_RECOVERABLE_ERROR => 'Recoverable Error', E_WARNING => 'Warning', E_CORE_WARNING => 'Core Warning', E_USER_WARNING => 'User Warning', E_DEPRECATED => 'This function has been deprecated, please see php.net for the recommended alternative function.', E_USER_DEPRECATED => 'This function has been deprecated, please see update it to the new function.', E_NOTICE => 'Notice', E_COMPILE_WARNING => 'Compile Warning', E_USER_NOTICE => 'User Notice', E_STRICT => 'Strict');

	/**
	 * Create the exception manager
	 */
	public function __construct(){
		if (isset($_GET['showErrors'])){
			$this->acceptReports = true;
			$this->displayErrors = true;
		}
		elseif (sysConfig::get('ERROR_REPORTING_METHOD') == 'Display'){
			$this->acceptReports = true;
			$this->displayErrors = true;
		}
		elseif (sysConfig::get('ERROR_REPORTING_METHOD') == 'Log'){
			$this->acceptReports = true;
		}
		elseif (sysConfig::get('ERROR_REPORTING_METHOD') == 'Email'){
			$this->acceptReports = true;
		}
	}

	/**
	 * Load messages that were added to the session
	 * @return void
	 */
	public function initSessionMessages(){
		if ($this->reportsAllowed() === false) return;
		if (Session::exists('ExceptionManagerMessages')){
			$this->sessionMessages = Session::get('ExceptionManagerMessages');
			Session::remove('ExceptionManagerMessages');
		}
	}

	/**
	 * Check if reports are being accepted
	 * @return bool
	 */
	public function reportsAllowed(){
		return $this->acceptReports;
	}

	/**
	 * Report an error
	 * @param int $errno Error number
	 * @param string $errstr
	 * @param string $errfile File where the error occured
	 * @param int $errline Line where the error occured
	 * @param string $errcontext Description of the error
	 * @return
	 */
	public function addError($errno, $errstr, $errfile, $errline, $errcontext){
		if ($this->reportsAllowed() === false) return;
		$this->add(new ErrorException($errstr, 0, $errno, $errfile, $errline));
	}

	/**
	 * Add exception to the output array
	 * @param ExceptionWarning|ExceptionNotice|ExceptionFatal|ExceptionError $Exception
	 * @return void
	 */
	public function add($Exception){
		if ($this->reportsAllowed() === false) return;
		foreach($this->errorCodes as $type => $codes){
			if (sysConfig::inSet($type, 'ERROR_REPORTING_LEVEL')){
				if ($Exception instanceof ErrorException){
					if (in_array($Exception->getSeverity(), $codes)){
						if ($this->requireClass($type)){
							$className = 'Exception' . $type;
							$Description = $Exception->getSeverity();
							if (array_key_exists($Description, $this->errorCodesDescription)){
								$Description = $this->errorCodesDescription[$Description];
							}
							$Decorator = new $className($Exception);
							$Decorator->setIconClass($this->typeIcons[$type]);
							$Decorator->setErrorDescription($Description);
							if ($type == 'Fatal'){
								die($Decorator->output());
							}
							else{
								$this->messages[$type][] = $Decorator->output();
							}
						}
						break;
					}
				}
				elseif ($Exception instanceof Doctrine_Exception){
					if ($type == 'Error' && $this->requireClass($type)){
						$className = 'Exception' . $type;
						$Description = '0';
						if (array_key_exists($Description, $this->errorCodesDescription)){
							$Description = $this->errorCodesDescription[$Description];
						}
						$Decorator = new $className($Exception);
						$Decorator->hideTrace(false);
						$Decorator->setIconClass($this->typeIcons[$type]);
						$Decorator->setErrorDescription($Description);
						echo $Decorator->output();
						itwExit();
					}
				}
				elseif ($Exception instanceof Exception){
					if ($this->requireClass($type)){
						$className = 'Exception' . $type;
						$Description = '0';
						if (array_key_exists($Description, $this->errorCodesDescription)){
							$Description = $this->errorCodesDescription[$Description];
						}
						$Decorator = new $className($Exception);
						$Decorator->setIconClass($this->typeIcons[$type]);
						$Decorator->setErrorDescription($Description);
						if ($type == 'Fatal'){
							die($Decorator->output());
						}
						else{
							$this->messages[$type][] = $Decorator->output();
						}
					}
					break;
				}
				else{
					die(print_r($Exception));
				}
			}
		}
	}

	/**
	 * Report an exception
	 * @static
	 * @param string $errMsg Error message
	 * @param int $errLevel Error code
	 * @param array|null $addedInfo Additional information about the error
	 * @return
	 */
	public static function report($errMsg, $errLevel, $addedInfo = null){
		global $ExceptionManager;
		if ($ExceptionManager->reportsAllowed() === false) return;
		$Exception = new ErrorException($errMsg, 0, $errLevel, 'N/A', 0);
		if (is_null($addedInfo) === false){
			$Exception->addedInfo = $addedInfo;
		}
		$ExceptionManager->add($Exception);
	}

	/**
	 * Load the error types class
	 * @param string $type Error type
	 * @return bool
	 */
	private function requireClass($type){
		$className = 'Exception' . ucfirst($type);
		if (!class_exists($className)){
			$dir = sysConfig::getDirFsCatalog() . 'includes/classes/exceptionManager/';
			if (file_exists($dir . ucfirst($type) . '.php')){
				require($dir . ucfirst($type) . '.php');
				return true;
			}
			else{
				return false;
			}
		}
		return true;
	}

	/**
	 * Check if there's any messages to output
	 * @return int
	 */
	public function size(){
		$total = 0;
		if (is_null($this->sessionMessages) === false){
			foreach($this->sessionMessages as $type => $messages){
				$total += sizeof($messages);
			}
		}
		foreach($this->messages as $type => $messages){
			$total += sizeof($messages);
		}
		return $total;
	}

	/**
	 * Output the formatted exception report
	 * @param string $format
	 * @return void
	 */
	public function output($format = 'html'){
		if ($this->displayErrors === true){
			if (is_null($this->sessionMessages) === false){
				foreach($this->sessionMessages as $type => $messageArr){
					foreach($messageArr as $i => $Exception){
						if ($format == 'html' && (!isset($_GET['rType']) || $_GET['rType'] != 'ajax')){
							echo $Exception;
						}
						else{
							echo strip_tags(str_replace('</tr>', "\n", $Exception));
						}
					}
				}
				$this->sessionMessages = null;
			}
			foreach($this->messages as $type => $messageArr){
				foreach($messageArr as $i => $Exception){
					if ($format == 'html' && (!isset($_GET['rType']) || $_GET['rType'] != 'ajax')){
						echo $Exception;
					}
					else{
						echo strip_tags(str_replace('</tr>', "\n", $Exception));
					}
					unset($this->messages[$type][$i]);
				}
			}
		}
	}

	/**
	 * Push all non-reported exceptions to the session array
	 */
	public function __destruct(){
		if ($this->reportsAllowed() === false) return;
		$addedToSession = false;
		foreach($this->messages as $type => $messageArr){
			if (!empty($this->messages[$type])){
				Session::set('ExceptionManagerMessages', $this->messages);
				$addedToSession = true;
				break;
			}
		}
		if ($addedToSession === false && Session::exists('ExceptionManagerMessages') === true){
			Session::remove('ExceptionManagerMessages');
		}
	}
}
?>