<?php
	$leftTemplateBoxes = Doctrine_Query::create()
	->from('TemplatesInfoboxes i')
	->leftJoin('i.TemplatesInfoboxesToTemplates i2t')
	->where('i2t.template_column = ?', 'left')
	->andWhere('i2t.template_name = ?', $_GET['template'])
	->orderBy('i2t.sort_order')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	$leftColumnBoxes = array();
	if ($leftTemplateBoxes){
		foreach($leftTemplateBoxes as $boxInfo){
			$className = 'InfoBox' . ucfirst($boxInfo['box_code']);
			if (!class_exists($className)){
				require(sysConfig::getDirFsCatalog() . $boxInfo['box_path'] . 'infobox.php');
			}
			$classObj = new $className();
			
			$leftColumnBoxes[] = array(
				$classObj->getBoxCode(),
				$classObj->getExtName()
			);
		}
	}
	
	$rightTemplateBoxes = Doctrine_Query::create()
	->from('TemplatesInfoboxes i')
	->leftJoin('i.TemplatesInfoboxesToTemplates i2t')
	->where('i2t.template_column = ?', 'right')
	->andWhere('i2t.template_name = ?', $_GET['template'])
	->orderBy('i2t.sort_order')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	$rightColumnBoxes = array();
	if ($rightTemplateBoxes){
		foreach($rightTemplateBoxes as $boxInfo){
			$className = 'InfoBox' . ucfirst($boxInfo['box_code']);
			if (!class_exists($className)){
				require(sysConfig::getDirFsCatalog() . $boxInfo['box_path'] . 'infobox.php');
			}
			$classObj = new $className();

			$rightColumnBoxes[] = array(
				$classObj->getBoxCode(),
				$classObj->getExtName()
			);
		}
	}
	
	EventManager::attachActionResponse(array(
		'success'     => true,
		'leftColumn'  => $leftColumnBoxes,
		'rightColumn' => $rightColumnBoxes
	), 'json');
?>