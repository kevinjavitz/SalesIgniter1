<?php
/**
 * Holder for all registered events
 * @package EventManager
 */
class EventManager {
	/**
	 * Array holding all events
	 * @var array
	 */
	private static $Observers = array();

	/**
	 * Generic event class for event methods that are not in classes
	 * @var string
	 */
	protected static $genericClassName = 'generic';

	/**
	 * Get all events for profiling
	 * @return array
	 */
	public function getProfileEvents(){
		$return = array();
		foreach(self::$Observers as $className => $events){
			foreach($events as $EventsName => $EventArr){
				foreach($EventArr as $idx => $Event){
					$return[] = $Event;
				}
			}
		}
		return $return;
	}

	/**
	 * Attach multiple events using an array
	 * @static
	 * @param array $events array containing event information
	 * @param string|null $toClassName Class where the events can only be called from
	 * @param object|null $fromClass Class where the event methods reside
	 * @return void
	 */
	public static function attachEvents($events, $toClassName = null, &$fromClass = null){
		foreach($events as $eventInfo){
			if (is_array($eventInfo)){
				$Event = new EventTest($eventInfo['name'], $eventInfo['function'], $eventInfo['class']);
			}
			else{
				$Event = new EventTest($eventInfo, $eventInfo, $fromClass);
			}
			self::attach($Event, $toClassName);
		}
	}

	/**
	 * Attach a single event
	 * @static
	 * @param array|string $eventInfo If string then event name, if array then event information
	 * @param string|null $toClassName Class where the event can only be called from
	 * @param object|null $fromClass Class where the event method resides
	 * @return void
	 */
	public static function attachEvent($eventInfo, $toClassName = null, &$fromClass = null){
		if (is_array($eventInfo)){
			$Event = new EventTest($eventInfo['name'], $eventInfo['function'], $eventInfo['class']);
		}
		else{
			$Event = new EventTest($eventInfo, $eventInfo, $fromClass);
		}
		self::attach($Event, $toClassName);
	}

	/**
	 * Add the event to the observers array
	 * @static
	 * @param EventTest|EventActionResponse $observer Event to attach
	 * @param object|null $toClassName Class where the event is called from
	 * @return void
	 */
	public static function attach($observer, $toClassName = null){
		$eventName = $observer->getEventName();
		if (is_null($toClassName) === true){
			$toClassName = self::$genericClassName;
		}
		if (array_key_exists($toClassName, self::$Observers) === false){
			self::$Observers[$toClassName] = array();
		}
		if (array_key_exists($eventName, self::$Observers[$toClassName]) === false){
			self::$Observers[$toClassName][$eventName] = array();
		}
		$addResource = true;
		foreach(self::$Observers[$toClassName][$eventName] as $regResource){
			if ($regResource == $observer){
				$addResource = false;
				break;
			}
		}
		if ($addResource === true){
			self::$Observers[$toClassName][$eventName][] = &$observer;
		}
	}

	/**
	 * Remove the event from the observers array
	 * @static
	 * @param EventTest $observer Event to remove
	 * @param object|null $fromClassName Class event was attached to
	 * @return void
	 */
	public static function detach(EventTest $observer, $fromClassName = null){
		$eventName = $observer->getEventName();
		if (is_null($fromClassName) === true){
			$fromClassName = self::$genericClassName;
		}
		foreach(self::$Observers[$fromClassName][$eventName] as $idx => $Event){
			if ($observer == $Event){
				unset(self::$Observers[$fromClassName][$eventName][$idx]);
				break;
			}
		}
	}

	/**
	 * Run the events and return what the events return in an array
	 * @static
	 * @return array
	 */
	public static function notifyWithReturn(){
		$stack = debug_backtrace();
		$args = array();
		if (isset($stack[0]['args'])){
			for($i = 0; $i < count($stack[0]['args']); $i++){
				$args[$i] =& $stack[0]['args'][$i];
			}
		}
		$eventName = $args[0];
		array_shift($args);
		$returnArr = array();
		if (substr($eventName, 0, 1) == '*'){
			$parsed = explode('\\', $eventName);
			$eventName = $parsed[1];
			foreach(self::$Observers as $className => $events){
				foreach($events as $EventsName => $EventArr){
					if ($EventsName == $eventName){
						foreach($EventArr as $idx => $Event){
							if ($Event->getMethodName() == $eventName){
								$returnArr[] = call_user_func_array(array($Event, 'update'), $args);
							}
						}
					}
				}
			}
		}
		else{
			$ObserverArray = array();
			if (stristr($eventName, '\\')){
				$parsed = explode('\\', $eventName);
				$className = $parsed[0];
				$eventName = $parsed[1];
				if (array_key_exists($className, self::$Observers)){
					$ObserverArray = self::$Observers[$className];
				}
			}
			else{
				if (array_key_exists(self::$genericClassName, self::$Observers)){
					$ObserverArray = self::$Observers[self::$genericClassName];
				}
			}
			if (array_key_exists($eventName, $ObserverArray)){
				foreach($ObserverArray[$eventName] as $idx => $Event){
					$Event->start();
					$returnArr[] = call_user_func_array(array($Event, 'update'), $args);
					$Event->end();
				}
			}
		}
		return $returnArr;
	}

	/**
	 * Run the events without returning anything
	 * @static
	 * @return void
	 */
	public static function notify(){
		$stack = debug_backtrace();
		$args = array();
		if (isset($stack[0]['args'])){
			for($i = 0; $i < count($stack[0]['args']); $i++){
				$args[$i] =& $stack[0]['args'][$i];
			}
		}
		$eventName = $args[0];
		array_shift($args);
		if (substr($eventName, 0, 1) == '*'){
			$parsed = explode('\\', $eventName);
			$eventName = $parsed[1];
			foreach(self::$Observers as $className => $events){
				foreach($events as $EventsName => $EventArr){
					if ($EventsName == $eventName){
						foreach($EventArr as $idx => $Event){
							if ($Event->getMethodName() == $eventName){
								$Event->start();
								call_user_func_array(array($Event, 'update'), $args);
								$Event->end();
							}
						}
					}
				}
			}
		}
		else{
			if (stristr($eventName, '\\')){
				$parsed = explode('\\', $eventName);
				$className = $parsed[0];
				$eventName = $parsed[1];
				if (array_key_exists($className, self::$Observers)){
					$ObserverArray = self::$Observers[$className];
				}
				else{
					$ObserverArray = null;
				}
			}
			else{
				if (array_key_exists(self::$genericClassName, self::$Observers)){
					$ObserverArray = self::$Observers[self::$genericClassName];
				}else{
					$ObserverArray = null;
				}
			}
			if (is_array($ObserverArray)){
				if (array_key_exists($eventName, $ObserverArray)){
					foreach($ObserverArray[$eventName] as $idx => $Event){
						$Event->start();
						call_user_func_array(array($Event, 'update'), $args);
						$Event->end();
					}
				}
			}
		}
	}

	/**
	 * Attach an action response event
	 * @static
	 * @param array|string $data Event data
	 * @param string $type Event response type ( redirect, exit, json, html )
	 * @return void
	 */
	public static function attachActionResponse($data, $type){
		$Event = new EventActionResponse($data, $type);
		$Event->setEventName('ApplicationActionsAfterExecute');
		self::attach($Event);
	}
}
?>