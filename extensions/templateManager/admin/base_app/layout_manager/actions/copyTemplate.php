<?php

$TemplateName = $_POST['templateName'];
$TemplateDirectory = $_POST['templateDirectory'];
$tID = $_GET['tID'];
$Template = Doctrine_Core::getTable('TemplateManagerTemplates')->find($tID);
$TemplateToCopy = $Template->Configuration['DIRECTORY']->configuration_value;

if ($TemplateDirectory == 'newDir'){
	$TemplateDirectory = $_POST['templateCopyDirectory'];
	$Ftp = new SystemFTP();
	$Ftp->connect();
	$Ftp->checkPath('templates/' . $TemplateDirectory);

	$templateDirRel = 'templates/' . $TemplateDirectory . '/';
	$copyTemplateDir = sysConfig::getDirFsCatalog() . 'templates/'.$TemplateToCopy.'/';

	$Dir = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator($copyTemplateDir),
		RecursiveIteratorIterator::SELF_FIRST
	);
	foreach($Dir as $dInfo){
		if (!$dInfo->isFile()){
			continue;
		}

		$path = str_replace($copyTemplateDir, '', $dInfo->getPathname());
		$Ftp->copyFile(
			$dInfo->getPathname(),
			$templateDirRel . $path
		);
	}
}

$Template = Doctrine_Core::getTable('TemplateManagerTemplates')->create();
$Template->Configuration['NAME']->configuration_value = $TemplateName;
$Template->Configuration['DIRECTORY']->configuration_value = $TemplateDirectory;
$Template->save();
$ntID = $Template->template_id;

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

function saveConfigurationForLayout($olID, $nlID){
	$TemplatePages = Doctrine_Query::create()
	->from('TemplatePages')
	->where('FIND_IN_SET(' . $olID . ', layout_id) > 0')
	->execute();
	foreach($TemplatePages as $iTemplatePage){
		$iTemplatePage->layout_id = $iTemplatePage->layout_id.','.$nlID;
		$iTemplatePage->save();
	}
}



$TemplateLayouts = Doctrine_Core::getTable('TemplateManagerLayouts');


 //foreach layout
$QLayout = Doctrine_Query::create()

	->select('layout_id, layout_name')

	->from('TemplateManagerLayouts')

	->where('template_id=?', $tID)

	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

foreach($QLayout as $iLayout){
	$Original = $TemplateLayouts->find((int) $iLayout['layout_id']);

	$New = $TemplateLayouts->create();

	$New->template_id = $ntID;

	$New->layout_name = $iLayout['layout_name'];



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

	saveConfigurationForLayout($iLayout['layout_id'], $New->layout_id);
}


$json = array(
	'success' => true
);

EventManager::attachActionResponse($json, 'json');
?>