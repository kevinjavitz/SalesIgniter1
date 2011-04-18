<?php
	$boxId = $_GET['box_id'];
	$templateName = $_GET['template'];

	$TemplatesInfoboxes = Doctrine_Core::getTable('TemplatesInfoboxes')->find($boxId);

	$TemplatesInfoboxes->TemplatesInfoboxesToTemplates[$templateName]->template_file = $_POST['template_file'];
/*
	$TemplatesInfoboxes->box_path = $boxPath;
	$TemplatesInfoboxes->box_filename = $box . '.php';
	$TemplatesInfoboxes->sort_order = $Qmax[0]['sort_order']+1;
	$TemplatesInfoboxes->template_column = $column;
	$TemplatesInfoboxes->template_name = $template;

	$languages = tep_get_languages();
	for($i=0, $n=sizeof($languages); $i<$n; $i++){
		$TemplatesInfoboxes->TemplatesInfoboxesDescription[$languages[$i]['id']]->infobox_heading = addslashes($_POST['infobox_heading'][$languages[$i]['id']]);
	}
*/
	$TemplatesInfoboxes->save();

	$dirObj = new DirectoryIterator(sysConfig::getDirFsCatalog() . $TemplatesInfoboxes->box_path);

	while($dirObj->valid()){
		if ($dirObj->isDot() || $dirObj->isFile()){
			$dirObj->next();
			continue;
		}
		if (file_exists($dirObj->getPathname()  .'/'. basename(__FILE__))){
			$className = 'InfoBox' . ucfirst($TemplatesInfoboxes->box_code);
			$lastDir = strrpos($dirObj->getPathname(),'/');
			$dirName = substr($dirObj->getPathname(), 0, $lastDir + 1);

			if (!class_exists($className)){
				require($dirName  . 'infobox.php');
			}
			$classObj = new $className();
			require($dirName . 'actions/' . basename(__FILE__));
		}
		$dirObj->next();
	}

 	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');

?>