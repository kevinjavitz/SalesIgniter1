<?php
$MultiStores = $appExtension->getExtension('multiStore');
$buttonDataStores = array();
foreach($MultiStores->getStoresArray() as $sInfo){
	$buttonDataStores[] = array(
		'labelPosition' => 'after',
		'value' => $sInfo['stores_id'],
		'label' => $sInfo['stores_name']
	);
}

$StoresBoxes = htmlBase::newElement('checkbox');
$StoresBoxes->addGroup(array(
		'name' => 'report_stores[]',
		'separator' => array(
			'type' => 'table',
			'columns' => 4
		),
		'data' => $buttonDataStores
	));

$buttonDataIncomeCols = array();
$buttonDataIncomeCols[] = array(
	'labelPosition' => 'after',
	'value' => 'sales',
	'label' => sysLanguage::get('TEXT_ENTRY_SALES')
);

$buttonDataIncomeCols[] = array(
	'labelPosition' => 'after',
	'value' => 'late_fees',
	'label' => sysLanguage::get('TEXT_ENTRY_LATE_FEES')
);

$buttonDataIncomeCols[] = array(
	'labelPosition' => 'after',
	'value' => 'sales_tax',
	'label' => sysLanguage::get('TEXT_ENTRY_SALES_TAX')
);

$buttonDataIncomeCols[] = array(
	'labelPosition' => 'after',
	'value' => 'store_to_store_income',
	'label' => sysLanguage::get('TEXT_ENTRY_STORE_TO_STORE_INCOME')
);

$IncomeColumnBoxes = htmlBase::newElement('checkbox');
$IncomeColumnBoxes->addGroup(array(
		'name' => 'income_columns[]',
		'separator' => array(
			'type' => 'table',
			'columns' => 4
		),
		'data' => $buttonDataIncomeCols
	));

$buttonDataExpenseCols = array();
$buttonDataExpenseCols[] = array(
	'labelPosition' => 'after',
	'value' => 'coupons',
	'label' => sysLanguage::get('TEXT_ENTRY_COUPONS')
);

$buttonDataExpenseCols[] = array(
	'labelPosition' => 'after',
	'value' => 'credits',
	'label' => sysLanguage::get('TEXT_ENTRY_CREDITS')
);

$buttonDataExpenseCols[] = array(
	'labelPosition' => 'after',
	'value' => 'store_to_store_expense',
	'label' => sysLanguage::get('TEXT_ENTRY_STORE_TO_STORE_EXPENSE')
);

$ExpenseColumnBoxes = htmlBase::newElement('checkbox');
$ExpenseColumnBoxes->addGroup(array(
		'name' => 'expense_columns[]',
		'separator' => array(
			'type' => 'table',
			'columns' => 4
		),
		'data' => $buttonDataExpenseCols
	));

$buttonDataReportType = array();
$buttonDataReportType[] = array(
	'labelPosition' => 'after',
	'value' => 'day',
	'label' => sysLanguage::get('TEXT_ENTRY_DAILY')
);

$buttonDataReportType[] = array(
	'labelPosition' => 'after',
	'value' => 'month',
	'label' => sysLanguage::get('TEXT_ENTRY_MONTHLY')
);

$buttonDataReportType[] = array(
	'labelPosition' => 'after',
	'value' => 'year',
	'label' => sysLanguage::get('TEXT_ENTRY_YEARLY')
);

$ReportTypeBoxes = htmlBase::newElement('radio');
$ReportTypeBoxes->addGroup(array(
		'name' => 'report_type',
		'separator' => array(
			'type' => 'table',
			'columns' => 4
		),
		'data' => $buttonDataReportType
	));

$yearFromSelect = htmlBase::newElement('selectbox')
	->setName('year_from')
	->selectOptionByValue(date('Y'));
$yearToSelect = htmlBase::newElement('selectbox')
	->setName('year_to')
	->selectOptionByValue(date('Y'));
for($i=(date('Y') - 10); $i < (date('Y') + 10); $i++){
	$yearFromSelect->addOption($i, $i);
	$yearToSelect->addOption($i, $i);
}

$monthFromSelect = htmlBase::newElement('selectbox')
	->setName('month_from')
	->selectOptionByValue(date('m'));
$monthToSelect = htmlBase::newElement('selectbox')
	->setName('month_to')
	->selectOptionByValue(date('m'));
for($i=1; $i<13; $i++){
	$monthName = date('F',mktime(0,0,0,$i,1,2006));
	$monthFromSelect->addOption($i, $monthName);
	$monthToSelect->addOption($i, $monthName);
}

$buttonPaymentModules = array();
OrderPaymentModules::loadModules();
foreach(OrderPaymentModules::getModules() as $Module){
	$buttonPaymentModules[] = array(
		'labelPosition' => 'after',
		'value' => $Module->getCode(),
		'label' => $Module->getTitle()
	);
}

$PaymentModules = htmlBase::newElement('checkbox');
$PaymentModules->addGroup(array(
		'name' => 'payment_modules[]',
		'separator' => array(
			'type' => 'table',
			'columns' => 4
		),
		'data' => $buttonPaymentModules
	));
?>
<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE_SALES_REPORT');
	?></div>
<br />
<div>
	<table>
		<tr>
			<td><b><u>Report Type</u></b> <?php echo $ReportTypeBoxes->draw();?></td>
			<td rowspan="5" valign="top" id="dayFilter" class="reportTypeFilter">
				<table width="550px">
					<tr>
						<td style="text-align: center;"><b><u><?php echo sysLanguage::get('TEXT_ENTRY_DATE_FROM');?></u></b><div id="date_from"></div><input type="hidden" name="date_from"></td>
						<td style="text-align: center;"><b><u><?php echo sysLanguage::get('TEXT_ENTRY_DATE_TO');?></u></b><div id="date_to"></div><input type="hidden" name="date_to"></td>
					</tr>
				</table>
			</td>
			<td id="monthFilter" class="reportTypeFilter">
				<table>
					<tr>
						<td style="text-align: center;"><b><u><?php echo sysLanguage::get('TEXT_ENTRY_MONTH_FROM');?></u></b><div><?php echo $monthFromSelect->draw() . ' ' . $yearFromSelect->draw();?></div></td>
						<td style="text-align: center;"><b><u><?php echo sysLanguage::get('TEXT_ENTRY_MONTH_TO');?></u></b><div><?php echo $monthToSelect->draw() . ' ' . $yearToSelect->draw();?></div></td>
					</tr>
				</table>
			</td>
			<td id="yearFilter" class="reportTypeFilter">
				<table>
					<tr>
						<td style="text-align: center;"><b><u><?php echo sysLanguage::get('TEXT_ENTRY_YEAR_FROM');?></u></b><div><?php echo $yearFromSelect->draw();?></div></td>
						<td style="text-align: center;"><b><u><?php echo sysLanguage::get('TEXT_ENTRY_YEAR_TO');?></u></b><div><?php echo $yearToSelect->draw();?></div></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td><input type="checkbox" class="checkAll" tooltip="<?php echo sysLanguage::get('TEXT_INFO_CHECK_ALL_STORES');?>"><b><u><?php echo sysLanguage::get('TEXT_ENTRY_STORES');?></u></b> <?php echo $StoresBoxes->draw();?></td>
		</tr>
		<tr>
			<td><input type="checkbox" class="checkAll" tooltip="<?php echo sysLanguage::get('TEXT_INFO_CHECK_ALL_INCOME');?>"><b><u><?php echo sysLanguage::get('TEXT_ENTRY_INCOME_COLUMNS');?></u></b> <?php echo $IncomeColumnBoxes->draw();?></td>
		</tr>
		<tr>
			<td><input type="checkbox" class="checkAll" tooltip="<?php echo sysLanguage::get('TEXT_INFO_CHECK_ALL_EXPENSE');?>"><b><u><?php echo sysLanguage::get('TEXT_ENTRY_EXPENSE_COLUMNS');?></u></b> <?php echo $ExpenseColumnBoxes->draw();?></td>
		</tr>
		<tr>
			<td><input type="checkbox" class="checkAll" tooltip="<?php echo sysLanguage::get('TEXT_INFO_CHECK_ALL_PAYMENT');?>"><b><u><?php echo sysLanguage::get('TEXT_ENTRY_PAYMENT_MODULES');?></u></b> <?php echo $PaymentModules->draw();?></td>
		</tr>
		<tr>
			<td><?php
				echo htmlBase::newElement('button')
					->setId('generate')
					->setText(sysLanguage::get('TEXT_BUTTON_GENERATE'))
					->draw();

				echo htmlBase::newElement('button')
					->setId('generateCsv')
					->setText(sysLanguage::get('TEXT_BUTTON_GENERATE_CSV'))
					->draw();
			?></td>
		</tr>
	</table>
</div>
<div style="width:100%;float:left;">
	<div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
		<div style="width:99%;margin:5px;" class="reportHolder">
		</div>
	</div>
</div>