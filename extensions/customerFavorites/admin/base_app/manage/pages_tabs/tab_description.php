<?php

	$htmlSearchCustomer = htmlBase::newElement('input')
	->setName('searchCustomer')
	->attr('id','searchCustomer');

    $htmlButtonAddToList = htmlBase::newElement('button')
	->usePreset('save')
    ->attr('id','addToList')
	->setText('Add to List');

	if (isset($_GET['cID'])){
		$QCustomers = Doctrine_Query::create()
		->from('Customers c')
		->leftJoin('c.CustomersToCustomerGroups cg')
		->where('cg.customer_groups_id=?',(int)$_GET['cID'])
		->execute(array(),Doctrine_Core::HYDRATE_ARRAY);

		$name = $CustomerGroups->customer_groups_name;
		$credit = $CustomerGroups->customer_groups_credit;
		$htmlCustomerList = '<ul>';
		foreach($QCustomers as $iCustomer){
			$htmlInput = htmlBase::newElement('input')
			->setType('hidden')
			->setName('selectedCustomer[]')
			->addClass('selectedCustomer')
			->setValue($iCustomer['customers_id']);
			$htmlCustomerList .= '<li>'.$htmlInput->draw().' '.$iCustomer['customers_firstname'].' '.$iCustomer['customers_lastname'].'('.$iCustomer['customers_email_address'].')'.'<span class="ui-icon ui-icon-minusthick removeCustomer"></span>'.'</li>';
		}
		$htmlCustomerList .= '</ul>';
	}else{
		$name = '';
		$credit = 0;
		$htmlCustomerList = '<ul></ul>';
	}

?>



 <table cellpadding="3" cellspacing="0" border="0">
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_CUSTOMER_GROUPS_NAME'); ?></td>
   <td class="main"><?php echo tep_draw_input_field('customer_groups_name', $name); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
	 <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_CUSTOMER_GROUPS_CREDIT'); ?></td>
   <td class="main"><?php echo tep_draw_input_field('customer_groups_credit', $credit); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <tr>
	<td colspan="2">
		<table style="width:980px;table-layout:fixed">
			<tr>
				<td style="width:450px;vertical-align:top;"><?php echo sysLanguage::get('TEXT_CUSTOMER_GROUPS_SEARCH_CUSTOMER'); ?></td>
				<td style="width:500px;vertical-align:top;"><?php echo sysLanguage::get('TEXT_CUSTOMER_GROUPS_LIST'); ?></td>
			</tr>
			<tr>
				<td style="width:450px;vertical-align:top;"><?php echo $htmlSearchCustomer->draw(); ?><div id="searchingCustomerList"></div><br/><?php echo $htmlButtonAddToList->draw()?></td>
				<td style="width:500px;vertical-align:top;"><div id="customerList"><?php echo $htmlCustomerList; ?></div></td>
			</tr>
		</table>
	</td>

  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>

 </table>

