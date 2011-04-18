<?php
/**
 * Sortable List Widget Class
 * @package Html
 */
class htmlWidget_sortable_list implements htmlWidgetPlugin {
	protected $listElement, $listItems;
	
	public function __construct(){
		$this->listElement = htmlBase::newElement('list');
		
		$this->listElement
		->addClass('sortableList')
		->css(array(
		'list-style' => 'none',
		'margin'     => '0px',
		'padding'    => '0px'
		));
	}
	
	public function __call($function, $args){
		$return = call_user_func_array(array($this->listElement, $function), $args);
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
		$this->listElement->attr('id', $val);
		return $this;
	}
	
	public function setName($val){
		$this->listElement->attr('name', $val);
		return $this;
	}
	
	public function draw(){
		if (sizeof($this->listItems) > 0){
			foreach($this->listItems as $liObj){
				$this->listElement->append($liObj);
			}
		}
		return $this->listElement->draw();
	}
	/* Required Functions From Interface: htmlElementPlugin --END-- */
}
?>