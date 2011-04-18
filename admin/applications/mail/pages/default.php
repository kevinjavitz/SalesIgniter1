<?php
    $customers = array();
    $customers[] = array('id' => '', 'text' => sysLanguage::get('TEXT_SELECT_CUSTOMER'));
    $customers[] = array('id' => '***', 'text' => sysLanguage::get('TEXT_ALL_CUSTOMERS'));
    $customers[] = array('id' => '**D', 'text' => sysLanguage::get('TEXT_NEWSLETTER_CUSTOMERS'));
    
    $Qcustomers = Doctrine_Query::create()
    ->select('customers_email_address, customers_firstname, customers_lastname')
    ->from('Customers')
    ->orderBy('customers_lastname')
    ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
    foreach($Qcustomers as $cInfo){
    	$customers[] = array(
    		'id' => $cInfo['customers_email_address'],
    		'text' => $cInfo['customers_lastname'] . ', ' . $cInfo['customers_firstname'] . ' (' . $cInfo['customers_email_address'] . ')'
    	);
    }
?>
<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE');
?></div>
<form name="mail" action="<?php echo itw_app_link(null, 'mail', 'preview');?>" method="post">
<table border="0" cellpadding="0" cellspacing="2">
	<tr>
		<td class="main"><?php echo sysLanguage::get('TEXT_CUSTOMER'); ?></td>
		<td><?php echo tep_draw_pull_down_menu('customers_email_address', $customers, (isset($_GET['customer']) ? $_GET['customer'] : ''));?></td>
	</tr>
	<tr>
		<td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
	</tr>
	<tr>
		<td class="main"><?php echo sysLanguage::get('TEXT_FROM'); ?></td>
		<td><?php echo tep_draw_input_field('from', sysConfig::get('EMAIL_FROM')); ?></td>
	</tr>
	<tr>
		<td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
	</tr>
	<tr>
		<td class="main"><?php echo sysLanguage::get('TEXT_SUBJECT'); ?></td>
		<td><?php echo tep_draw_input_field('subject'); ?></td>
	</tr>
	<tr>
		<td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
	</tr>
	<tr>
		<td valign="top" class="main"><?php echo sysLanguage::get('TEXT_MESSAGE'); ?></td>
		<td><?php echo tep_draw_textarea_field('message', 'soft', '60', '15'); ?></td>
	</tr>
	<tr>
		<td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
	</tr>
	<tr>
		<td colspan="2" align="right"><?php echo htmlBase::newElement('button')->setText(sysLanguage::get('IMAGE_SEND_EMAIL'))->setType('submit')->draw(); ?></td>
	</tr>
</table>
</form>