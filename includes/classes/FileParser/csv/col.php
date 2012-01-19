<?php
class FileParserCsvCol
{

	private $_key;

	private $text;

	public function __construct($colText, $key) {
		$this->_key = $key;
		$this->text = $colText;
	}

	function getText() {
		return $this->text;
	}

	public function key(){
		return $this->_key;
	}
}