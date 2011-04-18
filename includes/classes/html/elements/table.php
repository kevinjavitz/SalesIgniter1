<?php
/**
 * Table Element Class
 * @package Html
 */
class htmlElement_table implements htmlElementPlugin {
	protected $tableElement,
	$tableHeaderElement,
	$tableBodyElement,
	$tableFooterElement,
	$stripeRows,
	$evenRowCls,
	$oddRowCls;

	public function __construct(){
		$this->tableElement = new htmlElement('table');
		$this->tableHeaderElement = new htmlElement('thead');
		$this->tableBodyElement = new htmlElement('tbody');
		$this->tableFooterElement = new htmlElement('tfoot');
		$this->bodyRowNum = 0;
		$this->stripeRows = false;
	}
	
	public function __call($function, $args){
		$return = call_user_func_array(array($this->tableElement, $function), $args);
		if (!is_object($return)){
			return $return;
		}
		return $this;
	}
	
	/* Required Functions From Interface: htmlElementPlugin --BEGIN-- */
	public function startChain(){
		return $this;
	}
	
	public function setId($val){
		$this->tableElement->attr('id', $val);
		return $this;
	}
	
	public function setName($val){
		$this->tableElement->attr('name', $val);
		return $this;
	}

	public function draw(){
		$this->tableElement->append($this->tableHeaderElement)->append($this->tableBodyElement);
		return $this->tableElement->draw();
	}
	/* Required Functions From Interface: htmlElementPlugin --END-- */
	
	public function setCellPadding($val){
		$this->tableElement->attr('cellpadding', $val);
		return $this;
	}

	public function setCellSpacing($val){
		$this->tableElement->attr('cellspacing', $val);
		return $this;
	}
	
	public function onClick($val){
		$tableRows = &$this->tableBodyElement->getAppendedElements();
		$lastKey = sizeof($tableRows)-1;
		
		$tableRows[$lastKey]->click($val);
		return $this;
	}

	private function parseRow($settings){
		$row = new htmlElement('tr');

		if (isset($settings['addCls'])){
			$classes = explode(' ', $settings['addCls']);
			foreach($classes as $className){
				$row->addClass($className);
			}
		}

		if (isset($settings['css'])){
			foreach($settings['css'] as $k => $v){
				$row->css($k, $v);
			}
		}

		if (isset($settings['attr'])){
			foreach($settings['attr'] as $k => $v){
				$row->attr($k, $v);
			}
		}

		if (isset($settings['click'])){
			$row->click($settings['click']);
		}
		return $row;
	}

	private function parseColumn($tag, $settings){
		global $currencies;
		$col = new htmlElement($tag);
		
		if (!is_object($settings['text'])){
			$colHtml = (isset($settings['text']) && strlen($settings['text']) > 0 ? $settings['text'] : '&nbsp;');
			if (isset($settings['format'])){
				switch($settings['format']){
					case 'int': $colHtml = (int)$colHtml; break;
					case 'float': $colHtml = (float)$colHtml; break;
					case 'string': $colHtml = (string)$colHtml; break;
					case 'currency': $colHtml = $currencies->format($colHtml); break;
				}
			}
			$col->html($colHtml);
		}else{
			$col->append($settings['text']);
		}

		if (isset($settings['align'])){
			$col->attr('align', $settings['align']);
		}

		if (isset($settings['valign'])){
			$col->attr('valign', $settings['valign']);
		}

		if (isset($settings['colspan'])){
			$col->attr('colspan', $settings['colspan']);
		}

		if (isset($settings['addCls'])){
			$col->addClass($settings['addCls']);
		}

		if (isset($settings['attr'])){
			foreach($settings['attr'] as $k => $v){
				$col->attr($k, $v);
			}
		}

		if (isset($settings['css'])){
			foreach($settings['css'] as $k => $v){
				$col->css($k, $v);
			}
		}

		if (isset($settings['click'])){
			$col->click($settings['click']);
		}
		return $col;
	}

	public function addHeaderRow($settings){
		if (!isset($settings['columns'])){
			die('Missing Columns For Table Header Row');
		}

		$tr = $this->parseRow($settings);
		foreach($settings['columns'] as $cInfo){
			$th = $this->parseColumn('th', $cInfo);
			$tr->append($th);
		}
		
		$this->tableHeaderElement->append($tr);
		return $this;
	}

	public function addBodyRow($settings){
		if (!isset($settings['columns'])){
			die('Missing Columns For Table Body Row');
		}

		$tr = $this->parseRow($settings);
		foreach($settings['columns'] as $cInfo){
			$td = $this->parseColumn('td', $cInfo);
			$tr->append($td);
		}
		
		if ($this->stripeRows === true){
			$tr->addClass(($this->bodyRowNum%2 == 0 ? $this->evenRowCls : $this->oddRowCls));
		}
		
		$this->tableBodyElement->append($tr);
		$this->bodyRowNum++;
		return $this;
	}
	
	public function stripeRows($evenCls, $oddCls){
		$this->stripeRows = true;
		$this->evenRowCls = $evenCls;
		$this->oddRowCls = $oddCls;
		return $this;
	}
}
?>