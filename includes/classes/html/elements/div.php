<?php
/**
 * Div Element Class
 * @package Html
 */
class htmlElement_div implements htmlElementPlugin {
	public function __construct(){
		$this->divElement = new htmlElement('div');
	}
	
	public function __call($function, $args){
		$return = call_user_func_array(array($this->divElement, $function), $args);
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
		$this->divElement->attr('id', $val);
		return $this;
	}
	
	public function setName($val){
		$this->divElement->attr('name', $val);
		return $this;
	}
	
	public function draw(){
		return $this->divElement->draw();
	}
	/* Required Functions From Interface: htmlElementPlugin --END-- */
		
}
?>