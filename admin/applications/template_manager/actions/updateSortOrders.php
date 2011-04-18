<?php
	$template = $_POST['template'];
	$column = ($_POST['column'] == 'rightColumn' ? 'right' : 'left');
	$boxes = (isset($_POST[$column . 'ColumnBox']) ? $_POST[$column . 'ColumnBox'] : array());
	
	if (!empty($boxes)){
		foreach($boxes as $sortOrder => $boxCode){
			$QboxId = Doctrine_Query::create()
			->select('box_id')
			->from('TemplatesInfoboxes')
			->where('box_code = ?', $boxCode)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			
			Doctrine_Query::create()
			->update('TemplatesInfoboxesToTemplates')
			->set('sort_order', '?', $sortOrder+1)
			->where('template_name = ?', $template)
			->andWhere('template_column = ?', $column)
			->andWhere('box_id = ?', $QboxId[0]['box_id'])
			->execute();
		}
	}
	
	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>