<?php
/**
 * Exception class for errors
 * @package ExceptionManager
 */
class ExceptionError extends ExceptionParser {
	/**
	 * Icon css class to use for the exception report
	 * @var string
	 */
	private $iconClass = '';

	public function __construct($Exception){
		$this->e = $Exception;
	}

	public function setIconClass($class){
		$this->iconClass = $class;
	}

	public function setErrorDescription($val){
		$this->errDesc = $val;
	}

	public function addInfo($val){
		$this->addedInfo = $val;
	}

	public function output(){
		$ReportContainer = htmlBase::newElement('div')->addClass('errorReport ui-widget');
		$TableContainer = htmlBase::newElement('div')->addClass('ui-state-error ui-corner-all');
		$Icon = htmlBase::newElement('span')->addClass('ui-icon ' . $this->iconClass);
		$TableContainer->append($Icon);
		$ErrorTable = htmlBase::newElement('table')->setCellPadding(2)->setCellSpacing(0);
		$ErrorTable->addBodyRow(array('columns' => array(array('addCls' => 'main', 'text' => '<b>PHP Error Description:</b>'), array('addCls' => 'main', 'text' => $this->errDesc))));
		$ErrorTable->addBodyRow(array('columns' => array(array('addCls' => 'main', 'text' => '<b>Server Message:</b>'), array('addCls' => 'main', 'text' => $this->e->getMessage()))));
		if (isset($this->e->addedInfo)){
			$this->parseAddedInfo(&$ErrorTable);
		}
		$ErrorTable->addBodyRow(array('columns' => array(array('addCls' => 'main', 'text' => '<b>Time Reported:</b>'), array('addCls' => 'main', 'text' => date('m-d-Y H:i:s')))));
		/*
			$phpTrace = $this->e->getTrace();
			
			$ErrorTable->addBodyRow(array(
				'columns' => array(
					array(
						'addCls' => 'main',
						'text' => '<b>File Involved:</b>'
					),
					array(
						'addCls' => 'main',
						'text' => $phpTrace[0]['file']
					)
				)
			));
			
			$ErrorTable->addBodyRow(array(
				'columns' => array(
					array(
						'addCls' => 'main',
						'text' => '<b>On Line:</b>'
					),
					array(
						'addCls' => 'main',
						'text' => $phpTrace[0]['line']
					)
				)
			));
*/
		$ErrorTable->addBodyRow(array('columns' => array(array('addCls' => 'main', 'valign' => 'top', 'text' => '<b>PHP Trace:</b>'), array('addCls' => 'main', 'text' => '<div><a href="#" class="phpTraceView">View Trace</a></div>' . $this->parseTrace($this->e->getTrace())))));
		$TableContainer->append($ErrorTable);
		$ReportContainer->append($TableContainer);

		if (isset($_GET['rType']) && $_GET['rType'] == 'ajax'){
			return strip_tags(str_replace('</tr>', "\n", $ReportContainer->draw()));
		}else{
			return $ReportContainer->draw();
		}
	}
}

?>