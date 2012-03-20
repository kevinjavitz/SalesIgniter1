<?php
require(SysConfig::getDirFsCatalog() . 'includes/classes/FileParser/csv/row.php');
require(SysConfig::getDirFsCatalog() . 'includes/classes/FileParser/csv/col.php');

class FileParserCsv implements Iterator
{

	private $colAssociations = array();
	
	private $fileObj;
	
	public function __construct($filename, $open_mode = "r", $use_include_path = false, $context = false) {
		if ($filename == 'temp'){
			$this->fileObj = new SplTempFileObject();
		}
		elseif ($context === false){
			$this->fileObj = new SplFileObject($filename, $open_mode, $use_include_path);
		}
		else {
			$this->fileObj = new SplFileObject($filename, $open_mode, $use_include_path, $context);
		}
		$this->fileObj->setCsvControl(',');
		$this->fileObj->setFlags(SplFileObject::READ_CSV);
		//$this->fwrite(pack("CCC",0xef,0xbb,0xbf)); /*patch for utf-8 files*/
	}

	public function setCsvControl($val){
		$this->fileObj->setCsvControl($val);
	}

	public function parseHeaderLine(){
		$this->fileObj->rewind();
		$curRow = $this->currentRow();
		while($curRow->valid()){
			$curCol = $curRow->current();

			$colText = $curCol->getText();
			if (!empty($colText)){
				$this->associateColumn($curCol->key(), $colText);
			}
			$curRow->next();
		}
		$this->fileObj->next();
	}

	public function currentRow() {
		return new FileParserCsvRow($this->fileObj->current(), $this->colAssociations);
	}

	public function associateColumn($key, $association) {
		$this->colAssociations[$key] = $association;
	}

	public function associateColumns($associations) {
		$this->colAssociations = $associations;
	}

	public function addRow($rInfo){
		$cInfo = $this->fileObj->getCsvControl();

		$lineArr = array();
		foreach($rInfo as $v){
			$lineArr[] = '' . $cInfo[1] . '' . $v . '' . $cInfo[1] . '';
		}

		$this->fileObj->fwrite(implode($cInfo[0], $lineArr) . "\n");
	}
	
	public function rewind() {
		$this->fileObj->rewind();
	}

	public function current() {
		return new FileParserCsvCol($this->fileObj->current(), $this->getAssociation($this->fileObj->key()));
	}

	public function key() {
		return $this->fileObj->key();
	}

	public function next() {
		$this->fileObj->next();
	}

	public function valid() {
		return $this->fileObj->valid();
	}
	
	public function output(){
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"labelSpreadsheet.csv\";" );
		header("Content-Transfer-Encoding: binary");
		
		$this->fileObj->rewind();
		$cInfo = $this->fileObj->getCsvControl();
		foreach($this->fileObj as $line){
			foreach($line as $k => $v){
				echo '' . $cInfo[1] . '' . $v . '' . $cInfo[1] . '';
				if (isset($line[$k+1])){
					echo $cInfo[0];
				}
			}
			echo "\n";
		}
		itwExit();
	}
}