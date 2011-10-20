<script type="text/javascript"><!--
var plans = [];
function fnPaymentChange(val){
	if (val!='paypal_ipn'){
		document.customers.cc_number.disabled = false;
		document.customers.cc_expires_month.disabled = false;
		document.customers.cc_expires_year.disabled = false;
	}else{
		document.customers.cc_number.disabled = true;
		document.customers.cc_expires_month.disabled = true;
		document.customers.cc_expires_year.disabled = true;
	}
}
function fnClicked() {
	if ($('select[name="activate"]').val() == 'Y') {
		var moveDays = plans[document.customers.planid.options[document.customers.planid.options.selectedIndex].value];

		document.customers.planid.disabled = false;
        $('select[name="payment_method"], input[name="cc_number"], select[name="cc_expires_month"], select[name="cc_expires_year"], select[name="next_billing_day"], select[name="next_billing_month"], select[name="next_billing_year"]').each( function (){
            $(this).removeAttr('disabled');
            $(this).removeClass('ui-state-disabled');
        });

		var daysMonths = new Array(0,31,28,31,30,31,30,31,31,30,31,30,31);
		var actualDay = <?php echo (int)date("d");?>;
		var actualMonth = <?php echo (int)date("m");?>;
		var actualYear = <?php echo (int)date("y");?>;
		var calculatedDay = actualDay + moveDays;

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
		document.customers.next_billing_day.options.selectedIndex = endingDay - 1;
		document.customers.next_billing_month.options.selectedIndex = endingMonth - 1;
		document.customers.next_billing_year.options.selectedIndex = endingYear - <?php echo (int)date("y");?>;
	}else if($('select[name="activate"]').val() == 'N'){
        $('select[name="payment_method"], input[name="cc_number"], select[name="cc_expires_month"], select[name="cc_expires_year"], select[name="next_billing_day"], select[name="next_billing_month"], select[name="next_billing_year"]').each( function (){
            $(this).attr('disabled','true');
            $(this).addClass('ui-state-disabled');
        });
		document.customers.planid.disabled = true;
	}

}

function check_form() {
	var error = 0;
	var error_message = "<?php echo sysLanguage::get('JS_ERROR'); ?>";
	var customers_firstname = document.customers.customers_firstname.value;
	var customers_lastname = document.customers.customers_lastname.value;
	<?php if (ACCOUNT_COMPANY == 'true') echo 'var entry_company = document.customers.entry_company.value;' . "\n"; ?>
	<?php if (ACCOUNT_DOB == 'true') echo 'var customers_dob = document.customers.customers_dob.value;' . "\n"; ?>
	var customers_email_address = document.customers.customers_email_address.value;
	var entry_street_address = document.customers.entry_street_address.value;
	var entry_postcode = document.customers.entry_postcode.value;
	var entry_city = document.customers.entry_city.value;
	var customers_telephone = document.customers.customers_telephone.value;

	<?php if (ACCOUNT_GENDER == 'true') { ?>
	if (document.customers.customers_gender[0].checked || document.customers.customers_gender[1].checked) {
	} else {
		error_message = error_message + "<?php echo sysLanguage::get('JS_GENDER'); ?>";
		error = 1;
	}
	<?php } ?>

	if (customers_firstname == "" || customers_firstname.length < <?php echo sysConfig::get('ENTRY_FIRST_NAME_MIN_LENGTH'); ?>) {
		error_message = error_message + "<?php echo sysLanguage::get('JS_FIRST_NAME'); ?>";
		error = 1;
	}

	if (customers_lastname == "" || customers_lastname.length < <?php echo sysConfig::get('ENTRY_LAST_NAME_MIN_LENGTH'); ?>) {
		error_message = error_message + "<?php echo sysLanguage::get('JS_LAST_NAME'); ?>";
		error = 1;
	}

	<?php if (ACCOUNT_DOB == 'true') { ?>
	if (customers_dob == "" || customers_dob.length < <?php echo sysConfig::get('ENTRY_DOB_MIN_LENGTH'); ?>) {
		error_message = error_message + "<?php echo sysLanguage::get('JS_DOB'); ?>";
		error = 1;
	}
	<?php } ?>

	if (customers_email_address == "" || customers_email_address.length < <?php echo sysConfig::get('ENTRY_EMAIL_ADDRESS_MIN_LENGTH'); ?>) {
		error_message = error_message + "<?php echo sysLanguage::get('JS_EMAIL_ADDRESS'); ?>";
		error = 1;
	}

	if (entry_street_address == "" || entry_street_address.length < <?php echo sysConfig::get('ENTRY_STREET_ADDRESS_MIN_LENGTH'); ?>) {
		error_message = error_message + "<?php echo sysLanguage::get('JS_ADDRESS'); ?>";
		error = 1;
	}

	if (entry_postcode == "" || entry_postcode.length < <?php echo sysConfig::get('ENTRY_POSTCODE_MIN_LENGTH'); ?>) {
		error_message = error_message + "<?php echo sysLanguage::get('JS_POST_CODE'); ?>";
		error = 1;
	}

	if (entry_city == "" || entry_city.length < <?php echo sysConfig::get('ENTRY_CITY_MIN_LENGTH'); ?>) {
		error_message = error_message + "<?php echo sysLanguage::get('JS_CITY'); ?>";
		error = 1;
	}

	<?php
	if (ACCOUNT_STATE == 'true') {
		?>
		if (document.customers.elements['entry_state'].type != "hidden") {
			if (document.customers.entry_state.value == '' || document.customers.entry_state.value.length < <?php echo ENTRY_STATE_MIN_LENGTH; ?> ) {
				error_message = error_message + "<?php echo sysLanguage::get('JS_STATE'); ?>";
				error = 1;
			}
		}
		<?php
	}
	?>

	if (document.customers.elements['entry_country_id'].type != "hidden") {
		if (document.customers.entry_country_id.value == 0) {
			error_message = error_message + "<?php echo sysLanguage::get('JS_COUNTRY'); ?>";
			error = 1;
		}
	}

	/*  if (customers_telephone == "" || customers_telephone.length < <?php echo sysConfig::get('ENTRY_TELEPHONE_MIN_LENGTH'); ?>) {
	error_message = error_message + "<?php echo sysLanguage::get('JS_TELEPHONE'); ?>";
	error = 1;
	}*/
    /*
	if(document.customers.make_member.checked){
		if(document.customers.payment_method[1].checked){
			if(document.customers.cc_number.value==''){
				error_message += "<?php echo sysLanguage::get('JS_ERROR_AUTH_NET_NUMBER');?>";
				error = 1;
			}
		}else if(document.customers.payment_method[2].checked){
			if(document.customers.cc_number.value==''){
				error_message += "\n<?php echo sysLanguage::get('JS_ERROR_USAEPAY_NUMBER');?>";
				error = 1;
			}
		}else if(document.customers.payment_method[3].checked){
			if(document.customers.cc_number.value==''){
				error_message += "\n<?php echo sysLanguage::get('JS_ERROR_CC_NUMBER');?>";
				error = 1;
			}
		}
	}
	*/

	with(document.customers){
		if(payment_method.value=="authorizenet"){
			if(next_billing_day.value=="" || next_billing_month.value=="" || next_billing_year.value==""){
				error_message = error_message + "\n<?php echo sysLanguage::get('JS_ERROR_BILLING_DATE');?>";
				error = 1;
			}
			if(cc_number.value==""){
				error_message = error_message + "\n<?php echo sysLanguage::get('JS_ERROR_CC_NUMBER');?>";
				error = 1;
			}
			if(cc_expires_month.value=="" || cc_expires_year.value==""){
				error_message = error_message + "\n<?php echo sysLanguage::get('JS_ERROR_CC_EXPIRES');?>";
				error = 1;
			}
		}else if(payment_method.value=="paypal_ipn" || payment_method.value=="cod" || payment_method.value=="moneyorder"){
			if(next_billing_day.value=="" || next_billing_month.value=="" || next_billing_year.value==""){
				error_message = error_message + "\n<?php echo sysLanguage::get('JS_ERROR_BILLING_DATE');?>";
				error = 1;
			}
		}else if(payment_method.value=="cc"){
			if(next_billing_day.value=="" || next_billing_month.value=="" || next_billing_year.value==""){
				error_message = error_message + "\n<?php echo sysLanguage::get('JS_ERROR_BILLING_DATE');?>";
				error = 1;
			}
			if(cc_number.value==""){
				error_message = error_message + "\n<?php echo sysLanguage::get('JS_ERROR_CC_NUMBER');?>";
				error = 1;
			}
			if(cc_expires_month.value=="" || cc_expires_year.value==""){
				error_message = error_message + "\n<?php echo sysLanguage::get('JS_ERROR_CC_EXPIRES');?>";
				error = 1;
			}
		}else if(payment_method.value=="usaepay"){
			if(next_billing_day.value=="" || next_billing_month.value=="" || next_billing_year.value==""){
				error_message = error_message + "\n<?php echo sysLanguage::get('JS_ERROR_BILLING_DATE');?>";
				error = 1;
			}
		}
	}

	if (error == 1) {
		alert(error_message);
		return false;
	} else {
		return true;
	}
}
//-->
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
	include(DIR_WS_APP . 'customers/pages_tabs/edit/customer_info.php');
	$customerInfoTab = ob_get_contents();
	ob_end_clean();
	
	ob_start();
	include(DIR_WS_APP . 'customers/pages_tabs/edit/membership_info.php');
	$membershipInfoTab = ob_get_contents();
	ob_end_clean();
	
	ob_start();
	include(DIR_WS_APP . 'customers/pages_tabs/edit/billing_history.php');
	$rentalTab1 = ob_get_contents();
	ob_end_clean();
	
	ob_start();
	include(DIR_WS_APP . 'customers/pages_tabs/edit/current_rentals.php');
	$rentalTab2 = ob_get_contents();
	ob_end_clean();
	
	ob_start();
	include(DIR_WS_APP . 'customers/pages_tabs/edit/rental_history.php');
	$rentalTab3 = ob_get_contents();
	ob_end_clean();
	
	ob_start();
	include(DIR_WS_APP . 'customers/pages_tabs/edit/rental_issue_history.php');
	$rentalTab4 = ob_get_contents();
	ob_end_clean();
	
	$tabsObj = htmlBase::newElement('tabs')
	->setId('customerTabs')
	->addTabHeader('customerTab1', array('text' => 'Customer Info'))
	->addTabPage('customerTab1', array('text' => $customerInfoTab))
	->addTabHeader('customerTab2', array('text' => 'Membership Info'))
	->addTabPage('customerTab2', array('text' => $membershipInfoTab))
	->addTabHeader('rentalTab1', array('text' => sysLanguage::get('HEADING_BILLING_HISTORY')))
	->addTabPage('rentalTab1', array('text' => $rentalTab1))
	->addTabHeader('rentalTab2', array('text' => sysLanguage::get('HEADING_CURRENT_RENTALS')))
	->addTabPage('rentalTab2', array('text' => $rentalTab2))
	->addTabHeader('rentalTab3', array('text' => sysLanguage::get('HEADING_RENTAL_HISTORY')))
	->addTabPage('rentalTab3', array('text' => $rentalTab3))
	->addTabHeader('rentalTab4', array('text' => sysLanguage::get('HEADING_ISSUE_HISTORY')))
	->addTabPage('rentalTab4', array('text' => $rentalTab4));
	
	EventManager::notify('AdminCustomerEditBuildTabs', $Customer, &$tabsObj);
	
	$updateButton = htmlBase::newElement('button')->setType('submit')->usePreset('save')->setText(sysLanguage::get('TEXT_BUTTON_UPDATE'));
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