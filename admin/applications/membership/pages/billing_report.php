<?php
	$Qreports = Doctrine_Query::create()
	->select('c.customers_id, cm.payment_method, concat(c.customers_firstname, " ",c.customers_lastname) as customers_name, c.customers_email_address, mbr.status, mbr.error, mbr.date as trans_date, mbr.billing_report_id')
	->from('MembershipBillingReport mbr')
	->leftJoin('mbr.Customers c')
	->leftJoin('c.CustomersMembership cm')
	->orderBy('c.customers_id');

	$tableGrid = htmlBase::newElement('newGrid')
	->setQuery($Qreports);
	
	$tableGrid->addButtons(array(
		htmlBase::newElement('button')->setText('Delete')->addClass('deleteButton')->disable()
	));

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => '&nbsp;'),
			array('text' => sysLanguage::get('TABLE_HEADING_ID')),
			array('text' => sysLanguage::get('TABLE_HEADING_CUSTOMER')),
			array('text' => sysLanguage::get('TABLE_HEADING_CUSTOMER_EMAIL')),
			array('text' => sysLanguage::get('TABLE_HEADING_TRANS_DATE')),
			array('text' => sysLanguage::get('TABLE_HEADING_PAYMENT_METHOD')),
			array('text' => sysLanguage::get('TABLE_HEADING_TRANS_STATUS')),
			array('text' => sysLanguage::get('TABLE_HEADING_TRANS_ERROR'))
		)
	));
	
	$Reports = &$tableGrid->getResults();
	if ($Reports){
		foreach($Reports as $rInfo){
			switch($rInfo['status']){
				case 'M': $transStatus = 'Manual'; break;
				case 'A': $transStatus = 'Approved'; break;
				case 'D': $transStatus = 'Declined'; break;
				case 'F': $transStatus = 'Free Trial'; break;
				default: $transStatus = 'Unknown'; break;
			}
			
			$tableGrid->addBodyRow(array(
				'columns' => array(
					array('text' => '<input type="checkbox" name="report[]" value="' . $rInfo['billing_report_id'] . '">'),
					array('text' => $rInfo['Customers']['customers_id']),
					array('text' => $rInfo['customers_name']),
					array('text' => $rInfo['Customers']['customers_email_address']),
					array('align' => 'center', 'text' => $rInfo['trans_date']),
					array('align' => 'center', 'text' => $rInfo['Customers']['CustomersMembership']['payment_method']),
					array('align' => 'center', 'text' => $transStatus),
					array('align' => 'center', 'text' => $rInfo['error'])
				)
			));
		}
	}
?>
<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE_REPORTS');
?></div>
<br />
<form name="actions" action="<?php echo itw_app_link('action=deleteReports', 'membership', 'billing_report');?>" method="post">
<div class="gridContainer">
	<div style="width:100%;float:left;">
		<div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
			<div style="width:99%;margin:5px;"><?php echo $tableGrid->draw();?></div>
		</div>
	</div>
</div>
</form>