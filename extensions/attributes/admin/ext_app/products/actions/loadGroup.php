<?php
	$groupId = $_POST['option_group'];
	$html = '';
	if ($groupId > 0){
		$Extension = $appExtension->getExtension('attributes');
		$OptionContainer = $Extension->pagePlugin->getGroupTable();
		$html = $OptionContainer->draw();
	}
	
	EventManager::attachActionResponse($html, 'html');
?>