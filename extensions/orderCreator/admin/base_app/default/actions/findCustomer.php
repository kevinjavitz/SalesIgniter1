<?php
	$jsonData = array();

$QcustomerName = Doctrine_Query::create()
	->from('Customers c')
	->where('(' .
		'c.customers_firstname LIKE "' . $_GET['term'] . '%"' .
		' OR ' .
		'c.customers_lastname LIKE "' . $_GET['term'] . '%"' .
		' OR ' .
		'c.customers_email_address LIKE "' . $_GET['term'] . '%"' .
		' OR ' .
		'c.customers_number LIKE "' . $_GET['term'] . '%"' .
		' OR ' .
		'c.customers_telephone LIKE "' . $_GET['term'] . '%"' .
		') AND TRUE');

if(sysConfig::get('SEARCH_CUSTOMERS_PER_STORE') == 'True')
    EventManager::notify('OrderCreatorFindCustomerQueryBeforeExecute', $QcustomerName);

$Result = $QcustomerName->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
if ($Result){
	$jsonData[] = array(
		'value' => 'no-select',
		'label' => '<span style="display:inline-block;width:20%;font-weight:bold;">Member Number</span>' .
			'<span style="display:inline-block;width:20%;font-weight:bold;">Telephone Number</span>' .
			'<span style="display:inline-block;width:20%;font-weight:bold;">First Name</span>' .
			'<span style="display:inline-block;width:20%;font-weight:bold;">Last Name</span>' .
			'<span style="display:inline-block;width:20%;font-weight:bold;">Email Address</span>'
	);
	foreach($Result as $cInfo){
		$msg = '';
		if ($cInfo['customers_account_frozen'] == '1'){
			$value = 'disabled';
			$msg = 'This customers account is frozen.';
		}
		$value = $cInfo['customers_id'];
		$jsonData[] = array(
			'value' => $value,
			'reason' => $msg,
			'label' => '<span class="' . ($value == 'disabled' ? 'ui-state-disabled' : '') . '" style="display:inline-block;width:20%;">' . $cInfo['customers_number'] . '</span>' .
				'<span class="' . ($value == 'disabled' ? 'ui-state-disabled' : '') . '" style="display:inline-block;width:20%;">' . $cInfo['customers_telephone'] . '</span>' .
				'<span class="' . ($value == 'disabled' ? 'ui-state-disabled' : '') . '" style="display:inline-block;width:20%;">' . $cInfo['customers_firstname'] . '</span>' .
				'<span class="' . ($value == 'disabled' ? 'ui-state-disabled' : '') . '" style="display:inline-block;width:20%;">' . $cInfo['customers_lastname'] . '</span>' .
				'<span class="' . ($value == 'disabled' ? 'ui-state-disabled' : '') . '" style="display:inline-block;width:20%;">' . $cInfo['customers_email_address'] . '</span>'
		);
	}
}

EventManager::attachActionResponse($jsonData, 'json');
?>
