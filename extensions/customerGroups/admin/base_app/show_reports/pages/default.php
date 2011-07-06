 <div class="pageHeading"><?php echo 'Customer Groups Reports';?></div>
 <br />
	 <div>

	 <?php
	 require(sysConfig::getDirFsCatalog(). 'includes/classes/currencies.php');
	 $currencies = new currencies();
	 $searchForm = htmlBase::newElement('form')
	 ->attr('name', 'search')
	 ->attr('action', itw_app_link(tep_get_all_get_params()))
	 ->attr('method', 'get');

	 $startdateField = htmlBase::newElement('input')->setName('start_date')
	 ->setLabel(sysLanguage::get('HEADING_TITLE_START_DATE'))->setLabelPosition('before')->setId('start_date');

	 $enddateField = htmlBase::newElement('input')->setName('end_date')
	 ->setLabel(sysLanguage::get('HEADING_TITLE_START_END'))->setLabelPosition('before')->setId('end_date');

	 $gobut = htmlBase::newElement('button')
	 ->setType('submit')
	 ->setText('Submit');

	 if (isset($_GET['start_date'])){
	 	$startdateField->setValue($_GET['start_date']);
	 }

	 if (isset($_GET['end_date'])){
	 	$enddateField->setValue($_GET['end_date']);
	 }

	 $searchForm->append($startdateField);
	 $searchForm->append($enddateField);
	 $searchForm->append($gobut);
	 echo $searchForm->draw();

?>
	 </div>

<div class="ui-widget ui-widget-content ui-corner-all pageButtonBar">
<form action="<?php echo itw_app_link('appExt=customerGroups&action=exportOrders','show_reports','default');?>" method="post">
	<input type="hidden" name="start_dates" value="<?php echo (isset($_GET['start_date'])?$_GET['start_date']:'') ?>">
	<input type="hidden" name="end_dates" value="<?php echo (isset($_GET['end_date'])?$_GET['end_date']:'') ?>">
<table border="0" cellspacing="0" cellpadding="3" width="100%">
<thead>
    <tr>
	 <th class="ui-widget-header" align="center"><b>Total Sales</b></th>
     <th class="ui-widget-header" align="center"><b>Customer Group</b></th>
    </tr>
</thead>
	   <tbody>
<?php

	$Qorders = Doctrine_Query::create()
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

	if (isset($_GET['start_date']) && !empty($_GET['start_date'])){
		$Qorders->andWhere('o.date_purchased >= ?', $_GET['start_date']);
	}

	if (isset($_GET['end_date']) && !empty($_GET['end_date'])){
		$Qorders->andWhere('o.date_purchased <= ?', date('Y-m-d',strtotime('+1 day',strtotime($_GET['end_date']))));
	}

	EventManager::notify('OrdersListingBeforeExecute', &$Qorders);

	$Result = $Qorders->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	if ($Result){

		foreach($Result as $oInfo){

			echo '<tr class="productListingRow-dater">
			  <td class="maind" align="center">'  . $currencies->format($oInfo['tot']) . '</td>
			  <td class="maind" align="center">'  . $oInfo['Customers']['CustomersToCustomerGroups'][0]['CustomerGroups']['customer_groups_name'].'</td>
			 </tr>';

		}
	}

?>

</tbody> </table>
	<?php
	$csvButton = htmlBase::newElement('button')
	->setType('submit')
	->usePreset('save')
	->setText('Save CSV');
	echo $csvButton->draw();
 ?>
		</form>
</div>