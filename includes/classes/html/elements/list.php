<?php
/**
 * (Un)Ordered List Class
 * @package Html
 */
class htmlElement_list implements htmlElementPlugin {
	protected $listElement, $listItems;
	
	public function __construct(){
		$this->listElement = new htmlElement('ul');
		$this->listItems = array();
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
	
	public function setType($type){
		if ($type == 'ordered'){
			$this->listElement->changeElement('ol');
		}else{
			$this->listElement->changeElement('ul');
		}
		return $this;
	}
	
	public function hasListItems(){
		return sizeof($this->listItems) > 0;
	}
	
	public function addItem($id = '', $html = ''){
		$liObj = new htmlElement('li');
		
		if ($id != ''){
			$liObj->attr('id', $id);
		}
		
		if (is_object($html)){
			$liObj->append($html);
		}else{
			$liObj->html($html);
		}
		
		$this->listItems[] = $liObj;
		return $this;
	}
	
	public function addItemObj($liObj){
		$this->listItems[] = $liObj;
		return $this;
	}
}
?>