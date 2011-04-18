<?php
/**
 * Star Rating Bar Widget Class
 * @package Html
 */
class htmlWidget_ratingbar implements htmlWidgetPlugin {
	protected $barElement, $settings;
	
	public function __construct(){
		$this->barElement = new htmlElement('div');
		$this->barElement->addClass('starRating');
		$this->settings = array(
			'stars'       => 5,
			'value'       => 0,
			'half'        => false,
			'allowCancel' => false,
			'readOnly'    => false,
			'disabled'    => false
		);
	}
	
	public function __call($function, $args){
		$return = call_user_func_array(array($this->barElement, $function), $args);
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
		$this->barElement->attr('id', $val);
		return $this;
	}
	
	public function setName($val){
		$this->barElement->attr('name', $val);
		return $this;
	}
	
	public function setValue($val){
		$this->settings['value'] = $val;
		return $this;
	}
	
	public function draw(){
		$numberOfStars = $this->settings['stars'];
		if ($this->settings['half'] === true){
			$numberOfStars *= 2;
		}
		/*
		if ($this->settings['allowCancel'] === true){
			$cancelButton = htmlBase::newElement('div')
		}
		*/
		
		for($i=1; $i<$numberOfStars+1; $i++){
			$radioButton = htmlBase::newElement('radio')
			->setId($this->barElement->attr('id') . '-star' . $i)
			->setName($this->barElement->attr('name'))
			->setValue($i);
			
			if ($i == $this->settings['value']){
				$radioButton->setChecked(true);
			}
			
			$this->barElement->append($radioButton);
		}
		
		return $this->barElement->draw();
	}
	/* Required Functions From Interface: htmlElementPlugin --END-- */
	
	public function setStars($val){
		$this->settings['stars'] = $val;
		return $this;
	}
	
	public function setHalf($val){
		$this->settings['half'] = $val;
		return $this;
	}
	
	public function setReadOnly($val){
		$this->settings['readOnly'] = $val;
		return $this;
	}
	
	public function setDisabled($val){
		$this->settings['disabled'] = $val;
		return $this;
	}
	
	public function setAllowCancel($val){
		$this->settings['allowCancel'] = $val;
		return $this;
	}
}
?>