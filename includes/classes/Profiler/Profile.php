<?php
	class SES_Profile {
		private $startedMicrotime;
		private $endedMicrotime;
		private $totalTime;
		private $startedMemory;
		private $endedMemory;
		private $memoryUsage;
		private $ended;

		public function __construct($name){
			$this->name = $name;
			$this->startedMicrotime = 0;
			$this->endedMicrotime = 0;
			$this->totalTime = 0;
			$this->ended = true;
		}

		public function getName(){
			return $this->name;
		}

		public function start(){
			if ($this->ended === false){
				$this->end();
			}
			$this->startedMicrotime = microtime(true);
			$this->startedMemory = memory_get_usage();
			$this->ended = false;
		}

		public function end(){
			$this->endedMicrotime = microtime(true);
			$this->endedMemory = memory_get_usage();
			$this->ended = true;

			$this->totalTime += ($this->endedMicrotime - $this->startedMicrotime);
			$this->memoryUsage = ($this->endedMemory - $this->startedMemory);
		}

		public function getElapsedSecs(){
			return $this->totalTime;
		}
		
		public function getMemoryUsage(){
			return $this->memoryUsage;
		}
	}
?>