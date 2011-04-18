<?php
	$fieldId = substr($_GET['field_id'], strpos($_GET['field_id'], '_')+1);

	Doctrine_Query::create()
	->delete('ProductsCustomFieldsToGroups')
	->where('field_id = ?', $fieldId)
	->execute();

	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>