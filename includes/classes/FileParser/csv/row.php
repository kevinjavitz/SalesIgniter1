<?php
class FileParserCsvRow implements Iterator
{

	private $position = 0;

	private $line = array();

	private $colAssociations = array();

	public function __construct($lineArr, $colAssociations = array()) {
		$this->line = $lineArr;
		$this->position = 0;
		$this->colAssociations = $colAssociations;
	}

	private function getAssociation($position){
		$return = $position;
		if (isset($this->colAssociations[$position])){
			$return = $this->colAssociations[$position];
		}
		return $return;
	}

	function rewind() {
		$this->position = 0;
	}

	function current() {
		return new FileParserCsvCol($this->line[$this->position], $this->getAssociation($this->position));
	}

	function key() {
		return $this->position;
	}

	function next() {
		++$this->position;
	}

	function valid() {
		return isset($this->line[$this->position]);
	}
}