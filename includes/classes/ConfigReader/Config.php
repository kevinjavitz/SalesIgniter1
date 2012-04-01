<?php
class Config
{

	private $cfg = array(
		'key'          => '',
		'value'        => '',
		'value_glue'   => ',',
		'title'        => '',
		'description'  => '',
		'use_function' => null,
		'set_function' => null,
		'is_editable'  => true
	);

	public function __construct($dataArray) {
		$this->cfg = array_merge($this->cfg, $dataArray);
	}

	public function getKey() {
		return $this->cfg['key'];
	}

	public function getValue($checkUseFunction = false) {
		$value = $this->cfg['value'];
		if ($checkUseFunction === true){
			$value = $this->parseValue();
		}
		return $value;
	}

	public function setValue($val){
		$this->cfg['value'] = $val;
	}

	public function getGlue(){
		return $this->cfg['value_glue'];
	}

	public function getTitle() {
		return $this->cfg['title'];
	}

	public function getDescription() {
		return $this->cfg['description'];
	}

	public function hasUseFunction() {
		return ($this->cfg['use_function'] !== null);
	}

	public function getUseFunction() {
		return $this->cfg['use_function'];
	}

	public function hasSetFunction() {
		return ($this->cfg['set_function'] !== null);
	}

	public function getSetFunction() {
		return $this->cfg['set_function'];
	}

	public function isEditable(){
		return $this->cfg['is_editable'];
	}

	public function parseValue(){
		$configurationValue = $this->getValue();
		if ($this->hasUseFunction() === true){
			$useFunction = $this->getUseFunction();
			if (ereg('->', $useFunction)){
				$class_method = explode('->', $useFunction);
				if (!is_object(${$class_method[0]})){
					include(sysConfig::get('DIR_WS_CLASSES') . $class_method[0] . '.php');
					${$class_method[0]} = new $class_method[0]();
				}
				$cfgValue = tep_call_function($class_method[1], $configurationValue, ${$class_method[0]});
			}
			else {
				$cfgValue = tep_call_function($useFunction, $configurationValue);
			}
		}
		else {
			$cfgValue = $configurationValue;
		}
		return $cfgValue;
	}
}
