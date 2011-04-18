<?php
/*
	Multi Stores Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class multiStore_admin_modules_edit extends Extension_multiStore {

	public function __construct(){
		parent::__construct('multiStore');
	}
	
	public function load(){
		if ($this->enabled === false) return;
		
		EventManager::attachEvent('ApplicationTemplateBeforeInclude', null, $this);
	}
	
	public function ApplicationTemplateBeforeInclude(){
		global $App;
		$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.tabs.js');
	}
	
	public function loadTabs(&$tableObj, &$moduleInfo){
		$multiStoreTabs = htmlBase::newElement('tabs')->setId('storeTabs');
		$multiStoreTabs->addTabHeader('tab_global', array(
			'text' => 'Global'
		))->addTabPage('tab_global', array(
			'text' => $tableObj->draw()
		));
		
		$stores = $this->getStoresArray();
		$languages = tep_get_languages();
		$n=sizeof($languages);

		$Qconfig = Doctrine_Query::create()
		->select('configuration_key, configuration_value, stores_id')
		->from('StoresConfiguration')
		->whereIn('configuration_key', $moduleInfo['configKeys'])
		->execute();
		if ($Qconfig->count() > 0){
			$configInfo = array();
			foreach($Qconfig->toArray() as $result){
				$configInfo[$result['stores_id']][$result['configuration_key']] = $result['configuration_value'];
			}
		}

		foreach($stores as $sInfo){
			if (isset($cInfo)) unset($cInfo);
			if (isset($configInfo) && array_key_exists($sInfo['stores_id'], $configInfo)){
				$cInfo = $configInfo[$sInfo['stores_id']];
			}
			$radioSet = htmlBase::newElement('radio');
			$radioSet->addGroup(array(
				'name' => 'store_show_method[' . $sInfo['stores_id'] . ']',
				'addCls' => 'showMethod',
				'checked' => 'use_global',
				'data' => array(
					array(
						'label' => 'Use Global',
						'value' => 'use_global',
					),
					array(
						'label' => 'Use Custom',
						'value' => 'use_custom'
					)
				)
			));

			if (isset($cInfo)){
				$radioSet->setChecked('use_custom');
			}

			$storeTableObj = htmlBase::newElement('table')->addClass('configTable')->setCellPadding(5)->setCellSpacing(0);
			if (!isset($pInfo) || (isset($pInfo) && $pInfo['show_method'] != 'use_custom')){
				$storeTableObj->hide();
			}
			reset($moduleInfo['keys']);
			while (list($key, $value) = each($moduleInfo['keys'])){
				if (isset($cInfo) && array_key_exists($key, $cInfo)){
					$val = $cInfo[$key];
				}else{
					$val = $value['value'];
				}
				
				if ($value['set_function']){
					$function = $value['set_function'];
					switch(true){
						case (stristr($value['set_function'], 'tep_cfg_select_option')):
							$type = 'radio';
							$function = str_replace(
								'tep_cfg_select_option',
								'tep_cfg_select_option_elements',
								$value['set_function']
							);
							break;
						case (stristr($value['set_function'], 'tep_cfg_pull_down_order_statuses')):
							$type = 'drop';
							$function = str_replace(
								'tep_cfg_pull_down_order_statuses',
								'tep_cfg_pull_down_order_statuses_element',
								$value['set_function']
							);
							break;
						case (stristr($value['set_function'], 'tep_cfg_pull_down_zone_classes')):
							$type = 'drop';
							$function = str_replace(
								'tep_cfg_pull_down_zone_classes',
								'tep_cfg_pull_down_zone_classes_element',
								$value['set_function']
							);
							break;
						case (stristr($value['set_function'], 'tep_cfg_select_multioption')):
						case (stristr($value['set_function'], '_selectOptions')):
							$type = 'checkbox';
							$function = str_replace(
								array(
									'tep_cfg_select_multioption',
									'_selectOptions'
								),
								'tep_cfg_select_multioption_element',
								$value['set_function']
							);
							break;
					}
					eval('$inputField = ' . $function . "'" . $val . "', '" . $key . "');");
					
					if (is_object($inputField)){
						if ($type == 'checkbox'){
							$inputField->setName('store_configuration[' . $sInfo['stores_id'] . '][' . $key . '][]');
						}else{
							$inputField->setName('store_configuration[' . $sInfo['stores_id'] . '][' . $key . ']');
						}
					}elseif (substr($inputField, 0, 3) == '<br'){
						$inputField = substr($inputField, 4);
					}
				} else {
					$inputField = tep_draw_input_field('store_configuration[' . $sInfo['stores_id'] . '][' . $key . ']', $val);
				}
				$storeTableObj->addBodyRow(array(
					'columns' => array(
						array(
							'text' => '<b>' . $value['title'] . '</b>',
							'addCls' => 'main',
							'valign' => 'top'
						),
						array(
							'text' => $inputField,
							'addCls' => 'main',
							'valign' => 'top'
						),
						array(
							'text' => $value['description'],
							'addCls' => 'main',
							'valign' => 'top'
						)
					)
				));
			}
			
			$multiStoreTabs->addTabHeader('storeTabs_store_' . $sInfo['stores_id'], array(
				'text' => $sInfo['stores_name']
			))->addTabPage('storeTabs_store_' . $sInfo['stores_id'], array(
				'text' => 'Configuration Method: ' . $radioSet->draw() . '<br /><br />' . $storeTableObj->draw()
			));
		}
		$tableObj = $multiStoreTabs;
	}
}
?>