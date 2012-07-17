<?php
	$pID_string = $_GET['products_id'];
	$product = new product((int)$pID_string);
	$purchaseTypeClass = $product->getPurchaseType('reservation');
	$purchaseTypeClasses = array();
	$purchaseTypeClasses[] = $purchaseTypeClass;
	$pprTable = Doctrine_Core::getTable('ProductsPayPerRental')->findOneByProductsId($pID_string);
	$insurancePrice = $pprTable->insurance;
    $currencies = new currencies();

    $freeTrialButton = 0;
    if(isset($_POST['freeTrialButton']) && $_POST['freeTrialButton'] == '1') {
        $freeTrialButton = $_POST['freeTrialButton'];
        $freeTrial = $_POST['freeTrial'];
        $freeOn = explode(',',$_POST['freeTrial']);
        $minRentalPeriod = ReservationUtilities::getPeriodTime($freeOn[0], $freeOn[1]) * 60 * 1000;
    }
    elseif(isset($_GET['freeTrialButton']) && $_GET['freeTrialButton'] == '1') {
        $freeTrialButton = $_GET['freeTrialButton'];
        $freeTrial = $_GET['freeTrial'];
        $freeOn = explode(',',$_GET['freeTrial']);
        $minRentalPeriod = ReservationUtilities::getPeriodTime($freeOn[0], $freeOn[1]) * 60 * 1000;
    }
    elseif (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_GLOBAL_MIN_RENTAL_DAYS') == 'False') {
		$minRentalPeriod = ReservationUtilities::getPeriodTime($pprTable->min_period, $pprTable->min_type) * 60 * 1000;
	}else{
		$minRentalPeriod = (int)sysConfig::get('EXTENSION_PAY_PER_RENTALS_MIN_RENTAL_DAYS') * 24 * 60 * 60 * 1000;
	}
	$maxRentalPeriod = -1;

    if($freeTrialButton) {
        $maxRentalPeriod = ReservationUtilities::getPeriodTime($freeOn[0], $freeOn[1]) * 60 * 1000;
    }
	elseif($pprTable->max_period > 0) {
		$maxRentalPeriod = ReservationUtilities::getPeriodTime($pprTable->max_period, $pprTable->max_type) * 60 * 1000;
	}

	ob_start();
?>
    <input type="hidden" name="freeTrial" value="<?php echo $freeTrial ?>">
    <input type="hidden" name="freeTrialButton" value="<?php echo $freeTrialButton ?>">
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
         <?php
          if($freeTrialButton) {
            echo sysLanguage::get('PPR_DEPOSIT_AMOUNT') . ' - '. $currencies->format(0) . $infoIcon->draw();
          }else{
            echo sysLanguage::get('PPR_DEPOSIT_AMOUNT') . ' - '. $currencies->format($purchaseTypeClass->getDepositAmount()) . $infoIcon->draw();
          }?>
	 </div>
		<?php
	}
		?>
	<div class="pricingTable" style="display:block;">
        <?php
    if($freeTrialButton) {
        echo $freeOn[0] . ' '.ReservationUtilities::getPeriodType($freeOn[1]).' - '. $currencies->format($freeOn[2]);
    }else{
        echo $purchaseTypeClass->getPricingTable();
    }?>
	</div>
	<div class="periodsInsurance">
<?php
	//this part needs redone
    if($freeTrialButton) {
        ?>
        <div class="maxPeriod" style="display:block;">
            <?php echo sysLanguage::get('TEXT_MAX') . ' ' . ReservationUtilities::getPeriodType($freeOn[1]);?>:
            <?php echo $freeOn[0] . ' '.ReservationUtilities::getPeriodType($freeOn[1]);?>
        </div>
        <?php
    }
    elseif (!$freeTrialButton && $maxRentalPeriod > 0){
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
        <?php
    if($freeTrialButton) {
        echo sysLanguage::get('TEXT_MIN') . ' ' . ReservationUtilities::getPeriodType($freeOn[1]) .': '. $freeOn[0].' '.ReservationUtilities::getPeriodType($freeOn[1]);
    }
	elseif (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_GLOBAL_MIN_RENTAL_DAYS') == 'False') {
		echo sysLanguage::get('TEXT_MIN') . ' ' . ReservationUtilities::getPeriodType($pprTable->min_type) .': '. $pprTable->min_period.' '.ReservationUtilities::getPeriodType($pprTable->min_type);
	}else{
		echo sysLanguage::get('TEXT_MIN') . ' ' . sysLanguage::get('TEXT_DAYS').': '. sysConfig::get('EXTENSION_PAY_PER_RENTALS_MIN_RENTAL_DAYS');
	}
	?>
	</div>

<?php
}
?>
	<?php
if ($insurancePrice > 0){
?>
	<div class="insurancePrice" style="display:block;">
		<?php
			$infoIconIns = htmlBase::newElement('a')
			->html(sysLanguage::get('TEXT_INSURANCE'))
			->attr('onclick', 'popupWindow(\'' . itw_app_link('appExt=infoPages&dialog=true', 'show_page', 'Insurance_calendar') . '\',400,300);return false;')
			->css(array(
				'cursor' => 'pointer'
			));
            if($freeTrialButton) {
                echo $infoIconIns->draw(). $currencies->format(0) ;
            }
            else {
                echo $infoIconIns->draw(). $currencies->format($insurancePrice) ;
            }
		?>
	</div>
<?php
}
?>
	</div>
	<div class="beforeCalendar"></div>
    <div class="calendarTable" style="display:block;">
		<?php
				echo ReservationUtilities::getCalendar($_GET['products_id'], $purchaseTypeClasses, 1, true);
		?>
	</div>

   <?php
	   $pageContents = ob_get_contents();
	   ob_end_clean();

	   $pageTitle = '<span class="pprText">'.$product->getName().'</span>';

	   $pageButtons = '';
	   if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_SHOW_CALENDAR_PRODUCT_INFO') == 'False') {
		   if(is_object($pageContent)){
				$pageButtons .= htmlBase::newElement('button')
				->usePreset('back')
				->addClass('pprBack')
				->setHref(itw_app_link('products_id=' . $pID_string, 'product', 'info'))
				->draw();



			   $pageContent->set('pageForm', array(
				   'name' => 'build_reservation',
				   'action' => itw_app_link(tep_get_all_get_params(array('action'))),
				   'method' => 'post'
			   ));

			   $pageContent->set('pageTitle', $pageTitle);
			   $pageContent->set('pageContent', $pageContents);
			   $pageContent->set('pageButtons', $pageButtons);
		   }
	   }else{
		   $htmlForm = htmlBase::newElement('form')
			->attr('name', 'build_reservation')
			->attr('action', itw_app_link(tep_get_all_get_params(array('action'))))
			->attr('method', 'post');
		   $htmlDiv = htmlBase::newElement('div')
			->html($pageContents);
		   echo $htmlForm->append($htmlDiv)->draw();
	   }
