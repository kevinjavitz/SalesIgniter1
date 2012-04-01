<?php
/*
 * Sales Igniter E-Commerce System
 * Version: 2.0
 *
 * I.T. Web Experts
 * http://www.itwebexperts.com
 *
 * Copyright (c) 2011 I.T. Web Experts
 *
 * This script and its source are not distributable without the written conscent of I.T. Web Experts
 */

require(__DIR__ . '/Config.php');

class ConfigurationReader
{

	public $configData = array();

	public $mappings = array();

	public $compareData = array();

	public $title = '';

	public $description = '';

	public $key = '';

	public function loadConfiguration($configFile, $parseConfig = true) {
		$xmlObj = simplexml_load_file(
			$configFile,
			'SimpleXMLElement',
			LIBXML_NOCDATA
		);

		if (!$xmlObj){
			die('Config Error::' . $configFile);
		}

		$this->title = (string)$xmlObj->title;
		$this->description = (string)$xmlObj->description;
		$this->key = (string)$xmlObj->key;

		if ($parseConfig === true){
			$this->parseXmlConfig($xmlObj);
		}
	}

	public function getTitle() {
		return $this->title;
	}

	public function getDescription() {
		return $this->description;
	}

	public function getKey() {
		return $this->key;
	}

	public function loadCompareData(SimpleXMLElement $xmlObj) {
		return array();
	}

	public function loadToSystem() {
		foreach($this->configData as $tInfo){
			foreach($tInfo['config'] as $cfg){
				sysConfig::set($cfg->getKey(), $cfg->getValue());
			}
		}
	}

	public function check(SimpleXMLElement $xmlObj) {
		return true;
	}

	public function parseXmlConfig(SimpleXMLElement $xmlObj) {
		global $App;
		if ($this->check($xmlObj) === false) {
			return;
		}

		if (empty($this->compareData)){
			$this->compareData = $this->loadCompareData($xmlObj);
		}

		foreach($xmlObj->tabs->children() as $TabKey => $TabInfo){
			$configurations = array();
			$configCount = 0;
			foreach($TabInfo->configurations->children() as $ConfigKey => $ConfigInfo){
				$cfgKey = (string)$ConfigKey;
				if (isset($this->compareData[$cfgKey])){
					$configVal = $this->compareData[$cfgKey]['configuration_value'];
				}
				else {
					$configVal = (string)$ConfigInfo->value;
				}

				$configArr = array(
					'key'   => $cfgKey,
					'value' => $configVal
				);

				if (isset($ConfigInfo->value_glue)){
					$configArr['value_glue'] = (string)$ConfigInfo->value_glue;
					$configArr['value'] = explode($configArr['value_glue'], $configArr['value']);
				}

				if (
					$App && (
						($App->getEnv() == 'admin' && $App->getAppName() == 'extensions' && isset($_GET['action'])) ||
						($App->getEnv() == 'admin' && $App->getAppName() == 'modules' && isset($_GET['action'])) ||
						($App->getEnv() == 'admin' && $App->getAppName() == 'configuration')
					)
				){
					$configArr['is_editable'] = true;
					$configArr['title'] = (string)$ConfigInfo->title;
					$configArr['description'] = (string)$ConfigInfo->description;
					$configArr['set_function'] = null;
					$configArr['use_function'] = null;
					$configArr['is_deprecated'] = false;

					if (isset($ConfigInfo['editable'])){
						$configArr['is_editable'] = ($ConfigInfo['editable'] == 'true' ? true : false);
					}

					if (isset($ConfigInfo['deprecated'])){
						$configArr['is_deprecated'] = ($ConfigInfo['deprecated'] == 'true' ? true : false);
					}

					if (isset($ConfigInfo->set_function)){
						if (isset($ConfigInfo->set_function->type)){
							$setFunction = array(
								'type' => (string)$ConfigInfo->set_function->type
							);

							if (isset($ConfigInfo->set_function->data_function)){
								$args = array();
								if (isset($ConfigInfo->set_function->data_function->name)){
									$function = (string)$ConfigInfo->set_function->data_function->name;
									if (isset($ConfigInfo->set_function->data_function->args)){
										foreach($ConfigInfo->set_function->data_function->args->children() as $arg){
											$argStr = (string)$arg;
											if (substr($argStr, 0, 7) == 'CONFIG:'){
												$argStr = sysConfig::get(substr($argStr, 7));
											}

											$args[] = $argStr;
										}
									}
								}
								else {
									$function = (string)$ConfigInfo->set_function->data_function;
								}
								$data = call_user_func_array($function, $args);
								foreach($data as $dInfo){
									$setFunction['values'][] = array(
										'id'   => $dInfo['id'],
										'text' => $dInfo['text']
									);
								}
							}
							elseif (isset($ConfigInfo->set_function->values)) {
								foreach($ConfigInfo->set_function->values->children() as $value){
									$id = (string)$value;
									if (isset($value['id'])){
										$id = $value['id'];
									}
									$setFunction['values'][] = array(
										'id'   => (string)$id,
										'text' => (string)$value
									);
								}
							}

							if (isset($ConfigInfo->set_function->attributes)){
								$setFunction['attributes'] = array();
								foreach($ConfigInfo->set_function->attributes->children() as $k => $v){
									$setFunction['attributes'][$k] = $v;
								}
							}

							if (!isset($setFunction['values']) || empty($setFunction['values'])){
								if (isset($ConfigInfo->set_function['use_default_if_no_data']) && $ConfigInfo->set_function['use_default_if_no_data'] == 'true'){
									$setFunction = null;
								}
							}
						}
						else {
							$setFunction = (string)$ConfigInfo->set_function;
						}
						$configArr['set_function'] = $setFunction;
					}

					if (isset($ConfigInfo->use_function)){
						$configArr['use_function'] = (string)$ConfigInfo->use_function;
					}
				}

				$configurations[$configCount] = new Config($configArr);
				$this->mappings[$configArr['key']] = array((string)$TabKey, $configCount);
				$configCount++;
			}

			if (!isset($this->configData[(string)$TabKey])){
				$this->configData[(string)$TabKey] = array(
					'title'	   => (string)$TabInfo->title,
					'description' => (string)$TabInfo->description,
					'config'	  => $configurations
				);
			}
			else {
				$this->configData[(string)$TabKey]['config'] = array_merge_recursive($this->configData[(string)$TabKey]['config'], $configurations);
			}
		}
	}

	/**
	 * @param bool $key
	 * @return Config
	 */
	public function getConfig($key = false) {
		if ($key === false){
			return $this->configData;
		}
		if (
			isset($this->mappings[$key]) &&
			isset($this->configData[$this->mappings[$key][0]])
		){
			return $this->configData[$this->mappings[$key][0]]['config'][$this->mappings[$key][1]];
		}
		return null;
	}

	public function getInputField(Config $Config, $disable = false, $fieldName = 'configuration') {
		$fieldHtml = 'NOTHING WORKED';
		if ($Config->isEditable() === false){
			$fieldHtml = $Config->getValue();
		}
		elseif ($Config->hasSetFunction() === true) {
			$function = $Config->getSetFunction();
			if (is_array($function)){
				switch($function['type']){
					case 'textarea':
						$inputField = htmlBase::newElement('textarea')
							->setName($fieldName . '[' . $Config->getKey() . ']')
							->html($Config->getValue());

						if (isset($function['attributes'])){
							foreach($function['attributes'] as $k => $v){
								$inputField->attr($k, $v);
							}
						}

						$fieldHtml = $inputField->draw();
						break;
					case 'selectbox':
						$inputField = htmlBase::newElement('selectbox')
							->addClass('notEdited')
							->setName($fieldName . '[' . $Config->getKey() . ']')
							->selectOptionByValue($Config->getValue());

						foreach($function['values'] as $vInfo){
							$inputField->addOption($vInfo['id'], $vInfo['text']);
						}

						$fieldHtml = $inputField->draw();
						break;
					case 'radio':
						$data = array();
						foreach($function['values'] as $vInfo){
							$data[] = array(
								'label' => $vInfo['text'],
								'value' => $vInfo['id']
							);
						}
						$inputField = htmlBase::newElement('radio')
							->addGroup(array(
							'name'          => $fieldName . '[' . $Config->getKey() . ']',
							'separator'     => '<br>',
							'labelPosition' => 'after',
							'checked'       => $Config->getValue(),
							'data'          => $data
						));

						$fieldHtml = $inputField->draw();
						break;
					case 'checkbox':
						$data = array();
						foreach($function['values'] as $vInfo){
							$data[] = array(
								'label' => $vInfo['text'],
								'value' => $vInfo['id']
							);
						}
						$inputField = htmlBase::newElement('checkbox')
							->addGroup(array(
							'name'          => $fieldName . '[' . $Config->getKey() . '][]',
							'separator'     => '<br>',
							'labelPosition' => 'after',
							'checked'       => $Config->getValue(),
							'data'          => $data
						));

						$fieldHtml = $inputField->draw();
						break;
				}
			}
			else {
				switch(true){
					case (stristr($function, 'tep_cfg_select_option')):
						$type = 'radio';
						$function = str_replace(
							'tep_cfg_select_option',
							'tep_cfg_select_option_elements',
							$function
						);
						break;
					case (stristr($function, 'tep_cfg_pull_down_order_statuses')):
						$type = 'drop';
						$function = str_replace(
							'tep_cfg_pull_down_order_statuses',
							'tep_cfg_pull_down_order_statuses_element',
							$function
						);
						break;
					case (stristr($function, 'tep_cfg_pull_down_zone_classes')):
						$type = 'drop';
						$function = str_replace(
							'tep_cfg_pull_down_zone_classes',
							'tep_cfg_pull_down_zone_classes_element',
							$function
						);
						break;
					case (stristr($function, 'tep_cfg_select_multioption')):
					case (stristr($function, '_selectOptions')):
						$type = 'checkbox';
						$function = str_replace(
							array(
								'tep_cfg_select_multioption',
								'_selectOptions'
							),
							'tep_cfg_select_multioption_element',
							$function
						);
						break;
				}
				eval('$inputField = ' . $function . "'" . $Config->getValue() . "', '" . $Config->getKey() . "');");

				if (is_object($inputField)){
					$inputField->addClass('notEdited');
					if ($type == 'checkbox'){
						$inputField->setName($fieldName . '[' . $Config->getKey() . '][]');
					}
					else {
						$inputField->setName($fieldName . '[' . $Config->getKey() . ']');
					}

					if ($disable === true){
						$inputField->disable(true);
					}

					$fieldHtml = $inputField->draw();
				}
				elseif (substr($inputField, 0, 3) == '<br') {
					$fieldHtml = substr($inputField, 4) . '<span style="display:none">' . $function . '</span>';
				}
				else {
					$fieldHtml = $inputField . '<span style="display:none">' . $function . '</span>';
				}
			}
		}
		else {
			$inputField = htmlBase::newElement('input')
				->addClass('notEdited')
				->setName($fieldName . '[' . $Config->getKey() . ']')
				->val($Config->getValue())
				->css(array(
				'width' => '100%'
			));

			if ($disable === true){
				$inputField->disable(true);
			}

			$fieldHtml = $inputField->draw();
		}

		if ($fieldHtml == 'NOTHING WORKED'){
			ob_start();
			echo '<pre>';
			print_r($Config);
			echo '</pre>';
			$fieldHtml .= '<br>' . ob_get_contents();
			ob_end_clean();
		}
		return $fieldHtml;
	}
}
