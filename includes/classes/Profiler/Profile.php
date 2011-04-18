<?php
	class SES_Profile {
		private $startedMicrotime;
		private $endedMicrotime;
		private $totalTime;
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
			$this->ended = false;
		}

		public function end(){
			$this->endedMicrotime = microtime(true);
			$this->ended = true;

			$this->totalTime += ($this->endedMicrotime - $this->startedMicrotime);
		}

		public function getElapsedSecs(){
			return $this->totalTime;
		}
	}
?>