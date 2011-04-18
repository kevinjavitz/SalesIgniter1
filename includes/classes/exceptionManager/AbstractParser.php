<?php
/**
 * Parser for the error output
 * @package ExceptionManager
 */
abstract class ExceptionParser {
	/**
	 * Hide the viewTrace link
	 * @var bool
	 */
	private $hideTrace = true;

	/**
	 * Set the icon class to use for the icon in the message
	 * @abstract
	 * @param string $class
	 * @return void
	 */
	abstract function setIconClass($class);

	/**
	 * Set the content for the error
	 * @abstract
	 * @param string $val
	 * @return void
	 */
	abstract function setErrorDescription($val);

	/**
	 * Add information to the error report
	 * @abstract
	 * @param array $val
	 * @return void
	 */
	abstract function addInfo($val);

	/**
	 * Output the error
	 * @abstract
	 * @return void
	 */
	abstract function output();

	/**
	 * Set the hideTrace variable
	 * @param bool $val
	 * @return void
	 */
	public final function hideTrace($val){
		$this->hideTrace = $val;
	}

	/**
	 * Parse arguement in the error report trace to keep large objects/arrays from being printed out
	 * @param mixed $v
	 * @return string
	 */
	public final function parseArgument($v){
		$display = $v;
		if (is_array($v)){
			if (sizeof($v) > 15){
				$display = 'Array(' . sizeof($v) . ')';
			}
			else{
				$display = $this->parseArrayArgument($v, 10);
			}
		}
		elseif (is_object($v)){
			$display = 'Object ' . get_class($v);
		}
		return (string) $display;
	}

	/**
	 * Format the trace from the error report
	 * @param array $phpTrace
	 * @return string
	 */
	public final function parseTrace($phpTrace){
		$traceList = htmlBase::newElement('table')->setCellPadding(2)->setCellSpacing(0)->addClass('phpTrace');
		if ($this->hideTrace === true){
			$traceList->css(array('display' => 'none'));
		}
		foreach($phpTrace as $tNum => $tInfo){
			$traceTable = htmlBase::newElement('table')->setCellPadding(2)->setCellSpacing(0);
			$traceTable->addBodyRow(array('columns' => array(array('addCls' => 'main', 'valign' => 'top', 'text' => '<b>File:</b>'), array('addCls' => 'main', 'valign' => 'top', 'text' => (isset($tInfo['file']) ? $tInfo['file'] : 'N/A')))));
			$traceTable->addBodyRow(array('columns' => array(array('addCls' => 'main', 'valign' => 'top', 'text' => '<b>Line:</b>'), array('addCls' => 'main', 'valign' => 'top', 'text' => (isset($tInfo['line']) ? $tInfo['line'] : 'N/A')))));
			$traceTable->addBodyRow(array('columns' => array(array('addCls' => 'main', 'valign' => 'top', 'text' => '<b>Function:</b>'), array('addCls' => 'main', 'valign' => 'top', 'text' => (isset($tInfo['class']) ? $tInfo['class'] . $tInfo['type'] : '') . $tInfo['function']))));
			$argsTable = 'None';
			if (!empty($tInfo['args'])){
				$argsTable = htmlBase::newElement('table')->setCellPadding(2)->setCellSpacing(0);
				foreach($tInfo['args'] as $k => $v){
					$argsTable->addBodyRow(array('columns' => array(array('addCls' => 'main', 'valign' => 'top', 'text' => '#' . $k . ': '), array('addCls' => 'main', 'valign' => 'top', 'text' => $this->parseArgument($v)))));
				}
			}
			$traceTable->addBodyRow(array('columns' => array(array('addCls' => 'main', 'valign' => 'top', 'text' => '<b>Arguments:</b>'), array('addCls' => 'main', 'valign' => 'top', 'text' => $argsTable))));
			$traceList->addBodyRow(array('columns' => array(array('addCls' => 'main', 'valign' => 'top', 'text' => '#' . $tNum . ':'), array('addCls' => 'main', 'valign' => 'top', 'text' => $traceTable))));
		}
		return $traceList->draw();
	}

	/**
	 * Parse an array argument from the trace to make it readable
	 * @param array $arr The array to be parsed
	 * @param int $max Maximum number of indexes in the array to show
	 * @return string
	 */
	public final function parseArrayArgument($arr, $max = 5){
		$arrayArgsTable = htmlBase::newElement('table')->setCellPadding(2)->setCellSpacing(0);
		$arrayArgsTable->addBodyRow(array('columns' => array(array('addCls' => 'main', 'valign' => 'top', 'css' => array('white-space' => 'nowrap'), 'text' => 'Array ('), array('addCls' => 'main', 'colspan' => 3, 'valign' => 'top', 'text' => ''))));
		foreach($arr as $k => $v){
			$arrayArgsTable->addBodyRow(array('columns' => array(array('addCls' => 'main', 'valign' => 'top', 'text' => ''), array('addCls' => 'main', 'valign' => 'top', 'text' => $k), array('addCls' => 'main', 'valign' => 'top', 'text' => '=>'), array('addCls' => 'main', 'valign' => 'top', 'text' => $this->parseArgument($v)))));
		}
		$arrayArgsTable->addBodyRow(array('columns' => array(array('addCls' => 'main', 'valign' => 'top', 'colspan' => 4, 'text' => ')'))));
		return $arrayArgsTable->draw();
	}

	/**
	 * Add parsed error information to the main error table
	 * @param htmlElement_table $ErrorTable
	 * @return void
	 */
	public final function parseAddedInfo(&$ErrorTable){
		foreach($this->e->addedInfo as $label => $text){
			$ErrorTable->addBodyRow(array('columns' => array(array('addCls' => 'main', 'valign' => 'top', 'text' => '<b>' . $label . ':</b>'), array('addCls' => 'main', 'valign' => 'top', 'text' => $text))));
		}
	}
}
?>