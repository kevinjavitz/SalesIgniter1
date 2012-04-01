<?php
	if (isset($WidgetProperties->linked_to)){
		$WidgetProperties->linked_to = $Widget[$WidgetProperties->linked_to]->widget_id;
	}else{
		foreach($WidgetProperties->menuSettings as $k => $mInfo){
			if ($mInfo->icon == 'custom'){
				$WidgetProperties->menuSettings->$k->icon_src = str_replace('<?php echo $TemplateDir;?>', $Template->Configuration['DIRECTORY']->configuration_value, $mInfo->icon_src);
			}
		}
	}