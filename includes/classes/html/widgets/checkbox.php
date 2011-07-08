<?php
/**
 * Checkbox Element Widget Class
 * @package Html
 */
class htmlWidget_checkbox implements htmlWidgetPlugin {
	protected $inputElement;
	
	public function __construct(){
		$this->inputElement = htmlBase::newElement('input')->setType('checkbox');
		$this->isGroup = false;
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
		if ($this->isGroup === true) die('Error: This is a group of radio inputs, please use data array to set id');
		
		$this->inputElement->setId($val);
		return $this;
	}
	
	public function setName($val){
		if ($this->isGroup === true){
			foreach($this->groupElements as $button){
				$button->setName($val);
			}
		}else{
			$this->inputElement->setName($val);
		}
		return $this;
	}
	
	public function setValue($val){
		if ($this->isGroup === true) die('Error: This is a group of radio inputs, please use data array to set values');
		
		$this->inputElement->val($val);
		return $this;
	}
	
	public function draw(){
		$html = '';
		if ($this->isGroup === true){
			if (is_array($this->groupSeparator)){
				if ($this->groupSeparator['type'] == 'table'){
					$table = htmlBase::newElement('table')->setCellPadding(2)->setCellSpacing(0);
					$columns = array();
					foreach($this->groupElements as $button){
						$columns[] = array('text' => $button->draw());
						if (sizeof($columns) == $this->groupSeparator['cols']){
							$table->addBodyRow(array(
								'columns' => $columns
							));
							$columns = array();
						}
					}
					if (!empty($columns)){
						$table->addBodyRow(array(
							'columns' => $columns
						));
						$columns = array();
					}
					$html .= $table->draw();
				}
			}else{
				$htmlOutput = array();
				foreach($this->groupElements as $button){
					$htmlOutput[] = $button->draw();
				}
				$html .= implode($this->groupSeparator, $htmlOutput);
			}
		}else{
			$html = $this->inputElement->draw();
		}
		
		return $html;
	}
	/* Required Functions From Interface: htmlElementPlugin --END-- */
	
	public function setGroupSeparator($html){
		if ($this->isGroup === true){
			$this->groupSeparator = $html;
		}
		return $this;
	}
	
	public function addGroup(array $data){
		$this->isGroup = true;
		$this->groupSeparator = (isset($data['separator']) ? $data['separator'] : '&nbsp;');
		
		$this->groupElements = array();
		foreach($data['data'] as $bInfo){
			$button = htmlBase::newElement('checkbox')
			->setName($data['name'])
			->setValue($bInfo['value'])
			->setLabel($bInfo['label']);
			
			if (isset($bInfo['labelPosition'])){
				$button->setLabelPosition($bInfo['labelPosition']);
			}
			
			if (isset($bInfo['labelSeparator'])){
				$button->setLabelSeparator($bInfo['labelSeparator']);
			}
			
			if (isset($bInfo['disabled']) && $bInfo['disabled'] === true){
				$button->attr('disabled', 'disabled');
			}
			
			if (isset($data['addCls'])){
				$button->addClass($data['addCls']);
			}
			
			if (isset($bInfo['id'])){
				$button->setId($bInfo['id']);
			}else{
				$number = rand(rand(1, 500), rand(505, 9000))*rand(1, 100)/rand(1, 15);
				$button->setId(strtolower($data['name'] . '_' . str_replace(array('-', ' '), '_', $bInfo['value']) . '_' . round($number)));
			}
			
			if (isset($bInfo['checked']) && ($bInfo['checked'] == $bInfo['value'] || $bInfo['checked'] === true)){
				$button->setChecked(true);
			}else{
				$button->setChecked(false);
			}
			
			$this->groupElements[] = $button;
		}
		
		if (isset($data['checked'])){
			$this->setChecked($data['checked']);
		}
		return $this;
	}

	public function setChecked($val){
		if ($this->isGroup === true){
			foreach($this->groupElements as $i => $button){
				$checked = false;
				if (is_array($val) && in_array($button->val(), $val)){
					$checked = true;
				}elseif ($button->val() == $val){
					$checked = true;
				}
				$button->setChecked($checked);
			}
		}else{		
			if ($val === true){
				$this->inputElement->attr('checked', 'checked');
			}else{
				$this->inputElement->removeAttr('checked');
			}
		}
		
		return $this;
	}
	
	public function isChecked(){
		return $this->inputElement->hasAttr('checked');
	}
}
?>