<?php
	$designer = $appExtension->getExtension('productDesigner');
	
	$dimensions = $designer->getDesignerDimensions(array(
		'source' => $_POST['img'],
		'zoom'   => $_POST['zoom']
	));
		
	EventManager::attachActionResponse(array(
		'success'        => true,
		'imgWidth'       => $dimensions['image']['zoom']['width']['px'],
		'imgHeight'      => $dimensions['image']['zoom']['height']['px'],
		'editableWidth'  => (int)$dimensions['editable']['zoom']['width']['px'],
		'editableHeight' => (int)$dimensions['editable']['zoom']['height']['px'],
		'editableX'      => $dimensions['editable']['pos']['x'],
		'editableY'      => $dimensions['editable']['pos']['y'],
		'scale'          => $dimensions['scale']
	), 'json');
?>