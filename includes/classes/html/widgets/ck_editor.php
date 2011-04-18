<?php
/**
 * CK Editor Widget Class
 * @package Html
 */
class htmlWidget_ck_editor implements htmlWidgetPlugin {
	protected $element;
	
	public function __construct(){
		$this->element = new htmlElement('textarea');
		
		$this->element
		->addClass('makeFCK')
		->attr('wrap', 'soft')
		->attr('cols', 40)
		->attr('rows', 10);
	}
	
	public function __call($function, $args){
		$return = call_user_func_array(array($this->element, $function), $args);
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
		$this->element->attr('id', $val);
		return $this;
	}
	
	public function setName($val){
		$this->element->attr('name', $val);
		return $this;
	}
	
	public function setValue($val){
		$this->element->attr('value', $val);
		return $this;
	}
	
	public function draw(){
		return $this->element->draw();
	}
	/* Required Functions From Interface: htmlElementPlugin --END-- */
}
?>