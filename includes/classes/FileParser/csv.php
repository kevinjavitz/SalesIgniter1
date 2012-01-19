<?php
require(SysConfig::getDirFsCatalog() . 'includes/classes/FileParser/csv/row.php');
require(SysConfig::getDirFsCatalog() . 'includes/classes/FileParser/csv/col.php');

class FileParserCsv extends SplFileObject
{

	private $colAssociations = array();

	public function __construct($filename, $open_mode = "r", $use_include_path = false, $context = false) {
		if ($context === false){
			parent::__construct($filename, $open_mode, $use_include_path);
		}
		else {
			parent::__construct($filename, $open_mode, $use_include_path, $context);
		}
		$this->setCsvControl(',');
		$this->setFlags(SplFileObject::READ_CSV);
		$this->fwrite(pack("CCC",0xef,0xbb,0xbf)); /*patch for utf-8 files*/
	}

	public function parseHeaderLine(){
		$this->rewind();
		$curRow = $this->currentRow();
		while($curRow->valid()){
			$curCol = $curRow->current();

			$colText = $curCol->getText();
			if (!empty($colText)){
				$this->associateColumn($curCol->key(), $colText);
			}
			$curRow->next();
		}
		$this->next();
	}

	public function currentRow() {
		return new FileParserCsvRow($this->current(), $this->colAssociations);
	}

	public function associateColumn($key, $association) {
		$this->colAssociations[$key] = $association;
	}

	public function associateColumns($associations) {
		$this->colAssociations = $associations;
	}

	public function addRow($rInfo){
		$cInfo = $this->getCsvControl();

		$lineArr = array();
		foreach($rInfo as $v){
			$lineArr[] = '' . $cInfo[1] . '' . $v . '' . $cInfo[1] . '';
		}

		$this->fwrite(implode($cInfo[0], $lineArr) . "\n");
	}
}