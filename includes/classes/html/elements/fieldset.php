<?php
/**
 * Fieldset Element Class
 * @package Html
 */
class htmlElement_fieldset implements htmlElementPlugin {
	protected $textareaElement, $labelElement, $labelElementPosition;
	
	public function __construct(){
		$this->fieldsetElement = new htmlElement('fieldset');
		$this->legendElement = new htmlElement('legend');
	}
	
	public function __call($function, $args){
		$return = call_user_func_array(array($this->fieldsetElement, $function), $args);
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
		$this->textareaElement->attr('id', $val);
		return $this;
	}
	
	public function setName($val){
		$this->textareaElement->attr('name', $val);
		return $this;
	}
	
	public function draw(){
		$html = '';
		if ($this->legendElement !== false){
			$this->fieldsetElement->prepend($this->legendElement);
		}
		
		$html .= $this->fieldsetElement->draw();
		return $html;
	}
	/* Required Functions From Interface: htmlElementPlugin --END-- */
	
	public function setLegend($val){
		$this->legendElement->html($val);
		return $this;
	}
}
?>