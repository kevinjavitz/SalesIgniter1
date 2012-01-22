<?php
/**
 * Event class to hold information specific to the event
 * @package EventManager
 */
class EventClosure
{

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
	 * @var null The start memory usage for the event
	 */
	private $startedMemory;

	/**
	 * @var null The end memory usage for the event
	 */
	private $endedMemory;

	/**
	 * @var int The calculated memory usage of the event
	 */
	private $memoryUsage = 0;

	/**
	 * @var string Event name for the event
	 */
	private $eventName;

	/**
	 * @var Closure Closure function for event
	 */
	private $closure;

	/**
	 * Creates an event
	 * @param string $eventName The name for the event
	 * @param Closure $closure Closure function for event
	 */
	public function __construct($eventName, Closure $closure) {
		$this->eventName = $eventName;
		$this->closure = $closure;
		$this->startedMicrotime = null;
		$this->endedMicrotime = null;
		$this->totalTime = 0;
		$this->startedMemory = null;
		$this->endedMemory = null;
		$this->memoryUsage = 0;
	}

	/**
	 * Start the timer for the event
	 * @return void
	 */
	public function start() {
		$this->startedMicrotime = microtime(true);
		$this->startedMemory = memory_get_usage();
	}

	/**
	 * Stop the timer for the event
	 * @return void
	 */
	public function end() {
		$this->endedMicrotime = microtime(true);
		$this->totalTime += ($this->endedMicrotime - $this->startedMicrotime);

		$this->endedMemory = memory_get_usage();
		$this->memoryUsage = ($this->endedMemory - $this->startedMemory);
	}

	/**
	 * Get the total time for the event
	 * @return int
	 */
	public function getElapsedSecs() {
		return $this->totalTime;
	}

	/**
	 * Get the total memory usage for the event
	 * @return int
	 */
	public function getMemoryUsage() {
		return $this->memoryUsage;
	}

	/**
	 * Get the event name
	 * @return string
	 */
	public function getEventName() {
		return $this->eventName;
	}

	/**
	 * Get the method name
	 * @return string
	 */
	public function getClosure() {
		return $this->closure;
	}

	/**
	 * Run the event
	 * @return mixed
	 */
	public function update() {
		$stack = debug_backtrace();
		$args = array();
		if (isset($stack[0]['args'])){
			for($i = 0; $i < count($stack[0]['args']); $i++){
				$args[$i] =& $stack[0]['args'][$i];
			}
		}
		return call_user_func_array($this->closure, $args);
	}
}

?>