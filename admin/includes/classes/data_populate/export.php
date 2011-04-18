<?php
class dataExport {
	public function __construct(){
		$this->curHeaderCol = 0;
		$this->colSeparator = "\t";
		$this->maxRecords = 300;
		$this->tempDir = sysConfig::getDirFsCatalog() . 'temp/';
		$this->endOfRow = 'EOREOR' . "\n";
	}
	
	public function setHeader($key){
		$this->fileHeaders[$key] = $this->curHeaderCol++;
		return $this;
	}
	
	public function setQuery($query){
		$this->sqlQuery = $query;
		return $this;
	}
	
	public function setExportData($data){
		$this->exportData = $data;
		return $this;
	}
	
	public function setColSeparator($val){
		$this->colSeparator = $val;
		return $this;
	}
	
	public function setEndOfRow($val){
		$this->endOfRow = $val;
		return $this;
	}
	
	public function setHeaders($headers){
		foreach($headers as $key){
			$this->setHeader($key);
		}
		return $this;
	}
	
	public function process(){
		$this->fileString = '';
		foreach($this->fileHeaders as $key => $value){
			$this->fileString .= $key . $this->colSeparator;
		}
		$this->fileString .= $this->endOfRow;

		if (isset($this->exportData)){
			foreach($this->exportData as $rowData){
				foreach($this->fileHeaders as $key => $value){
					$this->fileString .= $this->readyColumn((isset($rowData[$key]) ? $rowData[$key] : ''));
				}
				$this->fileString .= $this->endOfRow;
			}
		}else{
			$Qproducts = dataAccess::setQuery($this->sqlQuery);
			while($Qproducts->next() !== false){
				foreach($this->fileHeaders as $key => $value){
					$this->fileString .= $this->readyColumn($Qproducts->getVal($key));
				}
				$this->fileString .= $this->endOfRow;
			}
		}
		return $this;
	}
	
	private function readyColumn($colText){
		// kill the carriage returns and tabs in the descriptions, they're killing me!
		$colText = str_replace(array("\r", "\n", "\t"), ' ', $colText);

		// and put the text into the output separated by tabs
		$colText .= $this->colSeparator;
		return $colText;
	}
	
	public function output($direct = false){
		global $request_type;
		$EXPORT_TIME = 'BP' . strftime('%Y%b%d-%H%I');

		/*
		$tmpfname = $this->tempDir . $EXPORT_TIME . '.txt';
		//unlink($tmpfname);
		$fp = fopen($tmpfname, "w+");
		fwrite($fp, $this->fileString);
		fclose($fp);
		*/

		header("Content-type: application/vnd.ms-excel");
		header("Content-disposition: attachment; filename=$EXPORT_TIME.xls");
		// Changed if using SSL, helps prevent program delay/timeout (add to backup.php also)
		//	header("Pragma: no-cache");
		if ($request_type== 'NONSSL'){
			header("Pragma: no-cache");
		} else {
			header("Pragma: ");
		}
		header("Expires: 0");
		if (!isset($this->fileString)){
			$this->process();
		}
		echo $this->fileString;
		itwExit();
	}
}
?>