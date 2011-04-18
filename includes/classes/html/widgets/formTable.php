<?php
/**
 * Form Table Widget Class
 * @package Html
 */
class htmlWidget_formTable implements htmlWidgetPlugin {
	protected $tableElement;
	
	public function __construct(){
		$this->tableElement = htmlBase::newElement('table')
		->setCellPadding(2)
		->setCellSpacing(0);
	}
	
	public function __call($function, $args){
		$return = call_user_func_array(array($this->tableElement, $function), $args);
		if (!is_object($return)){
			return $return;
		}
		return $this;
	}
	
	/* Required Classes From Interface: htmlElementPlugin --BEGIN-- */
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
		return $this->tableElement->draw();
	}
	/* Required Classes From Interface: htmlElementPlugin --END-- */
	
	public function setAddClass($class){
		$this->addClass = $class;
		return $this;
	}
	
	public function addRow($leftCol, $rightCol = false){
		$colArr = array();
		$colArr[0] = array(
			'text' => $leftCol
		);
		
		if ($rightCol !== false){
			$colArr[1] = array(
				'text' => $rightCol
			);
		}else{
			$colArr[0]['colspan'] = '2';
		}
		
		foreach($colArr as $idx => $col){
			$colArr[$idx]['valign'] = 'top';
			$colArr[$idx]['align'] = 'left';
			
			if (isset($this->addClass)){
				$colArr[$idx]['addCls'] = $this->addClass;
			}
		}
		
		$this->tableElement->addBodyRow(array(
			'columns' => $colArr
		));
	}
}
?>