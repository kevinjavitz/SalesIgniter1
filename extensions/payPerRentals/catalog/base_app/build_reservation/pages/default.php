<?php
	$pID_string = $_GET['products_id'];
	$product = new product((int)$pID_string);
	$purchaseTypeClass = $product->getPurchaseType('reservation');
	$pprTable = Doctrine_Core::getTable('ProductsPayPerRental')->findOneByProductsId($pID_string);
	$insurancePrice = $pprTable->insurance;
	if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_GLOBAL_MIN_RENTAL_DAYS') == 'False') {
		$minRentalPeriod = ReservationUtilities::getPeriodTime($pprTable->min_period, $pprTable->min_type) * 60 * 1000;
	} else {
		$minRentalPeriod = (int)sysConfig::get('EXTENSION_PAY_PER_RENTALS_MIN_RENTAL_DAYS') * 24 * 60 * 60 * 1000;
	}
	$maxRentalPeriod = -1;

	if ($pprTable->max_period > 0) {
		$maxRentalPeriod = ReservationUtilities::getPeriodTime($pprTable->max_period, $pprTable->max_type) * 60 * 1000;
	}
	ob_start();
?>
  <table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox reservationTable">
   <tr class="infoBoxContents">
    <td><table cellpadding="3" cellspacing="0" border="0" width="100%">
     <tr>
      <td colspan="2" class="main" id="daysMsg" style="display:none;"><b><?php echo sysLanguage::get('TEXT_ONETIME_INTRO');?></b></td>
     </tr>
	<?php
      if ($purchaseTypeClass->getDepositAmount() > 0){
		$infoIcon = htmlBase::newElement('icon')
		->setType('info')
		->attr('onclick', 'popupWindow(\'' . itw_app_link('appExt=infoPages&dialog=true', 'show_page', 'ppr_deposit_info') . '\',400,300);')
		->css(array(
			'display' => 'inline-block',
			'cursor' => 'pointer'
		));
	?>
	<tr>

      <td colspan="2" class="main"><?php echo sysLanguage::get('PPR_DEPOSIT_AMOUNT') . ' - '. $currencies->format($purchaseTypeClass->getDepositAmount()) . $infoIcon->draw();?></td>
     </tr>
		<?php
	}
		?>
     <tr>

      <td class="main" colspan="2"><?php echo $purchaseTypeClass->getPricingTable(false, false, false);?></td>
     </tr>
<?php
	//this part needs redone
	 if ($maxRentalPeriod > 0){
?>
     <tr>
      <td class="main"><?php echo sysLanguage::get('TEXT_MAX') . ' ' . ReservationUtilities::getPeriodType($pprTable->max_type);?>: </td>
      <td class="main" id="maxPeriod"><?php echo $pprTable->max_period. ' '.ReservationUtilities::getPeriodType($pprTable->max_type);?></td>
     </tr>
<?php
}
?>
		<?php
if ($minRentalPeriod > 0){
?>
     <tr>
      <td class="main"><?php echo sysLanguage::get('TEXT_MIN') . ' ' . ReservationUtilities::getPeriodType($pprTable->min_type);?>: </td>
      <td class="main" id="minPeriod"><?php echo $pprTable->min_period.' '.ReservationUtilities::getPeriodType($pprTable->min_type);?></td>
     </tr>
<?php
}
?>
	<?php
if ($insurancePrice > 0){
?>
     <tr>
      <td class="main"><?php echo sysLanguage::get('TEXT_INSURANCE') . ' ' ;?>: </td>
      <td class="main" id="insurance_price"><?php echo $currencies->format($insurancePrice) ;?></td>
     </tr>
<?php
}
?>
       <tr>
	       <td colspan="2">
			<?php
				echo ReservationUtilities::getCalendar($_GET['products_id']);
			?>
	       </td>
       </tr>

    </table></td>

      </tr>
     </table>
   <?php
	   $pageContents = ob_get_contents();
	   ob_end_clean();

	   $pageTitle = 'Create Reservation';

	   $pageButtons = '';
	   if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_SHOW_CALENDAR_PRODUCT_INFO') == 'False') {
		    $pageButtons .= htmlBase::newElement('button')
		    ->usePreset('back')
		    ->setHref(itw_app_link('products_id=' . $pID_string, 'product', 'info'))
		    ->draw();
	   }

	   $pageButtons .= sysLanguage::get('TEXT_ESTIMATED_PRICING') . '<span id="priceQuote"></span>';
	   $pageButtons .= '<input type="hidden" name="products_id" id="pID" value="' . $product->getID() . '">';
	   $pageButtons .= $purchaseTypeClass->getHiddenFields($pID_string);

	   $pageButtons .= htmlBase::newElement('button')
	   ->setId('checkAvail')
	   ->setName('checkAvail')
	   ->setText(sysLanguage::get('TEXT_BUTTON_CHECK_AVAIL'))
	   ->draw();

	   $pageButtons .= htmlBase::newElement('div')
	   ->attr('id','inCart')
	   ->css(array(
		   'display'   => 'inline-block',
		   'width' => '150px'
	   ))
	   ->html(sysLanguage::get('TEXT_BUTTON_IN_CART'))
	   ->draw();

	   $pageContent->set('pageForm', array(
		   'name' => 'build_reservation',
		   'action' => itw_app_link(tep_get_all_get_params(array('action'))),
		   'method' => 'post'
	   ));

	   $pageContent->set('pageTitle', $pageTitle);
	   $pageContent->set('pageContent', $pageContents);
	   $pageContent->set('pageButtons', $pageButtons);
