<?php
/**
 * Event class to hold information specific to the event, this event is used for action responses in action files
 * @package EventManager
 */
class EventActionResponse{
	/**
	 * @var null The start time for the event
	 */
	private $startedMicrotime;
	/**
	 * @var null The end time for the event
	 */
	private $endedMicrotime;
	/**
	 * @var int The calculated time of the event
	 */
	private $totalTime = 0;
	/**
	 * Holds the event data
	 * @var string|array
	 */
	private $eventData;
	/**
	 * Type of event response
	 * @var string ( redirect, exit, json, html )
	 */
	private $responseType;

	/**
	 * Creates an event action response
	 * @param string|array $data Event data
	 * @param string $responseType Type of event response
	 */
	public function __construct($data, $responseType){
		$this->eventData = $data;
		$this->responseType = $responseType;
		$this->startedMicrotime = null;
		$this->endedMicrotime = null;
		$this->totalTime = 0;
	}

	/**
	 * Start the timer for the event
	 * @return void
	 */
	public function start(){
		$this->startedMicrotime = microtime(true);
	}

	/**
	 * Stop the timer for the event
	 * @return void
	 */
	public function end(){
		$this->endedMicrotime = microtime(true);
		$this->totalTime += ($this->endedMicrotime - $this->startedMicrotime);
	}

	/**
	 * Get the total time for the event
	 * @return int
	 */
	public function getElapsedSecs(){
		return $this->totalTime;
	}

	/**
	 * Set the event name
	 * @return void
	 */
	public function setEventName($val){
		$this->eventName = $val;
	}

	/**
	 * Get the event name
	 * @return string
	 */
	public function getEventName(){
		return $this->eventName;
	}

	/**
	 * Run the event
	 * @return void
	 */
	public function update(){
		$stack = debug_backtrace();
		$args = array();
		if (isset($stack[0]['args'])){
			for($i = 0; $i < count($stack[0]['args']); $i++){
				$args[$i] =& $stack[0]['args'][$i];
			}
		}
		if ($this->responseType == 'redirect'){
			tep_redirect($this->eventData);
		}
		elseif ($this->responseType == 'exit'){
			itwExit();
		}
		elseif ($this->responseType == 'json' || $this->responseType == 'html'){
			/*
			 * @TODO: Determine if this is really the best way
			 */
			if ($this->responseType == 'json'){
				header('Content-Type: text/json');
				if (is_array($this->eventData)){
					$jSON = array();
					foreach($this->eventData as $k => $v){
						if (is_array($v)){
						}
						elseif (substr($v, 0, 1) == '[' || substr($v, 0, 1) == '{'){
							$jSON[] = '"' . $k . '" : ' . str_replace("\'", "'", $v);
						}
						elseif ($v === true || $v === false){
							$jSON[] = '"' . $k . '": ' . ($v === true ? 'true' : 'false');
						}
						else{
							$jSON[] = '"' . $k . '": "' . str_replace("\'", "'", addslashes($v)) . '"';
						}
					}
					//echo '{' . implode(',', $jSON) . '}';
					echo json_encode($this->eventData);
				}
				else{
					echo $this->eventData;
				}
			}
			else{
				echo $this->eventData;
			}
			itwExit();
		}
	}
}
?>