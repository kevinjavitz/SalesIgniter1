<?php
	$className = $_GET['module'];
	if (!class_exists($className)){
		require(sysConfig::get('DIR_FS_CATALOG_LANGUAGES') . Session::get('language') . '/modules/' . $module_type . '/' . $className . '.php');
		require(sysConfig::get('DIR_FS_CATALOG') . 'includes/modules/' . $module_type . '/' . $className . '.php');
	}
	$classObj = new $className;
	$moduleInfo = array(
		'title'       => $classObj->title,
		'code'        => $classObj->code,
		'description' => $classObj->description,
		'installed'   => ($classObj->check() > 0),
		'configKeys'  => $classObj->keys(),
		'enabled'     => $classObj->enabled,
		'sort_order'  => ($classObj->check() > 0 ? $classObj->sort_order : '')
	);

	$Qconfig = Doctrine_Query::create()
	->select('configuration_key, configuration_title, configuration_value, configuration_description, use_function, set_function')
	->from('Configuration')
	->whereIn('configuration_key',  $moduleInfo['configKeys'])
	//->orderBy('sort_order')
	->execute();
	$keys_extra = array();
	if ($Qconfig->count() > 0){
		foreach($Qconfig->toArray() as $cInfo){
			$key = $cInfo['configuration_key'];
			$keys_extra[$key]['title'] = $cInfo['configuration_title'];
			$keys_extra[$key]['value'] = $cInfo['configuration_value'];
			$keys_extra[$key]['description'] = $cInfo['configuration_description'];
			$keys_extra[$key]['use_function'] = $cInfo['use_function'];
			$keys_extra[$key]['set_function'] = $cInfo['set_function'];
		}
	}
	$moduleInfo['keys'] = $keys_extra;
	
	foreach($moduleInfo['configKeys'] as $key){
		if (!isset($moduleInfo['keys'][$key])){
			$moduleInfo['missingKeys'] = true;
			break;
		}
	}
	
	reset($moduleInfo['keys']);
	$tableObj = htmlBase::newElement('table')->setCellPadding(5)->setCellSpacing(0);
	while (list($key, $value) = each($moduleInfo['keys'])){
		if ($value['set_function'] && $value['set_function'] != 'isArea') {
			eval('$inputField = ' . $value['set_function'] . "'" . $value['value'] . "', '" . $key . "');");
			if (substr($inputField, 0, 3) == '<br'){
				$inputField = substr($inputField, 4);
			}
		} else if ($value['set_function'] && $value['set_function'] == 'isArea') {
			$inputField = tep_draw_textarea_field('configuration[' . $key . ']','hard',30,5, $value['value'],'class="makeModFCK"');
		}else{
			$inputField = tep_draw_input_field('configuration[' . $key . ']', $value['value']);
		}
		$tableObj->addBodyRow(array(
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
	
	$headingTitle = htmlBase::newElement('div')
	->addClass('pageHeading')
	->html($moduleInfo['title']);

	$saveButton = htmlBase::newElement('button')->setType('submit')->usePreset('save');
	$cancelButton = htmlBase::newElement('button')->usePreset('cancel')
	->setHref(itw_app_link('set=' . $set . '&module=' . $_GET['module'], null, 'default'));

	$buttonContainer = new htmlElement('div');
	$buttonContainer->append($saveButton)->append($cancelButton)->css(array(
		'float' => 'right',
		'width' => 'auto'
	))->addClass('ui-widget');

	$multiStore = $appExtension->getExtension('multiStore');
	if ($multiStore !== false && $multiStore->isEnabled() === true){
		$multiStore->pagePlugin->loadTabs($tableObj, $moduleInfo);
	}

	$pageForm = htmlBase::newElement('form')
	->attr('name', 'modules')
	->attr('action', itw_app_link('set=' . $set . '&module=' . $_GET['module'] . '&action=save', null, 'default'))
	->attr('method', 'post')
	->html('<div style="position:relative;">' . $tableObj->draw() . '</div><br />' . $buttonContainer->draw());

	echo $headingTitle->draw() . '<br />' . $pageForm->draw();
?>