<?php
/**
 * Input Element Class
 * @package Html
 */
class htmlElement_input implements htmlElementPlugin {
	protected $inputElement, $labelElement, $labelElementPosition, $labelElementSeparator;
	
	public function __construct(){
		$this->inputElement = new htmlElement('input');
		$this->inputElement->attr('type','text');
		$this->labelElement = false;
		$this->labelElementPosition = 'before';
		$this->labelElementSeparator = '';
	}
	
	public function __call($function, $args){
		$return = call_user_func_array(array($this->inputElement, $function), $args);
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
		$this->inputElement->attr('id', $val);
		return $this;
	}
	
	public function setName($val){
		$this->inputElement->attr('name', $val);
		return $this;
	}
	
	public function draw(){
		$html = '';
		if ($this->labelElement !== false){
			if ($this->inputElement->hasAttr('id') === true){
				$this->labelElement->attr('for', $this->inputElement->attr('id'));
			}
			if ($this->labelElementPosition == 'before'){
				$html .= $this->labelElement->draw();
				if (is_object($this->labelElementSeparator)){
					$html .= $this->labelElementSeparator->draw();
				}else{
					$html .= $this->labelElementSeparator;
				}
			}
		}
		
		$html .= $this->inputElement->draw();
		
		if ($this->labelElement !== false){
			if ($this->labelElementPosition == 'after' || $this->labelElementPosition === false){
				if (is_object($this->labelElementSeparator)){
					$html .= $this->labelElementSeparator->draw();
				}else{
					$html .= $this->labelElementSeparator;
				}
				$html .= $this->labelElement->draw();
			}
		}
		return $html;
	}
	/* Required Functions From Interface: htmlElementPlugin --END-- */
	
	public function setValue($val){
		$this->inputElement->attr('value', stripslashes($val));
		return $this;
	}
	
	public function setType($type){
		$this->inputElement->attr('type', $type);
		return $this;
	}
	
	public function setSize($val){
		$this->inputElement->attr('size', $val);
		return $this;
	}
	
	public function setLabel($val){
		if ($this->labelElement === false){
			$this->labelElement = new htmlElement('label');
			if ($this->labelElementPosition === false){
				$this->labelElementPosition = 'after';
			}
		}
		$this->labelElement->html($val);
		return $this;
	}
	
	public function setLabelPosition($val){
		$this->labelElementPosition = $val;
		return $this;
	}
	
	public function setLabelSeparator($val){
		$this->labelElementSeparator = $val;
		return $this;
	}
	
	public function setChecked($val){
		if ($val === true){
			$this->inputElement->attr('checked', 'checked');
		}else{
			$this->inputElement->removeAttr('checked');
		}
		return $this;
	}

	public function setRequired($val){
		if ($val === true){
			$this->inputElement->addClass('required');
		}else{
			$this->inputElement->removeClass('required');
		}
		return $this;
	}
}
?>