<?php
	$Module = OrderPaymentModules::getModule($_POST['method']);
	$mInfo = $Module->onSelect();
	
	$onePageCheckout->onePage['info']['payment'] = array(
		'id'    => $Module->getCode(),
		'title' => $Module->getTitle()
	);

	$inputFields = '';
	$fieldRows = array();
	if ($mInfo !== false){
		if (isset($mInfo['fields'])){
			foreach($mInfo['fields'] as $fInfo){
				$fieldRows[] = array(
					$fInfo['title'],
					$fInfo['field']
				);
			}
		}
	}

	EventManager::attachActionResponse(array(
		'success' => true,
		'inputFields' => $fieldRows
	), 'json');
?>