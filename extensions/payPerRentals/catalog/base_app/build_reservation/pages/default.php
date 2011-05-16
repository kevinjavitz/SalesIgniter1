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
	 <div class="depositText" style="display:block;">
        <?php echo sysLanguage::get('PPR_DEPOSIT_AMOUNT') . ' - '. $currencies->format($purchaseTypeClass->getDepositAmount()) . $infoIcon->draw();?>
	 </div>
		<?php
	}
		?>
	<div class="pricingTable" style="display:block;">
        <?php echo $purchaseTypeClass->getPricingTable(false, false, false);?>
	</div>
<?php
	//this part needs redone
	 if ($maxRentalPeriod > 0){
?>
	<div class="maxPeriod" style="display:block;">
        <?php echo sysLanguage::get('TEXT_MAX') . ' ' . ReservationUtilities::getPeriodType($pprTable->max_type);?>:
        <?php echo $pprTable->max_period . ' '.ReservationUtilities::getPeriodType($pprTable->max_type);?>
	</div>
<?php
}
?>
		<?php
if ($minRentalPeriod > 0){
?>
	<div class="minPeriod" style="display:block;">
        <?php echo sysLanguage::get('TEXT_MIN') . ' ' . ReservationUtilities::getPeriodType($pprTable->min_type);?>:
		<?php echo $pprTable->min_period.' '.ReservationUtilities::getPeriodType($pprTable->min_type);?>
	</div>

<?php
}
?>
	<?php
if ($insurancePrice > 0){
?>
	<div class="insurancePrice" style="display:block;">
        <?php echo sysLanguage::get('TEXT_INSURANCE') . ' ' ;?>:
        <?php echo $currencies->format($insurancePrice) ;?>
	</div>
<?php
}
?>
    <div class="calendarTable" style="display:block;">
		<?php
				echo ReservationUtilities::getCalendar($_GET['products_id'],$product, $purchaseTypeClass, 1, true);
		?>
	</div>

   <?php
	   $pageContents = ob_get_contents();
	   ob_end_clean();

	   $pageTitle = sysLanguage::get('TEXT_CREATE_RESERVATION');

	   $pageButtons = '';
	   if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_SHOW_CALENDAR_PRODUCT_INFO') == 'False') {
		    $pageButtons .= htmlBase::newElement('button')
		    ->usePreset('back')
		    ->setHref(itw_app_link('products_id=' . $pID_string, 'product', 'info'))
		    ->draw();
	   }


	   $pageContent->set('pageForm', array(
		   'name' => 'build_reservation',
		   'action' => itw_app_link(tep_get_all_get_params(array('action'))),
		   'method' => 'post'
	   ));

	   $pageContent->set('pageTitle', $pageTitle);
	   $pageContent->set('pageContent', $pageContents);
	   $pageContent->set('pageButtons', $pageButtons);
