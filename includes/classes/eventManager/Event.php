<?php
/**
 * Event class to hold information specific to the event
 * @package EventManager
 */
class EventTest{
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
	 * @var string Event name for the event
	 */
	private $eventName;

	/**
	 * @var string Method name for the event
	 */
	private $methodName;

	/**
	 * @var null|object Class where the method resides
	 */
	private $fromClass = null;

	/**
	 * Creates an event
	 * @param string $eventName The name for the event
	 * @param string $methodName The method name to use for the event
	 * @param null|object $fromClass The class where the method resides
	 */
	public function __construct($eventName, $methodName, &$fromClass = null){
		$this->eventName = $eventName;
		$this->methodName = $methodName;
		$this->fromClass = $fromClass;
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
	 * Get the event name
	 * @return string
	 */
	public function getEventName(){
		return $this->eventName;
	}

	/**
	 * Get the method name
	 * @return string
	 */
	public function getMethodName(){
		return $this->methodName;
	}

	/**
	 * Get the class where the method resides
	 * @return null|object
	 */
	public function getFromClass(){
		return $this->fromClass;
	}

	/**
	 * Run the event
	 * @return mixed
	 */
	public function update(){
		$stack = debug_backtrace();
		$args = array();
		if (isset($stack[0]['args'])){
			for($i = 0; $i < count($stack[0]['args']); $i++){
				$args[$i] =& $stack[0]['args'][$i];
			}
		}
		return call_user_func_array(array($this->fromClass, $this->methodName), $args);
	}
}
?>