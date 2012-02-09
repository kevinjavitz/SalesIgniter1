<?php

class Zipper extends ZipArchive {
		public $basePath = '';

		public function setBasePath($path){
			$this->basePath = $path;
		}
	}

	function cleanString($string){
		$string = str_replace("'", "\'", $string);
		$string = str_replace("\n", '\' . "\n" . \'', $string);
		$string = str_replace('<?php', "<' . '?php", $string);
		$string = str_replace('?>', "?' . '>", $string);
		
		return $string;
	}

function sprintfStyles($pattern, $Styles){
	$return = '';
	foreach($Styles as $Style){
		$return .= sprintf($pattern, $Style->definition_key, cleanString($Style->definition_value)) . "\n";
	}
	return $return;
}

function sprintfConfig($pattern, $Configuration){
	$return = '';
	foreach($Configuration as $Config){
		$return .= sprintf($pattern, $Config->configuration_key, cleanString($Config->configuration_value)) . "\n";
	}
	return $return;
}

function exportElement($exportVar, $addItemVar, $exportTable, $Element, $elementId){
	global $TemplateDir, $WidgetUpdates;
	$return = "\n" . $exportVar . '[' . $elementId . '] = ' . $addItemVar . '->' . $exportTable . '->getTable()->create();' . "\n";
	$return .= $addItemVar . '->' . $exportTable . '->add(' . $exportVar . '[' . $elementId . ']);' . "\n";
	if ($exportTable == 'Widgets'){
		$return .= $exportVar . '[' . $elementId . ']->identifier = \'' . $Element->identifier . '\';' . "\n";
	}
	$return .= $exportVar . '[' . $elementId . ']->sort_order = \'' . $Element->sort_order . '\';' . "\n";
	$return .= sprintfStyles($exportVar . '[' . $elementId . ']->Styles[\'%s\']->definition_value = \'%s\';', $Element->Styles);
	$return .= sprintfConfig($exportVar . '[' . $elementId . ']->Configuration[\'%s\']->configuration_value = \'%s\';', $Element->Configuration);

	if ($Element->hasRelation('Children') && $Element->Children->count() > 0){
		foreach($Element->Children as $Child){
			$return .= exportElement(
				'$Child',
				$exportVar . '[' . $elementId . ']',
				'Children',
				$Child,
				$Child->container_id
			);
		}
	}elseif ($Element->hasRelation('Columns') && $Element->Columns->count() > 0){
		foreach($Element->Columns as $Column){
			$return .= exportElement(
				'$Column',
				$exportVar . '[' . $elementId . ']',
				'Columns',
				$Column,
				$Column->column_id
			);
		}
	}elseif ($Element->hasRelation('Widgets') && $Element->Widgets->count() > 0){
		foreach($Element->Widgets as $Widget){
			$boxCode = '\'' . $Widget->PDFTemplatesInfoboxes->box_code . '\'';
			$boxPath = '\'' . $Widget->PDFTemplatesInfoboxes->box_path . '\'';
			$boxExt = '\'' . (is_null($Widget->PDFTemplatesInfoboxes->ext_name) === false ? $Widget->PDFTemplatesInfoboxes->ext_name : 'null') . '\'';

			$return .= "\n" . 'if (!isset($Box[' . $boxCode . '])){' . "\n";
			$return .= ' $Box[' . $boxCode . '] = $PDFTemplatesInfoboxes->findOneByBoxCode(' . $boxCode . ');' . "\n";
			$return .= '    if (!is_object($Box[' . $boxCode . ']) || $Box[' . $boxCode . ']->count() <= 0){' . "\n" .
				'       installInfobox(' . $boxPath . ', ' . $boxCode . ', ' . $boxExt . ');' . "\n" .
				'       $Box[' . $boxCode . '] = $PDFTemplatesInfoboxes->findOneByBoxCode(' . $boxCode . ');' . "\n" .
				'   }' . "\n";
			$return .= '}' . "\n";

			$return .= exportElement(
				'$Widget',
				$exportVar . '[' . $elementId . ']',
				'Widgets',
				$Widget,
				$Widget->widget_id
			);

			if (file_exists(sysConfig::getDirFsCatalog() . $Widget->PDFTemplatesInfoboxes->box_path . 'actions/exportLayouts.php')){
				$WidgetUpdates .= '$WidgetProperties = json_decode($Widget[' . $Widget->widget_id . ']->Configuration[\'widget_settings\']->configuration_value);' . "\n";
				ob_start();
				require(sysConfig::getDirFsCatalog() . $Widget->PDFTemplatesInfoboxes->box_path . 'actions/exportLayouts.php');
				$WidgetUpdates .= ob_get_contents();
				ob_end_clean();
				$WidgetUpdates .= '$Widget[' . $Widget->widget_id . ']->Configuration[\'widget_settings\']->configuration_value = json_encode($WidgetProperties);' . "\n";
				$WidgetUpdates .= '$Widget[' . $Widget->widget_id . ']->save();' . "\n";
			}
		}
	}
	return $return;
}


	ob_start();
	$Layout = Doctrine_Core::getTable('PDFTemplateManagerLayouts')->find($_GET['lID']);
	$TemplateDir = sysConfig::get('DIR_WS_TEMPLATES_DEFAULT');
	$WidgetUpdates = '';
	echo '<?php' . "\n";
	echo '$Layout = Doctrine_Core::getTable(\'PDFTemplateManagerLayouts\')->create();' . "\n";
	//echo sprintfStyles('$Template->Styles[\'%s\']->definition_value = \'%s\';', $Template->Styles);
	//echo sprintfConfig('$Template->Configuration[\'%s\']->configuration_value = \'%s\';', $Template->Configuration);

	//$layoutIds = array();
	//foreach($Template->Layouts as $Layout){
		//$layoutKey = $Layout->layout_id;
		//$layoutIds[] = $layoutKey;
		//echo "\n" . '$Layout[' . $layoutKey . '] = $Layout->getTable()->create();' . "\n";
		//echo '$Template->Layouts->add($Layout[' . $layoutKey . ']);' . "\n";
		echo '$Layout->layout_name = \'' . $Layout->layout_name . '\';' . "\n";
		echo sprintfStyles('$Layout->Styles[\'%s\']->definition_value = \'%s\';', $Layout->Styles);
		echo sprintfConfig('$Layout->Configuration[\'%s\']->configuration_value = \'%s\';', $Layout->Configuration);

		foreach($Layout->Containers as $Container){
			if ($Container->parent_id > 0) continue;

			echo exportElement(
				'$Container',
				'$Layout[' . $layoutKey . ']',
				'Containers',
				$Container,
				$Container->container_id
			);
		}
	//}
	echo '$Layout->save();' . "\n";
	echo $WidgetUpdates;

	$installFileContent = ob_get_contents();
	ob_end_clean();

	if (file_exists(sysConfig::getDirFsCatalog() . 'templates/' . $TemplateDir . '/exportPDFLayout'.$_GET['lID'].'.zip')){
		unlink(sysConfig::getDirFsCatalog() . 'templates/' . $TemplateDir . '/exportPDFLayout'.$_GET['lID'].'.zip');
	}
	
	$ZipArchive = new Zipper();
	$ZipArchive->open(sysConfig::getDirFsCatalog() . 'templates/' . $TemplateDir . '/exportPDFLayout.zip', ZipArchive::OVERWRITE);
	$ZipArchive->setBasePath(sysConfig::getDirFsCatalog() . 'templates/' . $TemplateDir . '/');
	$ZipArchive->addFromString('installData.php', $installFileContent);
	$ZipArchive->close();
	
	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
	