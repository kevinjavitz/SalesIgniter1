<?php

//if (isset($_POST['selectedOrder']) && is_array($_POST['selectedOrder'])){
    require(sysConfig::getDirFsCatalog(). 'includes/classes/currencies.php');
	$currencies = new currencies();
    if (isset($_POST['start_dates'])){
	 	$start_date = $_POST['start_dates'];
	 }

	 if (isset($_POST['end_dates'])){
	 	$end_date = $_POST['end_dates'];
	 }

    $dataExport = new dataExport();

	$fields = array();

	$fields[] = 'v_customer_group_name';

	$fields[] = 'v_customer_group_total';


	if (sizeof($fields) > 0) {
		$dataExport->setHeaders($fields);
	}
	EventManager::notify('CustomerGroupsExportQueryFileLayoutHeader', &$dataExport);


	$QfileLayout = Doctrine_Query::create()
	->select('
			o.*,
			SUM(ot.value) as tot,
			cgt.*,
			cg.*,
			c.*
		')
	->from('Orders o')
	->leftJoin('o.Customers c')
	->leftJoin('c.CustomersToCustomerGroups cgt')
	->leftJoin('cgt.CustomerGroups cg')
	->leftJoin('o.OrdersTotal ot')
	->andWhereIn('ot.module_type', array('total', 'ot_total'))
	->groupBy('cg.customer_groups_id');

	if (isset($start_date) && !empty($start_date)){
		$QfileLayout->andWhere('o.date_purchased >= ?', $start_date);
	}

	if (isset($end_date) && !empty($end_date)){
		$QfileLayout->andWhere('o.date_purchased <= ?', date('Y-m-d',strtotime('+1 day',strtotime($end_date))));
	}
		EventManager::notify('CustomerGroupsExportQueryBeforeExecute', &$QfileLayout);

		$Result = $QfileLayout->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		$dataRows = array();
		foreach($Result as $oInfo){

			$pInfo['v_customer_group_name'] =  $currencies->format($oInfo['tot']);
			$pInfo['v_customer_group_total'] =  $oInfo['Customers']['CustomersToCustomerGroups'][0]['CustomerGroups']['customer_groups_name'];
			EventManager::notify('CustomerGroupsExportBeforeFileLineCommit', &$pInfo, &$oInfo);

			$dataRows[] = $pInfo;
		}

		$dataExport->setExportData($dataRows);
		$dataExport->output(true);

//}
?>