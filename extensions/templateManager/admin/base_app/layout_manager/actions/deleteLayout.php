<?php
$Layout = Doctrine_Core::getTable('TemplateManagerLayouts')->find((int)$_GET['lID']);
if ($Layout){
	$TemplatePages = Doctrine_Core::getTable('TemplatePages')->findAll();
	foreach($TemplatePages as $rInfo){
		$pageLayouts = explode(',', $rInfo->layout_id);
		if (in_array($Layout->layout_id, $pageLayouts)){
			foreach($pageLayouts as $idx => $id){
				if ($id == $Layout->layout_id){
					unset($pageLayouts[$idx]);
				}

				if ($id = ''){
					unset($pageLayouts[$idx]);
				}
			}
			$rInfo->layout_id = implode(',', $pageLayouts);
		}
	}
	$TemplatePages->save();
	$Layout->delete();
}

EventManager::attachActionResponse(array(
	'success' => true
), 'json');
