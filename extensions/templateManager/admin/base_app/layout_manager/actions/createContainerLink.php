<?php
$ContainerLink = new TemplateManagerContainerLinks();
$ContainerLink->container_id = $_POST['cID'];
$ContainerLink->link_name = $_POST['link_name'];
$ContainerLink->save();

EventManager::attachActionResponse(array(
	'success' => true
), 'json');