<?php
	$json = array(
		'success' => false,
		'message' => 'Module not installed, this error should never show up'
	);
	
	$Qconfig = Doctrine_Query::create()
	->from('Modules m')
	->leftJoin('m.ModulesConfiguration c')
	->where('modules_code = ?', $_GET['module'])
	->andWhere('modules_type = ?', 'stream_provider')
	->orderBy('c.sort_order ASC')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if ($Qconfig){
		$json = array(
			'success' => true,
			'message' => 'No Additional Configuration Available'
		);
		
		$providerSettings = array();
		if (isset($_GET['pID'])){
			$Qprovider = Doctrine_Query::create()
			->select('provider_module, provider_module_settings')
			->from('ProductsStreamProviders')
			->where('provider_id = ?', (int) $_GET['pID'])
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if ($Qprovider){
				if ($Qprovider[0]['provider_module'] == $_GET['module'] && !empty($Qprovider[0]['provider_module_settings'])){
					$providerSettings = unserialize($Qprovider[0]['provider_module_settings']);
				}
			}
		}
		
		$fields = array();
		foreach($Qconfig as $mInfo){
			if (isset($mInfo['ModulesConfiguration']) && !empty($mInfo['ModulesConfiguration'])){
				foreach($mInfo['ModulesConfiguration'] as $cInfo){
					if (substr($cInfo['configuration_key'], -6) == 'STATUS') continue;
					if (isset($json['message'])) unset($json['message']);
					
					$key = $cInfo['configuration_key'];
					$value = $cInfo['configuration_value'];
					if (isset($providerSettings[$key])){
						$value = $providerSettings[$key];
					}
					if (isset($cInfo['set_function']) && $cInfo['set_function'] != 'isArea') {
						eval('$inputField = ' . $cInfo['set_function'] . "'" . $value . "', '" . $key . "');");
					} else if (isset($cInfo['set_function']) && $cInfo['set_function'] == 'isArea') {
						$inputField = tep_draw_textarea_field('configuration[' . $key . ']', 'hard', 30, 5, $value, 'class="makeModFCK" style="width:90%;"');
					}else {
						$inputField = htmlBase::newElement('input')
						->css(array(
							'width' => '100%'
						))
						->setName('configuration[' . $key . ']')
						->val($value);
					}
					
					$fields[] = array(
						'title' => $cInfo['configuration_title'],
						'description' => $cInfo['configuration_description'],
						'inputField' => $inputField
					);
				}
			}
		}
		
		if (!empty($fields)){
			$htmlTable = htmlBase::newElement('table')
			->css(array(
				'width' => '75%'
			))
			->setCellPadding(3)
			->setCellSpacing(0);
			
			foreach($fields as $fInfo){
				$htmlTable->addBodyRow(array(
					'columns' => array(
						array('text' => '<b>' . $fInfo['title'] . '</b><br>' . $fInfo['description'])
					)
				));
				$htmlTable->addBodyRow(array(
					'columns' => array(
						array('text' => $fInfo['inputField'])
					)
				));
			}
			
			$json['fields'] = $htmlTable->draw();
		}
	}
	
	EventManager::attachActionResponse($json, 'json');
?>