<script type="text/javascript">
var plans = [];
function fnPaymentChange(val){
	if (val!='paypalipn'){
		$('input[name="cc_number"], input[name="cc_cvv"], select[name="cc_expires_month"], select[name="cc_expires_year"]').each( function (){
			$(this).removeAttr('disabled');
			$(this).removeClass('ui-state-disabled');
		});
	}else{
		$('input[name="cc_number"], input[name="cc_cvv"], select[name="cc_expires_month"], select[name="cc_expires_year"]').each( function (){
			$(this).attr('disabled','true');
			$(this).addClass('ui-state-disabled');
		});

	}
}
function fnClicked() {
	if ($('select[name="activate"]').val() == 'Y') {
		var moveDays = plans[document.customers.planid.options[document.customers.planid.options.selectedIndex].value];

		document.customers.planid.disabled = false;
        $('select[name="payment_method"], input[name="cc_number"], input[name="cc_cvv"], select[name="cc_expires_month"], select[name="cc_expires_year"], select[name="next_billing_day"], select[name="next_billing_month"], select[name="next_billing_year"]').each( function (){
            $(this).removeAttr('disabled');
            $(this).removeClass('ui-state-disabled');
        });

		var daysMonths = new Array(0,31,28,31,30,31,30,31,31,30,31,30,31);
		var actualDay = <?php echo (int)date("d");?>;
		var actualMonth = <?php echo (int)date("m");?>;
		var actualYear = <?php echo (int)date("y");?>;
		var calculatedDay;
		if( moveDays != undefined){
			calculatedDay = actualDay + moveDays;
		}else{
			calculatedDay = $('select[name="next_billing_day"]').val();//document.customers.next_billing_day.selectedValue;
			actualMonth = $('select[name="next_billing_month"]').val();
			actualYear = $('select[name="next_billing_year"]').val();
		}

		var endingDay = calculatedDay;
		var endingMonth = actualMonth;
		var endingYear = actualYear;
		while (endingDay > daysMonths[endingMonth]) {
			endingDay -= daysMonths[endingMonth];
			endingMonth++;
			if (endingMonth > 12) {
				endingMonth = 1;
				endingYear++;
			}
		}
	}else if($('select[name="activate"]').val() == 'N'){
        $('select[name="payment_method"], input[name="cc_number"], input[name="cc_cvv"], select[name="cc_expires_month"], select[name="cc_expires_year"], select[name="next_billing_day"], select[name="next_billing_month"], select[name="next_billing_year"]').each( function (){
            $(this).attr('disabled','true');
            $(this).addClass('ui-state-disabled');
        });
		document.customers.planid.disabled = true;
	}

}
</script>
<script type="text/javascript">
$(document).ready(function (){
	$('#customerTabs').tabs();
	$('#rentalTabs').tabs();
	makeTabsVertical('#customerTabs');
});
</script>
<?php
	$Customers = Doctrine_Core::getTable('Customers');
	if (isset($_GET['cID'])){
		$Customer = $Customers->find((int) $_GET['cID']);
	}else{
		$Customer = $Customers->getRecord();
	}
		
	$newsletter_array = array(
		array(
			'id'   => '1',
			'text' => sysLanguage::get('ENTRY_NEWSLETTER_YES')
		),
		array(
			'id'   => '0',
			'text' => sysLanguage::get('ENTRY_NEWSLETTER_NO')
		)
	);
	
	ob_start();
	include(sysConfig::getDirFsAdmin() . 'applications/customers/pages_tabs/edit/customer_info.php');
	$customerInfoTab = ob_get_contents();
	ob_end_clean();
	
	ob_start();
	include(sysConfig::getDirFsAdmin() . 'applications/customers/pages_tabs/edit/membership_info.php');
	$membershipInfoTab = ob_get_contents();
	ob_end_clean();

	if (isset($_GET['cID'])){
ob_start();
include(sysConfig::getDirFsAdmin() . 'applications/customers/pages_tabs/edit/order_history.php');
$orderHistoryTab = ob_get_contents();
ob_end_clean();


ob_start();
include(sysConfig::getDirFsAdmin() . 'applications/customers/pages_tabs/edit/billing_history.php');
$rentalTab1 = ob_get_contents();
ob_end_clean();

	ob_start();
	include(sysConfig::getDirFsAdmin() . 'applications/customers/pages_tabs/edit/current_rentals.php');
	$rentalTab2 = ob_get_contents();
	ob_end_clean();
	
	ob_start();
	include(sysConfig::getDirFsAdmin() . 'applications/customers/pages_tabs/edit/rental_history.php');
	$rentalTab3 = ob_get_contents();
	ob_end_clean();
	
	ob_start();
	include(sysConfig::getDirFsAdmin() . 'applications/customers/pages_tabs/edit/rental_issue_history.php');
	$rentalTab4 = ob_get_contents();
	ob_end_clean();
	}

	$tabsObj = htmlBase::newElement('tabs')
	->setId('customerTabs')
	->addTabHeader('customerTab1', array('text' => 'Customer Info'))
	->addTabPage('customerTab1', array('text' => $customerInfoTab))
	->addTabHeader('customerTab2', array('text' => 'Membership Info'))
	->addTabPage('customerTab2', array('text' => $membershipInfoTab));

	if (isset($_GET['cID'])){
		$tabsObj->addTabHeader('orderHistoryTab', array('text' => sysLanguage::get('TAB_ORDER_HISTORY')))
	->addTabPage('orderHistoryTab', array('text' => $orderHistoryTab))
	->addTabHeader('rentalTab1', array('text' => sysLanguage::get('HEADING_BILLING_HISTORY')))
	->addTabPage('rentalTab1', array('text' => $rentalTab1))
	->addTabHeader('rentalTab2', array('text' => sysLanguage::get('HEADING_CURRENT_RENTALS')))
	->addTabPage('rentalTab2', array('text' => $rentalTab2))
	->addTabHeader('rentalTab3', array('text' => sysLanguage::get('HEADING_RENTAL_HISTORY')))
	->addTabPage('rentalTab3', array('text' => $rentalTab3))
	->addTabHeader('rentalTab4', array('text' => sysLanguage::get('HEADING_ISSUE_HISTORY')))
	->addTabPage('rentalTab4', array('text' => $rentalTab4));
	}

if (isset($_GET['cID'])){
	EventManager::notify('AdminCustomerEditBuildTabs', $Customer, &$tabsObj);
}else{
	EventManager::notify('AdminCustomerInsertBuildTabs', $Customer, &$tabsObj);
}
	
	$updateButton = htmlBase::newElement('button')
		->setType('submit')
		->usePreset('save')
		->setText((isset($_GET['cID']) ? sysLanguage::get('TEXT_BUTTON_UPDATE') : sysLanguage::get('TEXT_BUTTON_INSERT')));

	$cancelButton = htmlBase::newElement('button')->usePreset('cancel')->setText(sysLanguage::get('TEXT_BUTTON_CANCEL'))
	->setHref(itw_app_link(null, null, 'default', 'SSL'));
	
	$hiddenField = htmlBase::newElement('input')
	->setType('hidden')
	->setName('default_address_id')
	->setValue($Customer->customers_default_address_id);

	$buttonContainer = htmlBase::newElement('div')->addClass('ui-widget')->css(array(
		'text-align' => 'right',
		'width' => 'auto'
	))->append($hiddenField)->append($updateButton)->append($cancelButton);
	
	$tabsWrapper = htmlBase::newElement('div')->css('position', 'relative')->append($tabsObj);
	
	$pageForm = htmlBase::newElement('form')
	->attr('name', 'customers')
	->attr('action', itw_app_link(tep_get_all_get_params(array('action')) . 'action=update', null, null, 'SSL'))
	->attr('method', 'post')
	->append($tabsWrapper)
	->append(htmlBase::newElement('br'))
	->append($buttonContainer);
	
	$headingTitle = htmlBase::newElement('div')
	->addClass('pageHeading')
	->html(sysLanguage::get('HEADING_TITLE'));
	
	echo $headingTitle->draw() . '<br />' . $pageForm->draw();
?>
<script type="text/javascript">
	<?php echo (isset($jsMBM) ? $jsMBM : ''); /* Will be moved later */ ?>
</script>