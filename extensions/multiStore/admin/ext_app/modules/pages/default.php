<?php
/*
	Multi Stores Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class multiStore_admin_modules_default extends Extension_multiStore
{

	public function __construct() {
		parent::__construct('multiStore');
	}

	public function load() {
		if ($this->isEnabled() === false) {
			return;
		}

		EventManager::attachEvents(array(
				'ModuleEditWindowBeforeDraw'
			), null, $this);
	}

	public function ModuleEditWindowBeforeDraw(&$TabPanel, $moduleCode, $moduleType, $modulePath) {
		$multiStoreTabs = htmlBase::newElement('tabs')
			->addClass('makeTabPanel')
			->addClass('makeTabsVertical')
			->setId('storeTabs');
		$multiStoreTabs->addTabHeader('tab_global', array(
				'text' => 'Global'
			))->addTabPage('tab_global', array(
				'text' => $TabPanel->draw()
			));

		$stores = $this->getStoresArray();

		$DefaultConfig = new ModuleConfigReader(
			$moduleCode,
			$moduleType,
			$modulePath
		);
		foreach($stores as $sInfo){
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

			$Qcheck = Doctrine_Query::create()
				->select('count(*) as total')
				->from('StoresModulesConfiguration')
				->where('module_type = ?', $moduleType)
				->andWhere('module_code = ?', $moduleCode)
				->andWhere('store_id = ?', $sInfo['stores_id'])
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if ($Qcheck && $Qcheck[0]['total'] > 0){
				$radioSet->setChecked('use_custom');
			}

			$tabs = array();
			$tabsPages = array();
			$panelId = 1;
			$tabId = 1;
			foreach($DefaultConfig->getConfig() as $cfg){
				if (!isset($tabs[$cfg->getTab()])){
					$tabs[$cfg->getTab()] = array(
						'panelId' => 'panel-' . $panelId . '-page-' . $tabId,
						'panelHeader' => $cfg->getTab(),
						'panelTable' => htmlBase::newElement('table')
							->addClass('configTable')
							->setCellPadding(5)
							->setCellSpacing(0)
					);
					$tabId++;
				}

				$Qconfig = Doctrine_Query::create()
					->select('configuration_key, configuration_value, store_id')
					->from('StoresModulesConfiguration')
					->where('module_type = ?', $moduleType)
					->andWhere('module_code = ?', $moduleCode)
					->andWhere('configuration_key = ?', $cfg->getKey())
					->andWhere('store_id = ?', $sInfo['stores_id'])
					->fetchOne();

				if ($Qconfig){
					$fieldVal = $Qconfig->configuration_value;
				}else{
					$fieldVal = $cfg->getValue();
				}

				if ($cfg->hasSetFunction() === true){
					$function = $cfg->getSetFunction();
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
					eval('$inputField = ' . $function . "'" . $fieldVal . "', '" . $cfg->getKey() . "');");

					if (is_object($inputField)){
						if ($type == 'checkbox'){
							$inputField->setName('store_configuration[' . $sInfo['stores_id'] . '][' . $cfg->getKey() . '][]');
						}
						else {
							$inputField->setName('store_configuration[' . $sInfo['stores_id'] . '][' . $cfg->getKey() . ']');
						}
					}
					elseif (substr($inputField, 0, 3) == '<br') {
						$inputField = substr($inputField, 4);
					}
				}
				else {
					$inputField = tep_draw_input_field('store_configuration[' . $sInfo['stores_id'] . '][' . $cfg->getKey() . ']', $fieldVal);
				}

				$tabs[$cfg->getTab()]['panelTable']->addBodyRow(array(
						'columns' => array(
							array(
								'text' => '<b>' . $cfg->getTitle() . '</b>',
								'addCls' => 'main',
								'valign' => 'top'
							),
							array(
								'text' => $inputField,
								'addCls' => 'main',
								'valign' => 'top'
							),
							array(
								'text' => $cfg->getDescription(),
								'addCls' => 'main',
								'valign' => 'top'
							)
						)
					));
			}

			$configurationTabs = htmlBase::newElement('tabs')
				->addClass('makeTabPanel')
				->setId('configuration_tabs_' . $sInfo['stores_id']);

			if (!$Qcheck || $Qcheck[0]['total'] <= 0){
				$configurationTabs->hide();
			}

			foreach($tabs as $pInfo){
				$configurationTabs->addTabHeader($pInfo['panelId'], array('text' => $pInfo['panelHeader']))
					->addTabPage($pInfo['panelId'], array('text' => $pInfo['panelTable']));
			}

			$multiStoreTabs->addTabHeader('storeTabs_store_' . $sInfo['stores_id'], array(
					'text' => $sInfo['stores_name']
				))->addTabPage('storeTabs_store_' . $sInfo['stores_id'], array(
					'text' => 'Configuration Method: ' . $radioSet->draw() . '<br /><br />' . $configurationTabs->draw()
				));
			$panelId++;
		}

		$TabPanel = $multiStoreTabs;
	}
}

?>