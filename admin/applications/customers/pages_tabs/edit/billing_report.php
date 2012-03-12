<?php
	/*
	 * Build the billing report table -- BEGIN
	 */
	$Qreports = Doctrine_Query::create()
	->select('c.customers_id, cm.payment_method, concat(c.customers_firstname, " ",c.customers_lastname) as customers_name, c.customers_email_address, mbr.status, mbr.error, mbr.date as trans_date, mbr.billing_report_id')
	->from('MembershipBillingReport mbr')
	->leftJoin('mbr.Customers c')
	->leftJoin('c.CustomersMembership cm')
	->where('c.customers_id = '.$Customer->customers_id)
	->orderBy('c.customers_id');

	$reportGrid = htmlBase::newElement('newGrid')
	->setQuery($Qreports);

	$reportGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_ID')),
			array('text' => sysLanguage::get('TABLE_HEADING_TRANS_DATE')),
			array('text' => sysLanguage::get('TABLE_HEADING_PAYMENT_METHOD')),
			array('text' => sysLanguage::get('TABLE_HEADING_TRANS_STATUS')),
			array('text' => sysLanguage::get('TABLE_HEADING_TRANS_ERROR'))
		)
	));
	
	$billingReports = &$reportGrid->getResults();
	if ($billingReports){
		foreach($billingReports as $rInfo){
			switch($rInfo['status']){
				case 'M': $transStatus = 'Manual'; break;
				case 'A': $transStatus = 'Approved'; break;
				case 'D': $transStatus = 'Declined'; break;
				case 'F': $transStatus = 'Free Trial'; break;
				default: $transStatus = 'Unknown'; break;
			}
			
			$reportGrid->addBodyRow(array(
				'columns' => array(
					array('text' => $rInfo['Customers']['customers_id']),
					array('align' => 'center', 'text' => $rInfo['trans_date']),
					array('align' => 'center', 'text' => $rInfo['Customers']['CustomersMembership']['payment_method']),
					array('align' => 'center', 'text' => $transStatus),
					array('align' => 'center', 'text' => $rInfo['error'])
				)
			));
		}
	}
	/*
	 * Build the billing report table -- END
	 */
?>
<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE_REPORTS');
?></div>
<br />
<div class="gridContainer">
	<div style="width:100%;">
		<div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
			<div style="width:99%;margin:5px;"><?php echo $reportGrid->draw();?></div>
		</div>
	</div>
</div>