<?php
function LoadAllWidgetData(&$Data, &$New){
	$New->identifier = $Data->identifier;
	$New->sort_order = $Data->sort_order;
	foreach($Data->Configuration as $Config){
		$New->Configuration[$Config->configuration_key]->configuration_value = $Config->configuration_value;
	}
	foreach($Data->Styles as $Style){
		$New->Styles[$Style->definition_key]->definition_value = $Style->definition_value;
	}
}
function LoadAllColumnData(&$Data, &$New){
	$New->sort_order = $Data->sort_order;
	foreach($Data->Configuration as $Config){
		$New->Configuration[$Config->configuration_key]->configuration_value  = $Config->configuration_value;
	}
	foreach($Data->Styles as $Style){
		$New->Styles[$Style->definition_key]->definition_value = $Style->definition_value;
	}
	if ($Data->Widgets && $Data->Widgets->count() > 0){
		foreach($Data->Widgets as $wInfo){
			$newWidget = $New->Widgets->getTable()->create();
			LoadAllWidgetData($wInfo, $newWidget);
			$New->Widgets->add($newWidget);
		}
	}
}
function LoadAllContainerData(&$Data, &$New){
	$New->sort_order = $Data->sort_order;
	foreach($Data->Configuration as $Config){
		$New->Configuration[$Config->configuration_key]->configuration_value  = $Config->configuration_value;
	}
	foreach($Data->Styles as $Style){
		$New->Styles[$Style->definition_key]->definition_value = $Style->definition_value;
	}

	if ($Data->Children && $Data->Children->count() > 0){
		foreach($Data->Children as $Container){
			$NewContainer = $New->Children->getTable()->create();
			LoadAllContainerData($Container, $NewContainer);
			$New->Children->add($NewContainer);
		}
	}else
	if ($Data->Columns && $Data->Columns->count() > 0){
		foreach($Data->Columns as $col){
			$NewColumn = $New->Columns->getTable()->create();
			LoadAllColumnData($col, $NewColumn);
			$New->Columns->add($NewColumn);
		}
	}
}
function LoadAllData(&$Original, &$New){
	foreach($Original->Containers as $Container){
		$NewContainer = $New->Containers->getTable()->create();
		LoadAllContainerData($Container, $NewContainer);
		$New->Containers->add($NewContainer);
	}
}

$TemplateLayouts = Doctrine_Core::getTable('PDFTemplateManagerLayouts');

$Original = $TemplateLayouts->find((int) $_GET['lID']);

$New = $TemplateLayouts->create();
$New->template_id = $Original->template_id;
$New->layout_name = $_POST['layout_name'];

foreach($Original->Configuration as $Config){
	$New->Configuration[$Config->configuration_key]->configuration_value = $Config->configuration_value;
}

foreach($Original->Styles as $Style){
	$New->Styles[$Style->definition_key]->definition_value = $Style->definition_value;
}

LoadAllData($Original, $New);

//$New->synchronizeWithArray($Original[0]);
//print_r($Original->toArray(true));exit;
$New->save();

EventManager::attachActionResponse(array(
	'success' => true,
	'layoutId' => $New->layout_id,
	'layoutName' => $New->layout_name
), 'json');
