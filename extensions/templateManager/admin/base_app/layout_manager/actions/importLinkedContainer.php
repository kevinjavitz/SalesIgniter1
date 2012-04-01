<?php
$LinkedContainer = Doctrine_Core::getTable('TemplateManagerContainerLinks')
	->find((int) $_POST['lID']);
$return = 'false';
if ($LinkedContainer){
	$MainContainer = $LinkedContainer->Container;

	$MainEl = htmlBase::newElement('div')
		->attr('data-container_id', 0)
		->attr('data-sort_order', 0)
		->attr('data-link_id', $LinkedContainer->link_id)
		->addClass('container');

	if ($MainContainer->Styles->count() > 0){
		addStyles($MainEl, $MainContainer->Styles);
	}

	if ($MainContainer->Configuration->count() > 0){
		addInputs($MainEl, $MainContainer->Configuration);
	}

	processContainerColumns($MainEl, $MainContainer->Columns);
	if ($MainContainer->Children->count() > 0){
		processContainerChildren($MainContainer, $MainEl);
	}
	$return = $MainEl->draw();
}

EventManager::attachActionResponse($return, 'html');