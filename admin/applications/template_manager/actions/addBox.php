<?php
	$template = $_POST['template'];
	$column = ($_POST['column'] == 'rightColumn' ? 'right' : 'left');
	$box = $_POST['box'];
	
	$Qbox = Doctrine_Query::create()
	->select('box_id, ext_name')
	->from('TemplatesInfoboxes')
	->where('box_code = ?', $box)
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	
	$Qmax = Doctrine_Query::create()
	->select('sort_order')
	->from('TemplatesInfoboxesToTemplates')
	->where('template_column = ?', $column)
	->andWhere('template_name = ?', $template)
	->orderBy('sort_order desc')
	->limit(1)
	->execute();
	
	$TemplatesInfoboxes = new TemplatesInfoboxesToTemplates;
	$TemplatesInfoboxes->box_id = $Qbox[0]['box_id'];
	$TemplatesInfoboxes->template_column = $column;
	$TemplatesInfoboxes->template_name = $template;
	$TemplatesInfoboxes->sort_order = $Qmax[0]['sort_order']+1;
	$TemplatesInfoboxes->save();
	
	$json = array(
		'success' => true,
		'boxId'   => $_POST['column'] . 'Box_' . $box,
		'boxName' => $box
	);
	
	if (!empty($Qbox[0]['ext_name'])){
		$json['extName'] = $Qbox[0]['ext_name'];
	}
	
	EventManager::attachActionResponse($json, 'json');
?>