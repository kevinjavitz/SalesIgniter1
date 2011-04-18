<?php
	$optionId = $_POST['option'];
	$html = '';
	if ($optionId > 0){
		$Extension = $appExtension->getExtension('attributes');
		$OptionContainer = $Extension->pagePlugin->getOptionTable();
		$html = $OptionContainer->draw();
	}
	
	EventManager::attachActionResponse($html, 'html');
?>