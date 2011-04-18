<?php
/**
 * A Element Class
 * @package Html
 */
class htmlElement_a implements htmlElementPlugin {
	public function __construct(){
		$this->aElement = new htmlElement('a');
	}
	
	public function __call($function, $args){
		$return = call_user_func_array(array($this->aElement, $function), $args);
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
		$this->aElement->attr('id', $val);
		return $this;
	}
	
	public function setName($val){
		$this->aElement->attr('name', $val);
		return $this;
	}
	
	public function draw(){
		return $this->aElement->draw();
	}
	/* Required Functions From Interface: htmlElementPlugin --END-- */
	
	public function setHref($val){
		$this->aElement->attr('href', $val);
		return $this;
	}
}
?>