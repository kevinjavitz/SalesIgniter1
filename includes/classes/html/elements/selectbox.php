<?php
/**
 * Select Box Element Class
 * @package Html
 */
class htmlElement_selectbox implements htmlElementPlugin {
	protected $selectElement, $selectOptions, $selectedOptionValue, $selectedOptionIndex, $labelElement, $labelElementPosition, $labelElementSeparator;
	
	public function __construct(){
		$this->selectElement = new htmlElement('select');
		$this->selectOptions = array();
		$this->optionsAppended = false;
		$this->labelElement = false;
		$this->labelElementPosition = 'before';
		$this->labelElementSeparator = '';
	}
	
	public function __call($function, $args){
		$return = call_user_func_array(array($this->selectElement, $function), $args);
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
		$this->selectElement->attr('id', $val);
		return $this;
	}
	
	public function setName($val){
		$this->selectElement->attr('name', $val);
		return $this;
	}
	
	public function draw($skipOptionAdd = false){
		if ($this->optionsAppended === false){
			$options = $this->selectOptions;
		}else{
			$options = &$this->selectElement->getAppendedElements();
		}
		foreach($options as $index => $optionObj){
			$optionObj->removeAttr('selected');
			if ($optionObj->val() == $this->selectedOptionValue){
				$optionObj->attr('selected', 'selected');
			}
			
			if ($this->optionsAppended === false){
				$this->selectElement->append($optionObj);
			}
		}
		
		if ($this->optionsAppended === false){
			$this->optionsAppended = true;
		}
		
		$html = '';
		if ($this->labelElement !== false){
			if ($this->selectElement->hasAttr('id') === true){
				$this->labelElement->attr('for', $this->selectElement->attr('id'));
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
		
		$html .= $this->selectElement->draw();
		
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
	
	public function addOption($val, $html = '', $selected = false, $attributes = null){
		$optionEl = new htmlElement('option');
		$optionEl->attr('value', $val);
		if (strlen($html) > 0){
			$optionEl->html($html);
		}
		if ($selected === true){
			$optionEl->attr('selected', 'selected');
		}

		if (is_null($attributes) === false){
			foreach($attributes as $k => $v){
				$optionEl->attr($k, $v);
			}
		}

		$this->selectOptions[] = $optionEl;
		return $this;
	}

	public function addOptionWithAttributes($val, $html = '', $attributes, $selected = false){
		$optionEl = new htmlElement('option');
		$optionEl->attr('value', $val);
		foreach($attributes as $attr ){
			$optionEl->attr($attr['name'], $attr['value']);			
		}
		if (strlen($html) > 0){
			$optionEl->html($html);
		}
		if ($selected === true){
			$optionEl->attr('selected', 'selected');
		}
		$this->selectOptions[] = $optionEl;
		return $this;
	}
	                      
	public function removeOption($optionValue){
		if (!empty($this->selectOptions)){
			foreach($this->selectOptions as $idx => $optionObj){
				if ($optionObj->val() == $optionValue){
					unset($this->selectOptions[$idx]);
					ksort($this->selectOptions);
					break;
				}
			}
		}
		return $this;
	}
	
	public function addOptionObj($optionObj){
		$this->selectOptions[] = $optionObj;
		return $this;
	}
	
	public function selectOptionByIndex(){
		die('never used, guess it is time.');
	}
	
	public function selectOptionByValue($val){
		$this->selectedOptionValue = $val;
		return $this;
	}
	
	public function change($event){
		$this->selectElement->attr('onchange', $event);
		return $this;
	}
	
	public function setSize($val){
		$this->selectElement->attr('size', $val);
		return $this;
	}
	
	public function setLabel($val){
		if ($this->labelElement === false){
			$this->labelElement = new htmlElement('label');
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
}
?>