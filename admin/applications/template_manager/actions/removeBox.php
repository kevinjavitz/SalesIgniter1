<?php
	$template = $_GET['template'];
	$data = explode('_', $_GET['box']);
	$column = ($data[0] == 'rightColumnBox' ? 'right' : 'left');
	$box = $data[1];
	
	$QboxId = Doctrine_Query::create()
	->select('box_id')
	->from('TemplatesInfoboxes')
	->where('box_code = ?', $box)
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	
	Doctrine_Query::create()
	->delete('TemplatesInfoboxesToTemplates')
	->where('template_name = ?', $template)
	->andWhere('template_column = ?', $column)
	->andWhere('box_id = ?', $QboxId[0]['box_id'])
	->execute();
	
	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>