<?php
//, o.is_rental
$Qbillings = Doctrine_Query::create()
	->select('c.customers_id, o.orders_id, o.date_purchased, o.last_modified, o.currency, o.currency_value, cm.plan_name, ot.text as order_total')
	->from('Orders o')
	->leftJoin('o.OrdersTotal ot')
	->leftJoin('o.Customers c')
	->leftJoin('c.CustomersMembership cm')
	->where('o.customers_id = ?', $cID)
	//->andWhere('o.is_rental = ?', '1')
	->andWhereIn('ot.module_type', array('total', 'ot_total'))
	->orderBy('o.date_purchased desc')
	->execute();
$templateParsed = array();
if ($Qbillings->count() <= 0){
	$templateParsed[] = '<tr>
       <td colspan="4" class="messageStackError">' . sysLanguage::get('TEXT_INFO_NO_BILLING_HISTORY') . '</td>
      </tr>';
}
else {
	$template = '<tr class="dataTableRow">
       <td class="smallText" align="left">%s</td>
       <td class="smallText" align="left">%s</td>
       <td class="smallText" align="left">%s</td>
       <td class="smallText" align="left">%s</td>
      </tr>';
	foreach($Qbillings as $Billing){
		$templateParsed[] = sprintf($template,
			$Billing->orders_id,
			date('Y-m-d', strtotime($Billing->date_purchased)),
			$Billing->Customers->CustomersMembership->plan_name,
			strip_tags($Billing->order_total)
		);
	}
}
?>
<table border="0" width="95%" cellspacing="0" cellpadding="2">
	<tr class="dataTableHeadingRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)">
		<td class="dataTableHeadingContent"><?php echo sysLanguage::get('TABLE_HEADING_ORDER_ID');?></td>
		<td class="dataTableHeadingContent"><?php echo sysLanguage::get('TABLE_HEADING_BILLED_DATE');?></td>
		<td class="dataTableHeadingContent"><?php echo sysLanguage::get('TABLE_HEADING_PACKAGE_NAME');?></td>
		<td class="dataTableHeadingContent"><?php echo sysLanguage::get('TABLE_HEADING_AMOUNT');?></td>
	</tr>
	<?php echo implode("\n", $templateParsed);?>
</table>